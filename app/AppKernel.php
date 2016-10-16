<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    
    public function init()
    {
        // get rid of Warning: date_default_timezone_get(): It is not safe to rely on the system's timezone
        //Default time for application Europe/Paris
        date_default_timezone_set( 'Europe/Paris' );
        parent::init();
    }
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new AppBundle\AppBundle(),
            new FOS\UserBundle\FOSUserBundle(), //bundle FOSUser
            new JavierEguiluz\Bundle\EasyAdminBundle\EasyAdminBundle(),//admin de l'application
            new EENE\UserBundle\UserBundle(),  //gestion des utilisateurs : login; register; reset ; profil
            new EENE\ExtractionBundle\ExtractionBundle(), //bundle extraction des entities
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(), //pour generer les routes en javascript,
            new EENE\VisualizationBundle\VisualizationBundle(), // module de visualisation
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
