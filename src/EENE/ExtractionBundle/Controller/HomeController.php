<?php
namespace EENE\ExtractionBundle\Controller;
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
use Symfony\Component\HttpFoundation\Response;
class HomeController extends Controller 
{
//rendre la page de dashboard de l'utilisateur  Home/index.html.twig 
   public function indexAction()
    {   
        //appelle l'utilisateur connecté
         $user=$this->getUser();
         $em = $this->getDoctrine()->getManager();
           
     //appelle la méthode getTableFiles() qui existe dans le repository de entité User
     //le chemin de fichier repository est EENE/ExtractionBundle/Entity/Repository/HomeRepository.php
     //récupere les détails sur fichier JSON avec ses entités
      $tablejsonfiles=$em->getRepository('UserBundle:User')->getMyFiles($user);

         return $this->render('ExtractionBundle:Home:index.html.twig',
            array('tablejsonfiles' => $tablejsonfiles));
    }
 
 /*
 user_myfiles
 //rendre la page de dashboard de l'utilisateur  Home/index.html.twig 
 //generer la route user_myfiles
 */
 /**
* @Route ("/home/files", name="user_myfiles", options={"expose"=true})
*/
  public function getFilesAction(Request $request) {
               
                 //appelle l'utilisateur connecté
             $user=$this->getUser();
             $em = $this->getDoctrine()->getManager();
               
             //appelle la méthode getTableFiles() qui existe dans le repository de entité User
             //le chemin de fichier repository est EENE/ExtractionBundle/Entity/Repository/HomeRepository.php
             //récupere les détails sur fichier JSON avec ses entités
              $tablejsonfiles=$em->getRepository('UserBundle:User')->getMyFiles($user);
        
             return $this->render('ExtractionBundle:Home:index.html.twig',
                array('tablejsonfiles' => $tablejsonfiles));
       
       
  }
  
  /**
*  geolocaliser les entités non geolocalisées (de type "notGeocoded")
*/
  public function geocoderNotGeocodedEntitiesAction($idjson) {
             $user=$this->getUser();
             $em = $this->getDoctrine()->getManager();
              $listnotgeocodedentities = $em->getRepository('UserBundle:User')->getNotGeocodedSpatialEntities($idjson);
                    # Chemin vers fichier log
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
               //appelle service geolocation
               $this->get('geolocation_service')->geocoderEntities($listnotgeocodedentities);
              
             //appelle la méthode getTableFiles() qui existe dans le repository de entité User
             //le chemin de fichier repository est EENE/ExtractionBundle/Entity/Repository/HomeRepository.php
             //récupere les détails sur fichier JSON avec ses entités
              $tablejsonfiles=$em->getRepository('UserBundle:User')->getMyFiles($user);
         //affiche message de succes
                         $this->get('session')->getFlashBag()->add(
                        'successgeolocation',
                        'You successfully call geolocation process.'
                       );
             return $this->render('ExtractionBundle:Home:index.html.twig',
                array('tablejsonfiles' => $tablejsonfiles));
 
  }
   
}
