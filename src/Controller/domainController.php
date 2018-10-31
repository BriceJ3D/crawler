<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\CrawlLogger;
use App\Entity\Domain;
use App\Entity\Url;
use App\Entity\Research;
use App\Entity\Tag;
use App\Entity\Credits;


class domainController extends Controller
{
	/**
    * @Route("/domains", name="domain_index")
    * Page de listing des domaines
    */
    public function domain_index(Request $request){
        $entityManager = $this->getDoctrine()->getManager();

//formulaire de recherche par critère
        $form = $this->createFormBuilder()
            ->add('TF', NumberType::class, array('label' => 'TF superieur à :',
                                                    'required' => false
                                                ))
            ->add('TM', NumberType::class, array('label' => 'TM superieur à :',
                                                    'required' => false
                                                ))
            ->add('RefIp',  NumberType::class, array('label' => 'Ref Ip superieures à :',
                                                    'required' => false
                                                ))
            ->add('save', SubmitType::class, array('label' => 'Valider'))
            ->getForm();
//initialisation domaines avec stats de base
        $domains = $this->getDoctrine()
                ->getRepository(Domain::class)
                ->findDispoByCritere (10,0,0);
//initialisation domaines avec stats formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $tf = ($data['TF']) ? $data['TF']:0;
            $tm = ($data['TM']) ? $data['TM']:0;
            $refIp = ($data['RefIp']) ? $data['RefIp']:0;
            $domains = $this->getDoctrine()
                ->getRepository(Domain::class)
                ->findDispoByCritere ($refIp,$tf,$tm);
        }

        $credits = $this->getDoctrine()
                ->getRepository(Credits::class)
                ->findOneById(1);
     
        return $this->render('domains.html.twig',array(
            'domains' => $domains,
            'credits' => $credits,
            'form'=>$form->createView(),
        ));
    } 
}