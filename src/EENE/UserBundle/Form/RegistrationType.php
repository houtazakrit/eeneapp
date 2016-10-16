<?php

namespace EENE\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use FOS\UserBundle\Util\LegacyFormHelper;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
         ->add('email', 'text')
       ->add('username', 'text', array('required' => true,
       ))
         ->add('lastname', 'text',array('required' => false))
         ->add('firstname', 'text',array('required' => true))
         ->add('organization', 'text',array('required' => false))
         ->add('country', 'text',array('required' => false))
              
        
       ->add('plainPassword', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\RepeatedType'), array(
                'type' => LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'),
                 'invalid_message' => 'Passwords don\'t match',
            ));
            
    }
//invalid_message
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

    }

    public function getBlockPrefix()
    {
        return 'eene_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}