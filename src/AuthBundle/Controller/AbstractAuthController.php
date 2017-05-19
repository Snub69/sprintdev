<?php


namespace AuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use AuthBundle\Entity\User;
use Symfony\Component\Form\Form;
use AppBundle\Controller\AbstractAppController;


abstract class AbstractAuthController extends AbstractAppController
{
    

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 
     * @param Form $form
     * @return object
     */
    protected function readUser (Form $form)
    {
        return $this->getDoctrine()->getManager()
        ->getRepository(User::class)->findOneBy([
            "email"=>$form->getData()["email"]
        ]);
    }

    /**
     * 
     * @param unknown $submitLabel
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function getAuthAndJoinForm($submitLabel)
    {
        $builder=$this->createFormBuilder();
        $builder->add("email",
            EmailType::class, [
                "label"=>"Email adress",
                "attr" => [
                    "class" => "col-xs-12"
                ],
                "constraints" => [
                    new Email([
                        "message"=>"Incorrect email adress"
                    ]),
                    new NotBlank([
                        "message"=>"Enter a valid email adress"
                    ])
                ]
            ]);
        
        $builder->add("password",
            TextType::class, [
                "label"=>"Password",
                "attr" => [
                    "class" => "col-xs-12"
                ],
                "constraints" => [
                    new Regex([
                        "pattern"=>"/^[\w]{6,32}$/",
                        "message"=>"Incorrect Password"
                    ]),
                    new NotBlank([
                        "message"=>"Enter a valid Password"
                    ])
                ]
            ]);
        
        $builder->add("create", SubmitType::class, [
            "label"=>$submitLabel,
            "attr" => [
                "class" => "btn btn-primary col-xs-12 blocksubmit"
            ],
        ]);
        
        return $builder->getForm();
    }
}
