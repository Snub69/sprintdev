<?php

namespace AuthBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Controller\AbstractAppController;


class LeftController extends AbstractAppController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @Route("/left", name="left")
     */
    public function leftAction()
    {
        if ($this->hasGlobalAccess()) {
            $this->session->invalidate();
        }
        return $this->redirectToHomepage();
    }
}
