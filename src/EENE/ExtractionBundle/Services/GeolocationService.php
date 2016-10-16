<?php
namespace EENE\ExtractionBundle\Services;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as ContainerInterface;
//Service pour la géolocalisation
class GeolocationService 
{
    private $em;
    private $container;
    
    function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em             = $em;
        $this->EntityClassRepository = $this->em->getRepository('ExtractionBundle:Entity');
        $this->container = $container;
    }
      /**
     * Récuperer le type Geometry (Point)  d'une chaine de caractere
     *  afin que doctrine comprenne le geometry
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
   /**  
     ** Récuperer liste de toutes les entités d'un fichier JSON de BD
     ** en utilisant un algo qui permet de geolocaliser les entités (qui ont un uri de DBPedia ou de type Location)
     ** Geolocaliser en modifiant les entités geolocation associées à ces entitées
     **/
    public function geocoderEntities($listEntities)
    {
            /* 
            On suppose qu'on a un fichier bien formé qui  contient null ou notGeocoded pour les entités non géocodées.
            Les services geonames et gMapsApi prennent en paramètre le label d'une entité et retournent :
            - "serviceProblem" : lorsque le service ne peut plus être interrogé (nbre max de requêtes atteinte par exemple)
            - "notGeocodable" : le service ne sait pas géocoder l'entité
            - une géométrie : le service a su géocoder et il a retourné la géométrie de l'entité
            */
            // Initialisations
            $geonamesProblem = false;
            $GMapsProblem    = false;
            //get user
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            # Chemin vers fichier log
            $file =$user->getUploadUserDir().'/'.$user->getUsername()."_logfile.log";
            # Ouverture en mode écriture en ajoutant le contenu de fichier log
            $fileopen=fopen($file,'a');
            //écrire dans le fichier log "Geolocating Entity(ies)" si le nombre des entités >0
              
           if(count($listEntities)>0){
                $geolocating="Geolocating Entity(ies)";
           }else{
               $geolocating="No Geolocation";
           }
           fwrite($fileopen,"\n".$geolocating);
           foreach ($listEntities as $entity){
               # Ecriture dans le fichier log
                fwrite($fileopen,"\n\nEntity Id: ".$entity->getId());
                fwrite($fileopen,"\nTrying to geolocate: ".$entity->getLabel());
                fwrite($fileopen,"\nNERD type: ".$entity->getNerdType());
                fwrite($fileopen,"\nExtractor: ".$entity->getExtractor());
                fwrite($fileopen,"\nText position: ".$entity->getStartChar());
                if($entity->getUri()!=null){
                 $uri=$entity->getUri();
                }else{
                    $uri="Not defined";
                }
                fwrite($fileopen,"\nURI: ".$uri);
              //tranformer la liste des geolocation de l'entité (la liste est retournée en tant qu'objet de type Collection) en Array
              // $geo est une instance de GeoLocation
               $geo=$entity->getGeolocation();
              $geocodedDBPedia=false;
             // on essaie de géolocaliser chaque entité à partir en DBPedia
             if($this->isDBpediaUri($entity->getUri())){
                 
                 //on enregistre les coordonnées récupérées par DBPedia
                       $geometry= $this->getCoordinatesFromDBPedia($entity->getUri());
                       fwrite($fileopen,"\nCalling DBpedia... ");
                       fwrite($fileopen,"Response: $geometry ");
                    /* ON vérifie si le serveur de DbPedia a répondu et a retourné des coordonnées 
                    pour définir l'atttribut GeolocatedBy */
                    switch($geometry){
                           case 'notGeocodable':
                               $geolocatedBy='notGeocodable';
                               $geometry=null;
                                $geocodedDBPedia=false;
                                break;
                            case 'ServiceProblem': 
                                 $geolocatedBy='notGeocoded';
                                 $geometry=null;
                                 $geocodedDBPedia=false;
                                break;
                            default:
                                $geolocatedBy='DBPedia';
                                $geometry= $this->getGeometryByText($geometry);
                                $geocodedDBPedia=true;
                            }
                
                   //modify geometry and geolocatedBy
                       $geo->setGeolocatedBy($geolocatedBy);
                        $geo->setGeometry($geometry);
                        $this->em->persist($geo);
                        $this->em->flush();
                              
                }  
                
                //tester si le type de l'entité est Location et n'est pas geolocalisée par DBPedia
                 if ($geocodedDBPedia===false  && $entity->getNerdType()=='Location') {
                         
                        // L'entité n'est pas encore géolocalisée
                          if ($geo->getGeometry() === null) {
                              // On tente de géolocaliser via geonames ou GMapsApi
                                // On tente de géocoder avec Geonames
                                if ($geonamesProblem === false) // on peut appeler Geonames
                                    {
                                    // on essaie de géolocaliser à partir de Geonames
                                    $geometry = $this->getCoordinatesFromGeonames($entity->getLabel());
                                    fwrite($fileopen,"\nCalling Geonames... ");
                                    fwrite($fileopen,"Response: $geometry");
                                    
                                    
                                    switch ($geometry) {
                                        case "ServiceProblem": // il y a un problème avec Geonames --> on essaie avec GMapsApi
                                            $geonamesProblem = true;
                                            /* On indique que ce n'est plus la peine d'appeler Geonames */
                                            // On essaie d'appeler GMapsApi
                                            if ($GMapsProblem === false) {
                                                // on essaie de géolocaliser à partir de GoogleApi
                                                $geometry = $this->getCoordinatesFromGMapsApi($entity->getLabel());
                                                fwrite($fileopen,"\nCalling GMaps API... ");
                                                fwrite($fileopen,"Response: $geometry ");

                                                switch ($geometry) {
                                                    case "ServiceProblem": // il y a un problème avec GmapsAPI et Geonames, il faut reporter le geocodage à plus tard
                                                   //modify geometry and geolocatedBy
           
                                            $geo->setGeometry(null);
                                            $geo->setGeolocatedBy('notGeocoded');
                                            $this->em->persist($geo);
                                            $this->em->flush();
                                                        $GMapsProblem               = true;
                                             
                                                        break;
                                                    case "notGeocodable": //  GmapsAPI ne sait pas géocoder cette entité et Geonames n'a pas pu être appelé -->  il faut reporter le geocodage à plus tard 
                                             //modify geometry and geolocatedBy
                                            $geo->setGeometry(null);
                                            $geo->setGeolocatedBy('notGeocoded');
                                            $this->em->persist($geo);
                                            $this->em->flush();
                                                        break;
                                                    default: // gMapsApi a retourné une geometry
                                                                                                           //modify geometry and geolocatedBy
                                          $geo->setGeometry($this->getGeometryByText($geometry));
                                            $geo->setGeolocatedBy("GMapsApi");
                                            $this->em->persist($geo);
                                            $this->em->flush();   
                                                        break;
                                                }
                                            } else // On ne peut pas appeler GoogleMaps --> on reporte le géocodage
                                                {
                                        //modify geometry and geolocatedBy
                                             $geo->setGeometry(null);
                                            $geo->setGeolocatedBy('notGeocoded');
                                            $this->em->persist($geo);
                                            $this->em->flush();
                                                    
                                                }
                                            break;
                                        
                                        case "notGeocodable": // Geonames ne sait pas géocoder l'entité
                                            // On essaie d'appeler GMapsApi
                                            if ($GMapsProblem === false) {
                                                // on essaie de géolocaliser à partir de GoogleApi
                                                $geometry = $this->getCoordinatesFromGMapsApi($entity->getLabel());
                                               fwrite($fileopen,"\nCalling GMaps API... ");
                                               fwrite($fileopen,"Response: $geometry ");
                                                switch ($geometry) {
                                                    case "ServiceProblem": // il y a un problème avec GmapsAPI et Geonames, il faut reporter le geocodage à plus tard
                                              //modify geometry and geolocatedBy
                                                $geo->setGeometry(null);
                                                $geo->setGeolocatedBy('notGeocoded');
                                                $this->em->persist($geo);
                                                $this->em->flush();
 
                                                        $GMapsProblem               = true;
                                                        break;
                                                    case "notGeocodable": //  GmapsAPI ne sait pas géocoder cette entité et Geonames non plus --> on indique que cette entité n'est pas géocodable 
                                            //modify geometry and geolocatedBy
                                                $geo->setGeometry(null);
                                                $geo->setGeolocatedBy('notGeocodable');
                                                $this->em->persist($geo);
                                                $this->em->flush();
                                                        break;
                                                    default: // gMapsApi a retourné une geometry
                                               
                                                $geo->setGeometry($this->getGeometryByText($geometry));
                                                $geo->setGeolocatedBy("GMapsApi");
                                                $this->em->persist($geo);
                                                $this->em->flush(); 
                                                        break;
                                                }
                                            } else // Il y a un problème avec GoogleMaps --> on reporte le géocodage
                                                {
                                                $geo->setGeometry(null);
                                                $geo->setGeolocatedBy('notGeocoded');
                                                $this->em->persist($geo);
                                                $this->em->flush();
                                                    
                                                }
                                          break;
                                        default: // Geonames a retourné une geometry
                                          
                                                $geo->setGeometry($this->getGeometryByText($geometry));
                                                $geo->setGeolocatedBy("Geonames");
                                                $this->em->persist($geo);
                                                $this->em->flush(); 
                                    }
                                } else // On ne peut pas appeler Geonames
                                    {
                                    // On tente de géocoder avec GMapsApi
                                    if ($GMapsProblem === false) // on peut appeler GMapsApi
                                        {
                                        // on essaie de géolocaliser à partir de GoogleApi
                                        $geometry = $this->getCoordinatesFromGMapsApi($entity->getLabel());
                                             fwrite($fileopen,"\nCalling GMaps API... ");
                                             fwrite($fileopen,"Response: $geometry ");
                                        switch ($geometry) {
                                            case "ServiceProblem": // il y a un problème avec GmapsAPI, il faut reporter le geocodage à plus tard
                                                $GMapsProblem = true;
                                            /* On indique que ce n'est plus la peine d'appeler GMapsApi 
                                            et on exécute les actions du cas "notGeocodable" car on n'a pas mis de 'break'*/
                                            case "notGeocodable": //  GmapsAPI ne sait pas géocoder cette entité et Geonames n'a  pas pu être appelé --> on reporte le géocodage de cette entité 
                                               
                                                    $geo->setGeometry(null);
                                                    $geo->setGeolocatedBy('notGeocoded');
                                                    $this->em->persist($geo);
                                                    $this->em->flush();
                                                break;
                                            default: // gMapsApi a retourné une geometry
                                            
                                                    $geo->setGeometry($this->getGeometryByText($geometry));
                                                    $geo->setGeolocatedBy("GMapsApi");
                                                    $this->em->persist($geo);
                                                    $this->em->flush();  
                                                break;
                                        }
                                    } else // il faut reporter le geococage à plus tard
                                        {
                                                    $geo->setGeometry(null);
                                                    $geo->setGeolocatedBy('notGeocoded');
                                                    $this->em->persist($geo);
                                                    $this->em->flush();
                                    }
                                }
                            }//fin geolocalisation par geonames et GMAPSApi
                            
                        } //fin if geolocalisé

            } //FIN de foreach $listEntities
          # On ferme le fichier proprement
               fclose($fileopen);
    }
   
    
    /**  
     ** récuperer les coordonnées à partir d'une adresse en geonames
     ** renvoyer le point(x,y) d'une location
     * eenegeonames est username d'un compte geonames
     **/
    
    private function getCoordinatesFromGeonames($address)
    {
        $address     = str_replace(" ", "+", $address); // replace all the white space with "+" sign to match with google search pattern
        $url = "http://api.geonames.org/searchJSON?q=$address&username=eenegeonames";
       
       $ch      = curl_init();
        $options = array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false
        );
        
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        if(empty($response)){
            return "ServiceProblem";
        }
        $intReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //si ya erreur en curl on retourne null
        if (curl_errno($ch)) {
            return "ServiceProblem";
        }
        curl_close($ch);
        if($intReturnCode != 200 && $intReturnCode != 302 && $intReturnCode != 304) {
        return "ServiceProblem";
        }
        $response = json_decode($response, true);
        
        if (isset($response['geonames'][0]['lng']) && isset($response['geonames'][0]['lat'])) {

           //renvoyer Point(x y) x:longitude; y:latitude
            return "POINT(" . $response['geonames'][0]['lng'] . " " . $response['geonames'][0]['lat'] . ")";
            
        } 
         //indique que le géocode a réussi mais n'a renvoyé aucun résultat
        elseif (isset($response['totalResultsCount']) && $response['totalResultsCount'] === 0) {
            return "notGeocodable";
        } 
         //indique que vous avez un erreur par exemple :dépassé votre quota.
        else {
            return "ServiceProblem";
        }
        
    }
    /**  
     ** récuperer les coordonnées à partir d'une adresse de google maps 
     ** renvoyer le point(x,y) d'une location
     ** sinon renvoyer l'erreur OVER_QUERY_LIMIT si vous avez dépassé votre quota
     ** sinon renvoyer null
     **/
    
    private function getCoordinatesFromGMapsApi($address)
    {
        
        
        $address = str_replace(" ", "+", $address); // replace all the white space with "+" sign to match with google search pattern
        
        $url     = "http://maps.google.com/maps/api/geocode/json?address=$address";
        $ch      = curl_init();
        $options = array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false
        );
        
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        if(empty($response)){
            return "ServiceProblem";
        }
        $intReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //si ya erreur en curl on retourne null
        if (curl_errno($ch)) {
            return "ServiceProblem";
        }
        curl_close($ch);
        if($intReturnCode != 200 && $intReturnCode != 302 && $intReturnCode != 304) {
        return "ServiceProblem";
        }
        $response = json_decode($response, true);
        
        if ($response['status'] === "OK") {
            $geometry = $response['results'][0]['geometry'];
            $long     = $geometry['location']['lat'];
            $lat      = $geometry['location']['lng'];
            //renvoyer "POINT(x y)" avecx:longitude; y:latitude
            return "POINT(" . $long . " " . $lat . ")";
            
        } elseif ($response['status'] === "ZERO_RESULTS") {
            return "notGeocodable";
        } else {
            return "ServiceProblem";
        }
        
    }
    
    /**  
     ** Retourne, à partir d'une URI DbPedia, le lieu associé
     ** Retourne null si l'URI n'est pas DbPedia ou si aucun
     ** lieu n'est associé.
     **/
    function isDBpediaUri($uri)
    {
        return strpos($uri, 'dbpedia.org/resource/') == false ? false : true;
    }
    /**  
     ** récuperer les coordonnées à partir d'une uri de DB Pedia
     ** renvoyer le geometry de type point(x,y) d'une location
     * ou null si le serveur de DbPedia ne répond pas.
     **/
     
     function myUrlEncode($string) {
            $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
            $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
            return str_replace($entities, $replacements, urlencode($string));
     }
    private function getCoordinatesFromDBPedia($uri)
    {

       // $urlReconstructed = str_replace(')', '%29', str_replace('(', '%28', $uri));
       
        $tab   = explode("/", urldecode($uri));

        $place = end($tab);
        // construction de la requête pour récupérer la geometrie de $place    
        $query = "PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#>
           PREFIX dbp: <http://dbpedia.org/resource/>
           SELECT ?geometry 
           WHERE {
           dbp:" . $place . " 
           geo:geometry ?geometry .
          }
         LIMIT 1";
        
        // Construction de l'url qui va être appelée pour exécuter la requête
        $url = 'http://dbpedia.org/sparql?' . 'query=' . urlencode($query) . '&format=json';
        
        // is curl installed?
        if (!function_exists('curl_init')) {
            die('CURL is not installed!');
        }
        $ch      = curl_init();
        $options = array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url ,
            CURLOPT_HEADER => false
        );
        
        curl_setopt_array($ch, $options);
        $response      = curl_exec($ch);
         if(empty($response)){
             return "ServiceProblem";
        }
        if ($response === FALSE) {
          return "ServiceProblem";
        } 
        $intReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //si ya erreur en curl on retourne ServiceProblem
         if (curl_errno($ch)!=0) {
           return "ServiceProblem";
          }
      
         curl_close($ch);
         if($intReturnCode != 200 && $intReturnCode != 302 && $intReturnCode != 304 ) {
               return "ServiceProblem";
            }
        $responseArray = json_decode($response, true);
        //renvoyer geometry
        if (isset($responseArray["results"]["bindings"][0]["geometry"]["value"])) {
            
            //renvoyer Point(x y) x:longitude; y:latitude
            return $responseArray["results"]["bindings"][0]["geometry"]["value"];
          }
        else{
            
            return "notGeocodable";
        }
        
    }
} 