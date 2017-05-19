<?php

namespace AuthBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class AuthController extends AbstractAuthController
{
    
    const
    /**
     * @var string error message for authentification
     */
        ERROR_MESSAGE_AUTH="Incorrect email adress password";
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @Route("/auth", name="auth")
     * @param Request $request
     */
    public function authAction(Request $request)
    {

        if($this->hasGlobalAccess()) {
            return $this->redirectToHomepage();
        }
        $form=$this->getAuthAndjoinForm("Sign in");
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user=$this->readUser($form);
            
           if($user && password_verify(
               $form->getData() ["password"],
               $user->getPassword()
               )) {
                   $this->setGlobalAccess($user);
                   return $this->redirectToHomepage();
           }
           $message = self::ERROR_MESSAGE_AUTH;;
        }
        
        return $this->render('@AuthBundle/Resources/views/auth.html.twig', [
            "title"=>"Sign in",
            "form"=>$form->createView(),
            "message" => isset($message) ? $message : "",
        ]);
    }
}
