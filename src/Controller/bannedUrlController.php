<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Credits;
use App\Entity\BannedUrl;

class bannedUrlController extends Controller
{
	/**
    * @Route("/banned_urls", name="banned_index")
    */
    public function banned_index(Request $request){
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, array('label' => 'Url'))
            ->add('save', SubmitType::class, array('label' => 'Ajouter l\'url'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        // $form->getData() holds the submitted values
        // but, the original `$task` variable has also been updated
            $data = $form->getData();
            $result = $this->getDoctrine()
                ->getRepository(BannedUrl::class)
                ->findOneByName($data['name']);
            if (!$result){
                $bannedUrl = new BannedUrl($data['name']);
                $entityManager->persist($bannedUrl);
                $entityManager->flush();
            }            
        }
        $bannedUrls = $this->getDoctrine()
                ->getRepository(BannedUrl::class)
                ->findBy([], ['name' => 'ASC']);
        $credits = $this->getDoctrine()
                ->getRepository(Credits::class)
                ->findOneById(1);
        return $this->render('banned.html.twig',array(
            'banned' => $bannedUrls,
            'form' => $form->createView(),
            'credits' => $credits
        ));
    } 
}