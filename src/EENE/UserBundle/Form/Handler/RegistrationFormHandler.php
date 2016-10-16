<?php

namespace EENE\UserBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
use FOS\UserBundle\Model\UserInterface;

class RegistrationFormHandler extends BaseHandler
{
    protected function onSuccess(UserInterface $user, $confirmation)
    {
         /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        // I need an instance of Entity Manager but I don't know where get it!
  
        $user = $userManager->createUser();
  
       
        parent::onSuccess($user, $confirmation);
    }
}