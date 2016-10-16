<?php
namespace EENE\ExtractionBundle\Services;
use EENE\ExtractionBundle\Entity\Entity;
use Doctrine\ORM\EntityManager;
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;
use CrEOF\Spatial\PHP\Types\Geometry;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\ORM\Query\AST\Functions\MySql\GeomFromText;
//Service pour la création des entités 
class CreateEntitiesService{

    private $em;
    private $Userrepository;
    private $url;

   function __construct(EntityManager $em){
        $this->em = $em;
        $this->EntityClassRepository = $this->em->getRepository('ExtractionBundle:Entity');
        
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

   //créer toutes les entités (avec leurs gelocations si lentité est de type location 
   //ou il  a un uri de dbpedia.org) d'un fichier JSON sur BD
    public function createAllEntities($jsonfile,$nerdfile){
        $file=file_get_contents($jsonfile);
         //décoder le fichier JSON
              $tableentities=json_decode($file);
              if(isset($tableentities)){
               //get user from Nerd file
                    $user=$nerdfile->getUser();
                    # Chemin vers fichier log
                     $file =$user->getUploadUserDir().'/'.$user->getUsername()."_logfile.log";
                    //ajouter dans le fichier log l'entité crée en ajoutant au contenu
                      $fileopen=fopen($file,'a');
                       if(count($tableentities)>0){
                        $createdEntities="Created Entity(ies)";
                       }else{
                         $createdEntities="No entity identified";
                       }
                        fwrite($fileopen,$createdEntities."\n\n");
                       fclose($fileopen);
                    //parcourir le contenu de fichier JSON en récupérant les entités existantes
                    for($i=0;$i<count($tableentities);$i++) {
                       //creer l'entité avec geolocation par default null (geometry: null + geolocatedBy: null) sur BD
                       $this->EntityClassRepository->createEntityWithGeoLocation($tableentities[$i],$nerdfile);
                    }
                     return $tableentities;
              }
              return null;
        
    }
   
}