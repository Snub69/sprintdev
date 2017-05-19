<?php

namespace AuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AuthBundle\Entity\User;
use Throwable;


class JoinController extends AbstractAuthController
{
    /**
     * @Route("/join", name="join")
     * @param Request $request
     */
    public function joinAction(Request $request)
    {
        if($this->hasGlobalAccess()) {
            return $this->redirectToHomepage();
        }
        $form=$this->getAuthAndjoinForm("Create an account");
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            if (!$this->readUser($form)) {
                   $user=new User();
                   $user->setEmail($form->getData()["email"]);
                   $user->setPassword(password_hash($form->getData()
                       ["password"], PASSWORD_DEFAULT));
                   
                   //insertion dans la base
                   try {
                   $this->getDoctrine()->getManager()->persist($user);
                   $this->getDoctrine()->getManager()->flush();
                   } catch (\Throwable $e) {
                       $message="An error has occured";
                   }
                   
                   $this->setGlobalAccess($user);
                   return $this->redirectToHomepage();
                   
                   
               }
                   $message="Account already exist";
           
        }
        return $this->render('@AuthBundle/Resources/views/join.html.twig', [
            "title"=>"Sign up",
            "form"=>$form->createView(),
            "message"=>isset ($message) ? $message:""
        ]
            
            );
    }
}
