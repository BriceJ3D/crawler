<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Research;
use App\Entity\Domain;
use App\Entity\Credits;




class researchController extends Controller
{
    

    /**
    * @Route("/search", name="search_index")
    * Affichage de la liste des recherches et de leurs caracteristiques
    */
    public function search_index(){
        $entityManager = $this->getDoctrine()->getManager();
        $researches = $this->getDoctrine()
                ->getRepository(Research::class)
                ->findBy(array(), array('endDate' => 'DESC'));
        $credits = $this->getDoctrine()
                ->getRepository(Credits::class)
                ->findOneById(1);
        return $this->render('researches.html.twig',array(
            'researches' => $researches,
            'credits' => $credits
        ));
    }

     /**
    * @Route("/search/{id}", name="search_show")
    * Affichage d'une recherche et de ses resultats
    */
    public function search_show($id){
        $entityManager = $this->getDoctrine()->getManager();
        $research = $this->getDoctrine()
                ->getRepository(Research::class)
                ->findOneById($id);
        $credits = $this->getDoctrine()
                ->getRepository(Credits::class)
                ->findOneById(1);
        return $this->render('search_show.html.twig',array(
            'research' => $research,
            'credits' => $credits
        ));
    }
}