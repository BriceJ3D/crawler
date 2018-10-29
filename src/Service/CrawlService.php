<?php
namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\CrawlLogger;
use App\Entity\Domain;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlAllUrls;
use Spatie\Crawler\CrawlInternalUrls;
use Iodev\Whois\Whois;
use App\Entity\Url;
use App\Entity\Research;
use App\Entity\Credits;
use App\Entity\Tag;
use App\Entity\TopicalTrustFlow;
use App\Entity\BannedUrl;
use App\Service\MessageGenerator;
use Doctrine\ORM\EntityManagerInterface;



class CrawlService
{
    //nb d'url crawl au maximum
    const MAXCRAWL = 500;
    //nb d'url crawl en parallèle (augmenter peut provoquer un arrete prématuré)
    const CONCURRENCY = 30;
    
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function test(){
        print_r('test');
    }

    //gestion du crawl, initialisation des urls, tags et dates de recherche
    public function crawlIndex(String $urls, String $tags, $cleanSearch, $oldSearch)
    {
        //$this->log->info("test" );
        $urls = str_replace(PHP_EOL, ',',$urls);
        $urls = explode(',',$urls);
        $tags = str_replace(PHP_EOL, ',',$tags);
        $tags = explode(',',$tags);
        $entityManager = $this->em;
        //compteur d'url crawlées
        $allCount = 0;
        //compteur d'urls disponibles
        $availableCount = 0;

        $search = new Research();
        $search->setSearchDate(new \DateTime(date('Y-m-d H:i:s')));
        $bannedUrls = $this->em
                ->getRepository(BannedUrl::class)
                ->findAll();

        // gestion des tags de la recherche
        foreach($tags as $crawledTag){
            $crawledTag = trim($crawledTag);
            $result = $this->em
                ->getRepository(Tag::class)
                ->findOneByName($crawledTag);
            if (!$result){
                $tag = new Tag(ucwords($crawledTag));
            } else {
                $tag = $result;
            }
            $search->addTag($tag);
            $entityManager->persist($tag);
        }

        //gestion des urls
        foreach($urls as $crawledUrl){
            $crawledUrl = trim($crawledUrl);
            $crawledDomain = trim($crawledUrl);

            if (!preg_match('/(^https?:\/\/|^www.)/',$crawledUrl)){
                $crawledUrl = 'http://www.'.$crawledUrl;
            }
            if (!preg_match('/^https?:\/\//',$crawledUrl)){
                $crawledUrl = 'http://'.$crawledUrl;
            }
            $matches = false;
            //vérification des urls bannies
            foreach ($bannedUrls as $pattern){
                if (preg_match('/'.$pattern.'/', $crawledDomain))
                {
                    $matches = true;
                } 
            }
            //si l'url n'est pas bannie ou qu'on veut crawl les Url bannies
            if (!$matches or !$cleanSearch){
                $result = $this->em
                    ->getRepository(Url::class)
                    ->findOneByAddress($crawledUrl);
                //si l'url n'existe pas en base on en crée une sinon on recupere l'url existante pour la lier à la recherche
                if (!$result){
                    $url = new Url($crawledUrl);
                } else{
                    $url = $result;
                }
                //si l'url n'existe pas en base ou qu'on recherche les anciennes urls, on crawl
                if (!$result or $oldSearch){ 
                    echo "<br>Crawl de $crawledUrl<br>" ;
                    $crawlStats = $this->crawl($url);
                    $allCount = $allCount + $crawlStats['allCount'];
                    $availableCount = $availableCount + $crawlStats['availableCount'];
                    $search->addUrl($url);
                } else {
                    echo "$crawledUrl : deja crawl récemment<br>";
                }
                $entityManager->persist($url);
            } else {
                echo "$crawledUrl : bannie";
            }
        }       
        $search->setCrawledUrls($allCount);
        $search->setAvailableDomains($availableCount);
        $search->setEndDate(new \DateTime(date('Y-m-d H:i:s')));
        return ($search);
    }

     //gestion du crawl, initialisation des urls, tags et dates de recherche
    public function crawlIndex2($searchId)
    {
       echo "debut";
        $entityManager = $this->em;
        $search = $this->em
                ->getRepository(Research::class)
                ->findOneById($searchId);
        //compteur d'url crawlées
        $allCount = $search->getCrawledUrls();
        //compteur d'urls disponibles
        $availableCount = $search->getAvailableDomains();

      
        //gestion des urls
        foreach($search->getUrls() as $url){
           
            //si l'url n'existe pas en base ou qu'on recherche les anciennes urls, on crawl
            if (!$url->getCrawled()){ 
               // echo "<br>Crawl de $url<br>" ;
                $crawlStats = $this->crawl($url);
                $search->setCrawledUrls($search->getCrawledUrls() + $crawlStats['allCount']) ;
                $search->setAvailableDomains($search->getAvailableDomains() + $crawlStats['availableCount']);
            } else {
               // echo "$url : deja crawl récemment<br>";
            }
            $url->setCrawled(true);
            $entityManager->persist($url);
            $entityManager->flush();
      
        }       
        return ($search);
    }

    /*****************/
    // réalisation du crawl d'une url
    /*****************/
    public function crawl(Url $url)
    {
        //compteur d'url dispo
        $availableCount = 0;
        //tableau de domaines sans reponses
        $noResponseDomain = array();
        $entityManager = $this->em;
        $crawlLogger = new CrawlLogger();

        $result = Crawler::create()
            ->setCrawlObserver($crawlLogger)
            ->setCrawlProfile(new CrawlAllUrls)
            ->setMaximumCrawlCount(self::MAXCRAWL)
            ->setConcurrency(self::CONCURRENCY)
            ->startCrawling($url->getAddress());
        //echo 'Total ' . $result['allCount'] . '- Externes' . $result['externalCount'];
        //pour chaque domaine externe on recupere et on set la disponibilité
        foreach($result['externalLinks'] as $domain){
            $domainExist = $this->em
                ->getRepository(Domain::class)
                ->findOneByDomain_name($domain->getDomainName());
            //si le domaine existe deja on le met a jour
            if ($domainExist) {
                $domainExist->setDispo($domain->getDispo());
                $domain = $domainExist;
            }

            $domain->addUrl($url);
            //$now = new \DateTime(date('Y-m-d H:i:s'));
            // On recupere la disponibilité
            //echo 'traitement disponibilité : ' . $domain->getDomainName() . '<br>';
            if ($domain->getDispo() == 'available'){

                $request = 'https://developer.majestic.com/api/json?app_api_key='.getenv('API_MAJESTIC_KEY').'&cmd=GetIndexItemInfo&items=1&item0='.$domain->getDomainName().'&datasource=fresh';
                
            /**** a tester avec l'API prod pour voir si il y'a des differences ***/
                $request = 'https://developer.majestic.com/api/json?app_api_key='.getenv('API_MAJESTIC_KEY').'&cmd=GetIndexItemInfo&items=1&item0='.$domain->getDomainName().'&datasource=fresh';
            /****/
                $response = file_get_contents($request);
                $response = json_decode ($response);
                $topicalTrustFlows = $domain->getTopicalTrustFlows();
                foreach($topicalTrustFlows as $topicalTrustFlow){
                    $domain->removeTopicalTrustFlow($topicalTrustFlow);
                }

                $credits = $this->em
                ->getRepository(Credits::class)
                ->findOneById(1);
                //dump($response->DataTables->Results->Data[0]);
                $credits->setActual($credits->getActual()-1);
                $entityManager->persist($credits);

                $domain->setTrustFlow($response->DataTables->Results->Data[0]->TrustFlow);
                $domain->setTrustMetrics($response->DataTables->Results->Data[0]->TrustMetric);
                $domain->setRefIP($response->DataTables->Results->Data[0]->RefIPs);
                $domain->setTitle($response->DataTables->Results->Data[0]->Title);
                $domain->setLanguage($response->DataTables->Results->Data[0]->Language);
                $domain->setLastCrawledDate(new \DateTime($response->DataTables->Results->Data[0]->LastCrawlDate));
                for ($i = 0; $i <3; $i++){
                    $topic = 'TopicalTrustFlow_Topic_'.$i;
                    $value = 'TopicalTrustFlow_Value_'.$i;
                    
                    if ($response->DataTables->Results->Data[0]->$topic != '' ){
                        $topicalTrustFlow = new TopicalTrustFlow($response->DataTables->Results->Data[0]->$topic, $response->DataTables->Results->Data[0]->$value);
                        $domain->addTopicalTrustFlow($topicalTrustFlow);
                    }
                }
                $domain->setDispo('Disponible'); 
                $availableCount ++;
            } else {
                $domain->setDispo('Non dispo'); 
            }
        
            if (preg_match('/^((?!-)[A-Za-z0-9-]{1,63}(?<!-)\.)+[A-Za-z]{2,6}$/',$domain->getDomainName())){
                $domain->setCreationDate(new \DateTime(date('Y-m-d H:i:s')));
                $entityManager->persist($domain);
            }
        }
        $entityManager->flush();
        //echo ' Dispo ' .$availableCount .'<br>'; 
        return array('allCount'=>$result['allCount'],
            'externalCount'=>$result['externalCount'],
            'availableCount'=>$availableCount);
    }
}