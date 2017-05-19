<?php

namespace SprintBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SprintController extends AbstractSprintController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @Route("/sprint", name="sprint")
     */
    public function sprintAction()
    {        
        if(!$this->hasGlobalAccess()) {
            return $this->redirectToHomepage();
        } else if(!$this->hasSprintAccess()) {
            return $this->redirectToCreate();
        }
        
        $sprint=$this->readSprint();
        $lapsed=(time()-$sprint->getTime());
        $duration=$sprint->getDay()*86400;
        
        $percent= round($lapsed / $duration * 100, 2);        

        return $this->render('@SprintBundle/Resources/views/sprint.html.twig', [
            "goal" => $sprint->getGoal(),
            "description" => $sprint->getDescription(),
            "pourcentage" => $percent,
            "day" => $sprint->getDay(),
            "master_access"=>$this->hasScrumMasterAccess()
        ]);
    }
}
