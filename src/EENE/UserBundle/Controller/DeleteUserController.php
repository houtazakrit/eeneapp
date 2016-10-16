<?php

namespace EENE\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DeleteUserController extends Controller
{


public function deleteUserAction() {
     /* user Manager de fos user 
     * la méthode de deleteUser utilisé en dessous est implementé dans
     * le fichier personalisé de FOSUser EENE/UserBunble/Doctrine/UserManager.php
     */
       $userManager = $this->get('fos_user.user_manager');
      $user=$this->getUser();
      //supprimer le dossier de l'utilisateur
      $user->deleteUploadUserDir();
      //vider la session
       $this->get('session')->clear();
       //supprimer l'utilisateur avec ses fichiers de BD 
        $userManager->deleteUser($user);
        
     $url = $this->container->get('router')->generate("user_login");
    return new RedirectResponse($url);

  }

}