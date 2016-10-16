<?php

namespace EENE\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class HelpController extends Controller
{
    //page d'accueil de Help
    public function helpAction()
     {

         return $this->render('UserBundle:Help:index.html.twig');
    }
    
    //consulter un video Démo selon le nom de video
    public function consultAction($videoname)
     {

         return $this->render('UserBundle:Help:video_consult.html.twig',
         array(
            'videoname' =>$videoname,
        ));
    }
    
    
    
}
