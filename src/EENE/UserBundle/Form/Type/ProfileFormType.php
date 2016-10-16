<?php

namespace EENE\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('lastname', 'text',array('required' => false))
         ->add('firstname', 'text',array('required' => false))
         ->add('organization', 'text',array('required' => false))
         ->add('country', 'text',array('required' => false)) 
         ;
         //supprimer le champs username pour qu'il ne soit pas modifié par l'utilisateur
         $builder->remove('username'); 
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';

    }

    public function getBlockPrefix()
    {
        return 'eene_user_profile';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}