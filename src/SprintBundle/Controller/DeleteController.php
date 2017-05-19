<?php

namespace SprintBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DeleteController extends AbstractSprintController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @Route ("/delete", name="delete")
     */
    public function deleteAction()
    {
        if(!$this->hasGlobalAccess()) {
            return $this->redirectToHomepage();
        } else if(!$this->hasSprintAccess()
            || !$this->hasScrumMasterAccess()) {
            return $this->redirectToSprint();
            }
        
        //recupere le sprint
        $sprint=$this->readSprint();
        
        //recuperer les données de la table (via Doctrine)
        $users= $this
        ->getDoctrine()
        ->getManager()
        ->getRepository(\AuthBundle\Entity\User::class)
        ->findBy(["sprint"=>$this->getSprintAccess()]);
        
        //nettoyer la colonne sprint des utilisateurs
        foreach ($users as $user) {
            $user->setSprint(null);
            $this->getDoctrine()->getManager()->flush();
        }
        
        //Remove le sprint
        $this->getDoctrine()->getManager()->remove($sprint);
        $this->getDoctrine()->getManager()->flush();
        
        //il faut revoquer les droits
        $this->session->remove("sprint");
        $this->session->remove("master");
        return $this->redirectToCreate();
    }
}
