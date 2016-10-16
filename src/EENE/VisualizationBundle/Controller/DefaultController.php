<?php
namespace EENE\VisualizationBundle\Controller;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EENE\ExtractionBundle\Entity\NerdFile;
use EENE\ExtractionBundle\Controller\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EENE\ExtractionBundle\Form\Type\FileFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use EENE\ExtractionBundle\Uploader\Response\EmptyResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;


class DefaultController extends Controller 
{

 //rendre la page d'accueil de module de visualisation  Default/index.html.twig 
  
   public function getContentVisualizationAction($idjson)
    {   
        
       ///appelle l'utilisateur connecté
          $user=$this->getUser();
          $request = $this->container->get('request');

       //appelle l'utilisateur connecté
           $user=$this->getUser();
           $em = $this->getDoctrine()->getManager();
             //variable globale
              global $kernel;
              //contenu de kernel
              $container = $kernel->getContainer();
             //Récupérer les variables globales de block twig du fichier app/config.yml
             $globals = $container->get('twig')->getGlobals();
             //récuperer les couleurs de configuration de l'application (le fichier app/config.yml)
           //mettre les couleurs de fichier config.yml en un tableau visuColors
               $visuColors=array();
               $visuColors['location']=$globals['locationcolor'];
               $visuColors['thing']=$globals['thingcolor'];
               $visuColors['organization']=$globals['organizationcolor'];
               $visuColors['function']=$globals['functioncolor'];
               $visuColors['event']=$globals['eventcolor'];
               $visuColors['time']=$globals['timecolor'];
               $visuColors['product']=$globals['productcolor'];
               $visuColors['animal']=$globals['animalcolor'];
               $visuColors['amount']=$globals['amountcolor'];
               $visuColors['person']=$globals['personcolor'];
               
           //appelle les méthodes qui existe dans le repository de entité Entity
             //le chemin de fichier repository est EENE/ExtractionBundle/Entity/Repository/EntityClassRepository.php
             //récupere le contenu de fichier Text 
            $textContent=$em->getRepository('ExtractionBundle:Entity')->getTextContentByJsonFile($idjson,$user);
           //récupere toutes les entités  de fichier JSON à partir de repository de EntityClassRepository
           $entities=$em->getRepository('ExtractionBundle:Entity')->getEntities($idjson);
          //renvoie la page d'accueil de visualisation
          return $this->render('VisualizationBundle:Default:index.html.twig',
                array('tablevisucontent' => $entities,'textcontent' => $textContent,
                'visuColors'=>$visuColors));
    }
       
}
