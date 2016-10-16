<?php
namespace EENE\ExtractionBundle\Controller;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EENE\ExtractionBundle\Entity\NerdFile;
use EENE\ExtractionBundle\Entity\TextFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use EENE\ExtractionBundle\Uploader\Response\EmptyResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
class AnalyzeController extends Controller 
{
  
    /** 
    ** Rendre la page d'analyse de texte par default
    **  Si la formulaire est validée on analyse le texte par NERD API 
    **/
    public function analyzeAction(Request $request)
    {
      $defaultData = array();
       $form = $this->createFormBuilder($defaultData)
            ->add('filename')    //les noms des fichier Text et JSON
             -> add('extractor', ChoiceType::class,   //case à choisir un extracteur de NERD
                  array(
                      'choices' => array(
                          'combined' => 'Combined',
                          'alchemyapi' => 'Alchemyapi',
                          'dandelionapi' => 'Dandelionapi',
                          'dbspotlight' => 'Dbspotlight',
                          'lupedia' => 'Lupedia',
                          'opencalais' => 'Opencalais',
                           'saplo' => 'Saplo',
                          'semitags' => 'Semitags',
                          'textrazor' => 'Textrazor' ,
                           'thd' => 'Thd',
                          'wikimeta' =>  'Wikimeta',
                          'yahoo' =>  'Yahoo' ,
                          'zemanta' =>  'Zemanta',
                      )))
           ->add('plaintext', TextareaType::class)  //le texte à analyser
           ->add('submit', SubmitType::class, //le boutton d'analyser
               array(
             'label' => 'Analyze'))->getForm();
       $etatChargement=false;
       $form->handleRequest($request);
        
      //tester si formulaire est bien validée
       if ($form->isValid()) {
             $fs = new Filesystem();
             $em = $this->getDoctrine()->getManager();
              //variable globale
                global $kernel;
                //contenu de kernel
                $container = $kernel->getContainer();
               //Récupérer les variables globales de block twig du fichier app/config.yml
               $globals = $container->get('twig')->getGlobals();
               //récuperer le nerd key de configuration de l'application (le fichier app/config.yml)
               $nerdkey=$globals['nerdkey'];
                  $user=$this->getUser();
               // data is an array with "plaintext","filename"  keys
                $data = $form->getData();
                 /* 
                 Valeurs possibles d'extracteur:
                 combined, alchemyapi, dandelionapi, dbspotlight, lupedia, opencalais, saplo, semitags,
                 textrazor, thd, wikimeta, yahoo, zemanta
                 */
                 //récuperer les données de formulaire d'analyse de texte
                  $extractor = $data['extractor'];
                  $filename=$data['filename'];
                  //Multiple spaces and newlines are replaced with a single space 
                  $textcontent = trim(preg_replace('/\s\s+/', ' ', $data['plaintext']));
                  //teser si nombre de caracters est depassé 4770 caracteres
                 if(strlen($textcontent)>4770) {
                   //affiche message d'erreur
                      $this->get('session')->getFlashBag()->add(
                         'errorNerd',
                         'Error! The text to analyze is limited to 4770 characters.'
                        );
                 }
                   //teser si le fichier JSON existe
                  elseif($fs->exists($user->getUploadUserDir().'/'.$filename.'.json'))
                  {
              
                    //affiche message d'erreur
                      $this->get('session')->getFlashBag()->add(
                         'errorNerd',
                         'Error! The filename already exists.'
                        );
                  }
                 else{
                       //appelle service d'analyse de NERD
                      $json=$this->get('analyze_nerd_service')->analyzeTextWithNERD($extractor,$nerdkey,$textcontent);
                      if($json==false){
       
                          //affiche message d'erreur
                             $this->get('session')->getFlashBag()->add(
                             'errorNerd',
                             'Error with NERD Service! Please repeat this analyze later.'
                               );
                      }else{
                           //renvoyer le chemin de fichier JSON
                           $filejsonpath=$user->getUploadUserDir().'/'.$filename.'.json';
                           //appelle methode de repository pour créer le fichier Text en serveur/BD
                           $textfilecreate=$em->getRepository('ExtractionBundle:TextFile')->createTextFileForAnalyze($filename.'.txt', $json[0],$user);
                          //appelle methode de repository pour créer le fichier JSON en serveur/BD
                            $nerdfilecreate= $em->getRepository('ExtractionBundle:NerdFile')->createNerdFileForAnalyze($filename.'.json', $json[1], $textfilecreate,$user);
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
                               //ajouter extracteur dans le fichier
                                 fwrite($fileopen,"Extractor: ".$extractor."\n");
                                 fclose($fileopen);
                           //appelle service de création des entités de fichier fourni en parametre
                              $createentities=$this->get('createentities_service')->createAllEntities($filejsonpath,$nerdfilecreate);
                           
                         if($createentities!==null){
                               $em = $this->getDoctrine()->getManager();
                               //liste des entités de fichier JSON crée 
                               //méthode existe en repository de Entity (appelé EntityClassRepository)
                               // cas où l'utilisateur veut analyser le texte saisie
                               $listEntities = $em->getRepository('ExtractionBundle:Entity')->findEntitiesByJsonFile($nerdfilecreate);
                               //appelle service geolocate
                               $file=$this->get('geolocation_service')->geocoderEntities($listEntities);
                               //affiche message de succes
                               $this->get('session')->getFlashBag()->add(
                                 'notice',
                                 'Text successfully analyzed with NERD: JSON file with its corresponding Text file correctly generated.'
                                 );
                            }
                     
                   }//fin test nerd error
               }//fin if test existance filename
            }//fin test form valid

      // ... render the form
       return $this->render('ExtractionBundle:Analyze:index.html.twig',array(
            'form' => $form->createView(),
        ));
     
   }
   
   
}
