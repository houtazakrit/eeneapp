<?php
namespace EENE\ExtractionBundle\Entity\Repository;
use EENE\ExtractionBundle\Entity\Geolocation;
use EENE\ExtractionBundle\Entity\Entity;
use Doctrine\ORM\EntityRepository;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
/**
 * EntityClassRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EntityClassRepository extends EntityRepository
{
 
    /**  
     ** Retourne, à partir d'une URI DbPedia, le lieu associé
     ** Retourne null si l'URI n'est pas DbPedia ou si aucun
     ** lieu n'est associé.
     **/
    public function isDBpediaUri($uri)
    {
        return strpos($uri, 'dbpedia.org') == false ? false : true;
    }
    
     
    /**
    ** méthode permet d'afficher le détails sur des entités spatiales (geocoded, notGeocoded, notGeocodable )
    ** et temporelles et autres on fournit comme parametres l'id de fichier JSON et l'utilisateur
    **/
    public function getEntities($idjson) {

        $entities=$this->getEntityManager()->
          createQuery("
             SELECT entities
             FROM ExtractionBundle:Entity entities
             JOIN entities.nerdFile jsonFile
             LEFT JOIN entities.geolocation geo
             LEFT JOIN entities.times time
             LEFT JOIN jsonFile.textFile text
              WHERE jsonFile.id=:idjsonfile
              ORDER BY entities.startChar")
             ->setParameter('idjsonfile',$idjson)->getResult();
            
      //renvoyer un tableau en supprimant les entités redoublons basé sur la colonne 'label'
      // $entitiesdata = array_intersect_key($entitiesdata, array_unique(array_column($entitiesdata, 'label')));
      return $entities;

   }
     
    /**
      récuperer le contenu d'un fichier Text
    **/
    public function getTextContentByJsonFile($idjson,$user) {
      $textfile=$this->getEntityManager()->
          createQuery("
             SELECT textfile.name
             FROM ExtractionBundle:NerdFile jsonFile
             JOIN jsonFile.textFile textfile
             WHERE jsonFile.id=:idjsonfile")
             ->setParameter('idjsonfile',$idjson)->getResult();
       if(isset($textfile[0]['name'])){
         return file_get_contents($user->getUploadUserDir().'/'.$textfile[0]['name']);
       }else {
        return null;
       }
    }
  
   /**
     *  get Entity
     * creer l'entité sur BD avec sa geolocation NULL 
     * si l'entité a une uri de DBPedia ou de type Location
     * @return \EENE\ExtractionBundle\Entity\Entity
     */
     public function createEntityWithGeoLocation($table,$nerdfile)
    {  
           $em= $this->getEntityManager();
            $entity=new Entity();
            $arrayNerdType = explode('#', $table->{'nerdType'});
           $entity->setNerdFile($nerdfile);
           $entity->setExtractorType($table->{'extractorType'});
           $entity->setNerdType($arrayNerdType[1]);
           $entity->setUri($table->{'uri'});
           $entity->setConfidence($table->{'confidence'});
           $entity->setExtractor($table->{'extractor'});
           $entity->setRelevance($table->{'relevance'});
           $entity->setStartChar($table->{'startChar'});
           $entity->setEndChar($table->{'endChar'});
           $entity->setLabel($table->{'label'});
            
         //tester l'entité si de type location ou son uri est de DBPedia
         if($arrayNerdType[1]=='Location' || $this->isDBpediaUri($table->{'uri'})){
         // créer une geolocation de l'entité crée ci-dessus avec geometry et geolocatedBy sont null
             $geolocationcreate=new Geolocation();
            $geolocationcreate->addGeoEntity($entity);
           $entity->setGeolocation($geolocationcreate);
           $em->persist($geolocationcreate);
            $em->persist($entity);
           $em->flush();
           
        }else{
         

           $em->persist($entity);
            $em->flush();
        }
        //get user from Nerd file
        $user=$nerdfile->getUser();
        # Chemin vers fichier log
         $file =$user->getUploadUserDir().'/'.$user->getUsername()."_logfile.log";
        
      //ajouter dans le fichier log entité crée en ajoutant apres le contenu de fichier
      $fileopen=fopen($file,'a');
       fwrite($fileopen,"Create Entity with Id: ".$entity->getId().", Label: ".$entity->getLabel()."\n");
       fclose($fileopen);
    }
    

    /*
    * récuperer toutes les entités d'un fichier JSON
    */
  public function findEntitiesByJsonFile($jsonFile){
      
     $entities=$this->getEntityManager()->
          createQuery("
             SELECT e
             FROM ExtractionBundle:Entity e
              JOIN e.geolocation g
              WHERE e.nerdFile=:jsonFile")
             ->setParameter('jsonFile',$jsonFile)->getResult();

       return $entities;
     }
 
  
     /**
     * Récuperer le type Geometry (Point)  d'une chaine de caractere
     *
     */

      public function getGeometryByText($geometryString){
       
       $geometryArray= explode('(',$geometryString);
       //tester s'il s'agit bien d'un point
       if($geometryArray[0]==="POINT"){
             $geo= explode(' ',trim($geometryArray[1]));
             $geo1= explode(')',trim($geo[1]));
            return new Point($geo[0],$geo1[0]);
       }
       //on peut ajouter ici d'autres testes sur autres types de geometry (POLYGONE ...)
    }
    
    
    
 
}
