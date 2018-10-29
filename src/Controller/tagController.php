<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Research;
use App\Entity\Domain;
use App\Entity\Tag;
use App\Entity\Credits;




class tagController extends Controller
{
    

    /**
    * @Route("/tag", name="tag_index")
    */
    public function tag_index(){
        $entityManager = $this->getDoctrine()->getManager();
        $tags = $this->getDoctrine()
                ->getRepository(Tag::class)
                ->findAll();
        $credits = $this->getDoctrine()
                ->getRepository(Credits::class)
                ->findOneById(1);
        return $this->render('tags.html.twig',array(
            'tags' => $tags,
            'credits' => $credits
        ));
    }

     /**
    * @Route("/tag/{id}", name="tag_show")
    */
    public function tag_show($id){
        $entityManager = $this->getDoctrine()->getManager();
        $tag = $this->getDoctrine()
                ->getRepository(Tag::class)
                ->findOneById($id);
        $credits = $this->getDoctrine()
                ->getRepository(Credits::class)
                ->findOneById(1);
        return $this->render('tag_show.html.twig',array(
            'tag' => $tag,
            'credits' => $credits
        ));
    }
}