<?php
namespace EENE\ExtractionBundle\Controller;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EENE\ExtractionBundle\Entity\NerdFile;
use EENE\ExtractionBundle\Entity\TextFile;
use EENE\ExtractionBundle\Controller\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EENE\ExtractionBundle\Form\Type\FileFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use EENE\ExtractionBundle\Uploader\Response\EmptyResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Console\Helper\ProgressBar;
class UploadController extends Controller 
{
  //rendre la page d'upload des fichiers
   public function indexAction()
    {
       return $this->render('ExtractionBundle:Upload:index.html.twig');
    
    }
    
     // upload file JSON format only by dropzone
    public function uploadJsonFileAction()
    {
        $request = $this->get('request');
        //récuperer les fichiers uploadés
        $files = $request->files;
         $fs=new Filesystem();
        $em = $this->getDoctrine()->getManager();
       //récuperer l'utilisateur connecté
       $user= $this->getUser();
       
        try {
            //parcourir les fichiers uploadés
            //notre cas c'est juste un seul fichier autorisé à uploader
           foreach ($files as $uploadedFile) {
              $filename=$user->getUploadUserDir().'/'.$uploadedFile->getClientOriginalName();
               //tester si fichier n'existe pas dans le serveur (on teste sur son nom)
               if(!$fs->exists($filename)){
                  //appelle méthode qui crée nerd file en BD et serveur
                  //qui corresponds à un fichier Text 
                 
                   $nerdfile= $em->getRepository('ExtractionBundle:NerdFile')->createNerdFileForUpload($uploadedFile,null,$user);
                         //chemin vers fichier log
                          $file =$user->getUploadUserDir().'/'.$user->getUsername()."_logfile.log";
                            //ajouter dans le fichier log l'entité crée en écrasant le contenu
                              $fileopen=fopen($file,'w+');
                               //ajouter la date dans le fichier
                               $date=new \DateTime();
                                fwrite($fileopen,"At ".$date->format("Y-m-d H:i:s")."\n");
                                $username=$user->getUsername();
                                //ajouter username dans le fichier
                                 fwrite($fileopen,"Username: ".$username."\n");
                                 fclose($fileopen);
                   //appelle service de création des entités de fichier fourni en parametre
                  $createentities=$this->get('createentities_service')->createAllEntities($filename,$nerdfile);
                  
                  if($createentities!==null){
                   
                  //liste des entités de fichier JSON crée 
                  //méthode existe en repository de Entity (appelé EntityClassRepository)
                  // cas où l'utilisateur veut analyser le texte saisie
                  $listEntities = $em->getRepository('ExtractionBundle:Entity')->findEntitiesByJsonFile($nerdfile);
                  //appelle service geolocate
                  $file=$this->get('geolocation_service')->geocoderEntities($listEntities);

                   }
                }
                else {
                 //erreur 404 : si le fichier existe en serveur
                    // renvoyer message d'erreur
                    header('HTTP/1.0 404');
                     header('Content-Type: application/json; charset=UTF-8');
                       die();

                }
            }
           
           } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
           }
          
        // return data to the frontend
       return new JsonResponse([]);
    }
     // upload 1 json et 1 text file by dropzone
   public function uploadTxtJsonFileAction()
    {
            $fs=new Filesystem();
             $em = $this->getDoctrine()->getManager();
       // récuperer les fichiers uploadés:: file est nom de input de type file
        $files = $this->getRequest()->files->get('file');
         $user=$this->getUser();
        try {
            
            $filename1=$user->getUploadUserDir().'/'.$files[0]->getClientOriginalName();
            $filename2=$user->getUploadUserDir().'/'.$files[1]->getClientOriginalName();
                            //chemin vers fichier log
                             $file =$user->getUploadUserDir().'/'.$user->getUsername()."_logfile.log";
                            //ajouter dans le fichier log l'entité crée en écrasant le contenu
                              $fileopen=fopen($file,'w+');
                               //ajouter la date dans le fichier
                               $date=new \DateTime();
                                fwrite($fileopen,"At ".$date->format("Y-m-d H:i:s")."\n");
                                $username=$user->getUsername();
                                //ajouter username dans le fichier
                                 fwrite($fileopen,"Username: ".$username."\n");
                                 fclose($fileopen);
         //tester si le fichier JSON ou Text n'existe pas dans le serveur 
          if($fs->exists($filename1) || $fs->exists($filename2)){
                  //sinon afficher message erreur
                  //erreur 404 : si le fichier existe en serveur
                   header('HTTP/1.0 404');
                    header('Content-Type: application/json; charset=UTF-8');
                         die();
                      //   http_response_code(404);
           }
            // tester l'extension de fichier uploadé
           //1er cas le 1er fichier est JSON et 2éme est fichier Text
            elseif($files[0]->getClientOriginalExtension()=='json')
            {  
             //créer le fichier Text
                $textfile=$em->getRepository('ExtractionBundle:TextFile')->createTextFileForUpload($files[1],$user);
                    //créer le fichier JSON 
                  $nerdfile= $em->getRepository('ExtractionBundle:NerdFile')->createNerdFileForUpload($files[0],$textfile,$user);
                  //appelle service de création des entités de fichier fourni en parametre
                 $createentities=$this->get('createentities_service')->createAllEntities($filename1,$nerdfile);
                 if($createentities!==null){
                  
                  //liste des entités de fichier JSON crée 
                  //méthode existe en repository de Entity (appelé EntityClassRepository)
                  // cas où l'utilisateur veut analyser le texte saisie
                  $listEntities = $em->getRepository('ExtractionBundle:Entity')->findEntitiesByJsonFile($nerdfile);
                  //appelle service geolocate
             
                     //appelle service geolocate
                  $file=$this->get('geolocation_service')->geocoderEntities($listEntities);
                   }
  
              } //2eme cas le 1er fichier est Text et 2éme est fichier JSON
              else
              {
                //créer le fichier text 
                $textfile= $em->getRepository('ExtractionBundle:TextFile')->createTextFileForUpload($files[0],$user);
               
                //créer le fichier JSON 
                $nerdfile= $em->getRepository('ExtractionBundle:NerdFile')->createNerdFileForUpload($files[1],$textfile,$user);
               
                  //appelle service de création des entités de fichier fourni en parametre
                 $createentities=$this->get('createentities_service')->createAllEntities($filename2,$nerdfile);
                 if($createentities!==null){
                 
                  //liste des entités de fichier JSON crée 
                  //méthode existe en repository de Entity (appelé EntityClassRepository)
                  // cas où l'utilisateur veut analyser le texte saisie
                  $listEntities = $em->getRepository('ExtractionBundle:Entity')->findEntitiesByJsonFile($nerdfile);
             
                     //appelle service geolocate
                  $file=$this->get('geolocation_service')->geocoderEntities($listEntities);
                
                   }
               }
             
       } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
      }
      
        // return data to the frontend
    return new JsonResponse([]);
    }
  
}
