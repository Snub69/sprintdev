<?php

namespace SprintBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use SprintBundle\Entity\Sprint;


class CreateController extends AbstractSprintController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @Route("/create", name="create")
     */
    public function createAction(Request $request)
    {
        if(!$this->hasGlobalAccess()) {
            return $this->redirectToHomepage();
        } else if($this->hasSprintAccess()) {
            return $this->redirectToSprint();
        }else {
            $sprint=$this->readUserSprint();
            if ($sprint) {
                $this->setSprintAccess($sprint);
                $this->setScrumMasterAccess($sprint);
                return $this->redirectToSprint();
            }
        }
        $form=$this->getCreateForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sprint=$this->createSprint($form);
            $this->setSprintAccess($sprint);
            $this->setScrumMasterAccess($sprint);
            return $this->redirectToSprint();
        }
        
        return $this->render('@SprintBundle/Resources/views/create.html.twig', [
            "form"=>$form->createView(),
        ]);
    }
    
    private function getCreateForm ()
    {
        $builder=$this->createFormBuilder();
        $builder->add("goal",
            TextType::class, [
                "label"=>"Goal",
                "constraints" => [
                    new Regex ([
                        "pattern"=>"/^[\w]{6,32}$/",
                        "message"=>"Incorrect goal"
                    ]),
                    new NotBlank([
                        "message"=>"Goal is incorrect"
                    ])
                ]
            ]);
        
        $builder->add("description",
            TextareaType::class, [
                "label"=>"Description",
                "constraints" => [
                    new Regex([
                        "pattern"=>"/^[\w]{5,255}$/",
                        "message"=>"Incorrect Description"
                    ]),
                    new NotBlank([
                        "message"=>"Incorrect Description"
                    ])
                ]
            ]);
        
        $builder->add("days",
            TextType::class, [
                "label"=>"Days",
                "constraints" => [
                    new Regex([
                        "pattern"=>"/^[\w]{1,2}$/",
                        "message"=>"Incorrect duration"
                    ]),
                    new NotBlank([
                        "message"=>"Incorrect duration"
                    ])
                ]
            ]);
        
        $builder->add("create", SubmitType::class, [
            "label"=>"Create sprint"
        ]);
        
        return $builder->getForm();
    }
    
    private function createSprint(Form $form) : Sprint
    {
        $sprint=new Sprint();
        $sprint->setGoal($form->getData()["goal"]);
        $sprint->setDescription($form->getData()["description"]);
        $sprint->setDay($form->getData()["days"]);
        $sprint->setTime(time());
        $user=$this->getDoctrine()->getManager()
        ->getRepository(\AuthBundle\Entity\User::class)
        ->findOneBy(["id"=>$this->getGlobalAccess()]);
        $sprint->setUser($user);
        $this->getDoctrine()->getManager()->persist($sprint);
        $this->getDoctrine()->getManager()->flush();
        
        $user->setSprint($sprint);
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();
        return $sprint;
    }

}
