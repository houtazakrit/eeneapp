<?php
namespace EENE\ExtractionBundle\Services;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\EntityManager;

//Service pour l'analyse de texte with NERD API
class AnalyzeNERDService
{
    
    private $em;
     //les URL constants utilisés de NERD API
    const DOCUMENT_POST = 'http://nerd.eurecom.fr/api/document';
    const DOCUMENT_GET = 'http://nerd.eurecom.fr/document';
    const ANNOTATION_POST = 'http://nerd.eurecom.fr/api/annotation';
    const ENTITY_GET = 'http://nerd.eurecom.fr/api/entity';

    function __construct(EntityManager $em)
    {
        $this->em             = $em;
        
    }
    /**  
     ** Récuperer le fichier nerd complet à partir d'un fichier Json quelconque 
     ** Renvoyer le fichier complet crée en serveur
     **/
    public function analyzeTextWithNERD($extractor,$nerdkey,$text)
    {
        // Création d'un id de document pour analyser $text
        $doc = $this->createTextDocumentFromString($text,$nerdkey);
        //créer le texte à partir d'un Id document
        //créer un tableau qui va contenir (le texte à analyser+tableau des entités crées)
        $data=array();
        if($doc!=false){
               $data[0]=$doc['text'];
               
                // Création d'un id d'annotation pour analyser $text avec $extractor
                $idAnnotation = $this->createAnnotationtFromDocument($doc['idDocument'],$nerdkey,$extractor);
                // Création des entités d'un id annotation
                 $entitiesjson = $this->createEntitiestFromAnnotation($idAnnotation, $nerdkey);
                 if($entitiesjson!=false){
                     $data[1]=$entitiesjson;
                    return $data;
                 }
            }
         return false;
    }
    
      /**
     * Create a Id document with given text.
     * Returns created document's ID or false on failure.
     * @param string $text
     * @return mixed int if document gets created, false otherwise
     */
    public function createTextDocumentFromString($text,$nerdkey) {
        if (strlen($text) == 0) {
            return false;
        } else {
            $param = array("text" => $text, "key" => $nerdkey);

            $json_encoded = $this->api_request('POST', static::DOCUMENT_POST, $param);
            $json = json_decode($json_encoded, true);
            if (isset($json['idDocument'])) {
                $json_encoded = $this->api_request('GET', static::DOCUMENT_GET . '/' . $json['idDocument']);
                $json = json_decode($json_encoded, true);
              return $json;
           
             } else {
                return false;
            }
        }
    }
     
      /**
     * Retrieve a document data, given it's ID.
     */
    public function getTextDocumentFromIdDoc($idDocument) {

            $json_encoded = $this->api_request('GET', static::DOCUMENT_GET . '/' . $idDocument);
            $json = json_decode($json_encoded, true);
            return $json['text'];
    }
   /**
     * Create a annotation with given iddocument, keynerd, extractor.
     * Returns created aanotation's ID or false on failure.
     * @param  $idDocument, $key, $extractor 
     * @return mixed    int if annotation gets created, false otherwise
     */
    public function createAnnotationtFromDocument($idDocument, $key,$extractor ) {
        if ($idDocument == 0) {
            return false;
        } else {
            $param = array("idDocument" => $idDocument, "key" => $key, "extractor" => $extractor);
            
            $json_encoded = $this->api_request('POST', static::ANNOTATION_POST, $param);
            $json = json_decode($json_encoded, true);
            if (isset($json['idAnnotation'])) {
                return $json['idAnnotation'];
            } else {
                return false;
            }
        }
    }
   
    /**
     * Create entities with given idAnnotation.
     * Returns created array entities or false on failure.
     * @param  $idDocument, $key, $extractor 
     * @return mixed    array(entity) if objects entities gets created, false otherwise
     */
  
  public function createEntitiestFromAnnotation($idAnnotation, $key) {
        if ($idAnnotation == 0) {
            //echo "pas annot!!!!";
            return false;
       } else {
            $param = array("idAnnotation" => $idAnnotation, "key" => $key);
            
            $json = $this->api_request('GET', static::ENTITY_GET, $param);
           // var_dump( $json_encoded );
           // $json = json_decode($json_encoded, true);
            if (isset($json)) {
               // echo "contenu :::$json";
                return $json;
            } else {
              
                return false;
            }
        }
    }

    /**
     * Wrapper for CURL requests
     * @param type $method
     * @param type $url
     * @param type $data
     * @return boolean
     */
    protected function api_request($method, $url, $data = array()) {
        $http_code = 0;
        switch ($method) {
            case 'GET':
            case 'get':
                $response = $this->curl_get($url . (!empty($data) ? '?' . http_build_query($data) : ''), $http_code);
                break;
            case 'POST':
            case 'post':
                $response = $this->curl_post($url, $data, $http_code);
                break;
            default:
                return false;
        }
        $valid_http_codes = array(200, 201);
        if (!in_array($http_code, $valid_http_codes)) {
            // Error
            ob_start();
            echo "\n[".date('Y-m-d H:i:s')."] Error while using NERD APIs:\n".$method.' '.$url."\nParam:\n";
            var_dump($data);
            echo "\nResponse: $http_code\n";
            var_dump($response);
            $log = ob_get_clean();
            error_log($log);
            return $response;
        } else {
            return $response;
        }
    }

    /**
     * Used to perform POST request
     * @param type $URL
     * @param type $fields
     * @param type $http_code
     * @return boolean
     */
    protected function curl_post($URL, $fields, & $http_code = 0) {
        $fields_string="";
        //url-ify the data for the POST
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        //set the url, number of POST vars, POST data
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        curl_setopt($c, CURLOPT_POST, !empty($fields));
        curl_setopt($c, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($c, CURLOPT_HTTPHEADER, array(
            'Accept-Encoding: gzip, deflate',
        ));
        $contents = curl_exec($c);
        $http_code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
        if ($contents) {
            return $contents;
        } else {
            return FALSE;
        }
    }

    /**
     * Used to perform GET request
     * @param type $URL
     * @param type $http_code
     * @return boolean
     */
    protected function curl_get($URL, & $http_code = 0) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_TIMEOUT_MS, 0);
        curl_setopt($c, CURLOPT_URL, $URL);
        curl_setopt($c, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Accept-Encoding: gzip, deflate'
        ));
        $contents = curl_exec($c);
        $http_code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
        if ($contents) {
            return $contents;
        } else {
            return FALSE;
        }
    }

} 