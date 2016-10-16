<?php
namespace EENE\ExtractionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EENE\ExtractionBundle\Entity\NerdFile;
use EENE\ExtractionBundle\Uploader\Response\EmptyResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
class EntityController extends Controller 
{

     /**
    ** méthode permet d'afficher le détails sur des entités spatiales (geocoded, notGeocoded, notGeocodable )
    ** on fournit comme parametres l'id et le type de l'entité spatiale
    **/
    public function showEntitiesAction($id,$type) {
        
          $em = $this->getDoctrine()->getManager();
      //récupere les détails sur fichier JSON avec ses entités à partir de repository de nerdFile
      $spatialEntities=$em->getRepository('ExtractionBundle:NerdFile')->getSpatialEntities($id,$type);
      //rendre la page des entités d'un fichier JSON  Entities/showSpatialEntities.html.twig 
      return $this->render('ExtractionBundle:Entities:showSpatialEntities.html.twig',
            array('tableSpatialEntities' => $spatialEntities));

    }

  /**
    ** méthode permet d'afficher le détails sur des entités spatiales (geocoded, notGeocoded, notGeocodable )
    ** on fournit comme parametres l'id et le type de l'entité spatiale
    **/
    public function showAllEntitiesAction($idjson) {
        
          $em = $this->getDoctrine()->getManager();
      //récupere les détails sur fichier JSON avec ses entités à partir de repository de nerdFile
      $entities=$em->getRepository('ExtractionBundle:NerdFile')->getAllEntities($idjson);
      //rendre la page des entités d'un fichier JSON  Entities/showSpatialEntities.html.twig 
      return $this->render('ExtractionBundle:Entities:showAllEntities.html.twig',
            array('tableEntities' => $entities));

    }
    
     /**
    ** méthode permet d'afficher le détails sur des entités spatiales (geocoded, notGeocoded, notGeocodable )
    ** on fournit comme parametres l'id et le type de l'entité spatiale
    **/
    public function showTemporelAndOtherEntitiesAction($idjson,$entitytype) {
        
          $em = $this->getDoctrine()->getManager();
      //récupere les détails sur fichier JSON avec ses entités à partir de repository de nerdFile
      $entities=$em->getRepository('ExtractionBundle:NerdFile')->getTemporelAndOtherEntities($idjson,$entitytype);
      //rendre la page des entités d'un fichier JSON  Entities/showSpatialEntities.html.twig 
     if($entitytype==='temporal'){
      return $this->render('ExtractionBundle:Entities:showTemporalEntities.html.twig',
            array('tableEntities' => $entities));
     }else{
         
               return $this->render('ExtractionBundle:Entities:showOtherEntities.html.twig',
            array('tableEntities' => $entities));
     }
    }
}


