<?php
namespace App\Entity;

use Spatie\Crawler\CrawlObserver;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use App\Entity\Domain;
use Doctrine\ORM\EntityRepository;
use Iodev\Whois\Whois;
use Psr\Log\LoggerInterface;
use XML_RPC2_Client;

/************************/
// Cette classe permet d'utiliser le crawler et implemente les actions a réaliser lors de chaque etape du crawl
/************************/
class CrawlLogger extends CrawlObserver
{
    public $internalCount;

    public $externalCount;

    public $externalList = array();
    public $start;

    public function __construct(){
        $this->internalCount = 0;
        $this->externalCount = 0;
        $this->start = new \DateTime(date('Y-m-d H:i:s'));
    }

    /******************/
    // Lancé au début du crawl d'une url
    /******************/
    public function willCrawl(UriInterface $url)
    {
    }


    /******************/
    // Pour chaque Url crawlé
    /******************/
    public function crawled(UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null){
        $domain = $this->getDomain($url);
        
        if ( isset($foundOnUrl)) {
            $foundOnDomain = $this->getDomain($foundOnUrl);
            $this->isExternal($domain,$foundOnDomain,$url,$response->getStatusCode());
        }
    }
    /******************/
    // Pour chaque erreur de crawl
    /******************/
    public function crawlFailed(UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null){
        $domain = $this->getDomain($url);
      
        if ( isset($foundOnUrl)) {
            $foundOnDomain = $this->getDomain($foundOnUrl);
            $this->isExternal($domain,$foundOnDomain,$url,0);
        }
    }

    /******************/
    // A la fin du crawl general
    /******************/
    public function finishedCrawling()
    {
        $now = new \DateTime(date('Y-m-d H:i:s'));
        
        echo 'Compte rendu ' . $this->start->format('H:i:s') .' - ' . $now->format('H:i:s'). '<br>';
        $externalLinks = array_unique($this->externalList, SORT_REGULAR);
        foreach($externalLinks as $domain){
            if ($domain->getDispo() == '0') {
                $now = new \DateTime(date('Y-m-d H:i:s'));
                //echo 'Bulk ' . $now->format('H:i:s') . '-';
                $dispo = $this->apiBulk($domain->getDomainName());
                $domain->setDispo($dispo[$domain->getDomainName()]);
                $now = new \DateTime(date('Y-m-d H:i:s'));
                //echo $now->format('H:i:s') .'<br>';
            }
        }
        $result = array('allCount'=>$this->internalCount+$this->externalCount,
            'externalCount'=>count($externalLinks),
            'externalLinks'=>$externalLinks);
        return $result;
    }

    /**********************/
    // Verification si l'url fournie est externe
    /**********************/
    public function isExternal($domain,$foundOnUrl,$url,$statut)
    {
        if ($domain != $foundOnUrl){
            if (preg_match('/^((?!-)[-a-z0-9]{1,63}(?<!-)\.)+(?=.{1,63}$)(?!-)([-a-z0-9]*[a-z][-a-z0-9]*\.?)(?<!-)$/', $domain)) {
                $this->externalCount ++;
                $domain = new Domain($domain);       
                
                $domain->setDispo($statut);

                array_push($this->externalList, $domain);
            }
        } else {
            $this->internalCount ++;
        }
    }

    /**********************/
    // Verification de la disponibilité du domaine
    /**********************/
    public function apiBulk($domain)
    {
        
        $apikey=getenv('API_GANDI_KEY'); 

        $domain_api = XML_RPC2_Client::create(
            getenv('API_GANDI_CALL'), //modifier l'appel PROD/DEV dans le fichier .env
            array( 'prefix' => 'domain.' )
        );

        $result = $domain_api->available($apikey, array($domain));
        while ( $result[$domain] == 'pending') {
            usleep(700000);
            $result = $domain_api->available($apikey, array($domain));
        }

        $now = new \DateTime(date('Y-m-d H:i:s'));
        return ($result);
    }

    public function getDomain($url){
        preg_match('/^(?:https?:\/\/)?(?:[^@\/\n]+@)?(?:www\.)?([^:\/\n]+)/im', $url, $result);
        return $result[1];
    }
}