<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Credits;
use App\Entity\City;

class cityController extends Controller
{
	/**
    * @Route("/cities", name="city_index")
    */
    public function banned_index(Request $request){
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, array('label' => 'Ville'))
            ->add('save', SubmitType::class, array('label' => 'Ajouter la ville'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        // $form->getData() holds the submitted values
        // but, the original `$task` variable has also been updated
            $data = $form->getData();
            $result = $this->getDoctrine()
                ->getRepository(City::class)
                ->findOneByName($data['name']);
            if (!$result){
                $bannedUrl = new City($data['name']);
                $entityManager->persist($bannedUrl);
                $entityManager->flush();
            }            
        }
        $cities = $this->getDoctrine()
                ->getRepository(City::class)
                ->findBy([], ['name' => 'ASC']);
        $credits = $this->getDoctrine()
                ->getRepository(Credits::class)
                ->findOneById(1);
        return $this->render('cities.html.twig',array(
            'cities' => $cities,
            'form' => $form->createView(),
            'credits' => $credits
        ));
    } 
}