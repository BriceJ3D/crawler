<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\HttpFoundation\Request;
use App\Service\CrawlService;
use App\Service\MessageGenerator;
use App\Entity\Research;
use App\Entity\Domain;
use App\Entity\BannedUrl;
use App\Entity\City;
use App\Entity\Url;
use App\Entity\Tag;
use App\Entity\Credits;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\KernelInterface;
use Omines\DataTablesBundle\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Controller\DataTablesTrait;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;



class mainController extends Controller
{
    use DataTablesTrait;
	
    /**
    * @Route("/", name="indexcrawl")
    * Page d'accueil, recherche et tags
    */
    public function indexCrawl(Request $request, CrawlService $crawlService, LoggerInterface $logger){
        $recupOK = null;
        $entityManager = $this->getDoctrine()->getManager();
        $urls = [];
        $tags = [];

        // Gestion des Tags
        $form_tags = $this->get('form.factory')->createNamedBuilder('tags')
            ->add('keywords', TextareaType::class, array(
                    'label' =>'Mots-clés',
                    'required' => false
                ))

            ->add('city', ButtonType::class, array('label'=>'Ajouter les villes', 'attr' => array('class' => 'btn-sm btn-primary')))
            ->add('getUrl', SubmitType::class, array('label' => 'Récuperer les url', 'attr' => array('class' => 'btn-sm btn-primary')))
            ->getForm();
        $form_tags->handleRequest($request);
        if ($form_tags->isSubmitted() && $form_tags->isValid()) {
            $data = $form_tags->getData();
            $tags = explode("\n", $data['keywords']);
            $recupOK = true;
            foreach ($tags as $tag){
                $tag = str_replace(' ', '%20', $tag);
                /** a commenter si les accents sont pris en compte avec l'API prod **/
                $tag = htmlentities($tag, ENT_NOQUOTES, 'utf-8');
                $tag = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $tag);
                /*******/

                $apiRequest ="https://developer.majestic.com/api/json?app_api_key=".getenv('API_MAJESTIC_KEY')."&cmd=SearchByKeyword&query=$tag&scope=0&count=50";
                
                $response = file_get_contents($apiRequest);
                $response = json_decode ($response);
                
                if($response->Code != 'OK'){
                    $recupOK = false;
                }
                foreach($response->DataTables->Results->Data as $url){
                    array_push($urls, $url->Item);
                }
                
            }     
        }
        
        //Gestion des urls
        $urls = array_unique($urls);

        $form_crawl = $this->createFormBuilder()
            ->add('clean', ChoiceType::class, array('label' => 'Recherche propre',
                                                    'expanded' => true,
                                                    'multiple' => true,  
                                                    'choices'  => array(
                                                        'Bannir les domaines connus' => 'clean',
                                                        'Recrawler les urls déjà crawl' => 'recrawl'
                                                    ),
                                                    'required' => false
                                                )
                )
            ->add('urls', TextareaType::class, array('label' => 'Urls', 'data' => implode(PHP_EOL, $urls),
                'required' => true))
            ->add('tags', TextType::class, array('label' => 'Tags (separés par une virgule)', 'data' => implode(', ', $tags),
                'required' => false))
            ->add('save', SubmitType::class, array('label' => 'Envoyer les url', 'attr' => array('class' => 'btn-sm btn-primary')))
            ->getForm();


        $form_crawl->handleRequest($request);

        if ($form_crawl->isSubmitted() && $form_crawl->isValid()) {
            $count = 0;
            $search = new Research();
            $search->setSearchDate(new \DateTime(date('Y-m-d H:i:s')));
            $data = $form_crawl->getData();
            if ($data['tags'] != ''){
                $tags = explode(',',$data['tags']);
                foreach($tags as $tag){
                    $result = $this->getDoctrine()
                            ->getRepository(Tag::class)
                            ->findOneByName($tag);
                    //si le tag n'existe pas en base on en crée un sinon on recupere le tag existant pour le lier à la recherche
                    if (!$result){
                        $tag = new Tag($tag);
                    } else{
                        $tag = $result;
                    }
                    $search->addTag($tag);       
                    $entityManager->persist($tag);
                }
            }
            if ($data['urls'] != ''){
                //on active ou non les recherches spéciales
                $cleanSearch = false;
                $oldSearch = false;
                foreach ($data['clean'] as $value){
                    if ($value == 'clean'){
                        $cleanSearch = true;
                    }
                    if ($value == 'recrawl'){
                        $oldSearch = true;
                    }
                }
                //et on enregistre la recherche
                
                $urls = str_replace(PHP_EOL, ',',$data['urls']);
                $urls = explode(',',$urls);
                try {
                    
                    $bannedUrls = $this->getDoctrine()
                        ->getRepository(BannedUrl::class)
                        ->findAll();
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
                            $result = $this->getDoctrine()
                                ->getRepository(Url::class)
                                ->findOneByAddress($crawledUrl);
                            //si l'url n'existe pas en base on en crée une sinon on recupere l'url existante pour la lier à la recherche
                            if (!$result){
                                $url = new Url($crawledUrl);
                            } else{
                                $url = $result;
                                if ($oldSearch){
                                    $url->setCrawled(0);
                                }
                            }
                            
                            $search->addUrl($url);
                            $entityManager->persist($url);
                        } else {
                            echo "$crawledUrl : bannie";
                        }
                    }       
                    $entityManager->persist($search);
                    $entityManager->flush();
                    return $this->redirectToRoute('search_index');
                    
                }  catch (Exception $e) {
                    echo 'Exception reçue : ',  $e->getMessage(), "\n";
                }     
            }
        }

        $bannedUrls = $this->getDoctrine()
            ->getRepository(BannedUrl::class)
            ->findAll();

        $cities = $this->getDoctrine()
            ->getRepository(City::class)
            ->findAll();
        $credits = $this->getDoctrine()
            ->getRepository(Credits::class)
            ->findOneById(1);
        return $this->render('index.html.twig',array(
            'form_crawl' => $form_crawl->createView(),
            'form_tags' => $form_tags->createView(),
            'bannedUrls' => $bannedUrls,
            'cities' => $cities,
            'recupOK' => $recupOK,
            'credits' => $credits,
        ));
   } 

    /**
    * @Route("/research_crawl/{id}", name="researchcrawl")
    * Page appellée en ajax permettant le lancement d'une recherche
    */
    public function researchCrawl(Research $search, KernelInterface $kernel){
      
        // on passe par une commande console pour pouvoir utiliser les threads
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput(array(
           'command' => "app:crawl",
           'id' => $search->getId(),
        ));
        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();
        //system('script.bat -h');
        
        return new Response('ok');
    }
}