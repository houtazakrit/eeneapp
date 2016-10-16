<?php

namespace EENE\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ContactController extends Controller
{
    
    public function contactAction()
     {

         return $this->render('UserBundle:Contact:contact.html.twig');
    }
    
   public function contributorAction()
     {

         return $this->render('UserBundle:Contact:contributor.html.twig');
    }
    
}
