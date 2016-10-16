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
class FileController extends Controller 
{

     /**
    *méthode permet de télécherger et ouvrir un fichier
    *on fournit comme parametres l'id et le type de fichier (Json ou Txt)
    **/
    public function downloadFileAction($id,$typefile) {

        /** 
         * tester le type de fichier 'textfile or nerdfile' 
        *s'il s'agit de text file on rècupere l'entité TextFile
        *sinon on rècupere l'entité NerdFile 
        * **/
        if($typefile=="jsonfile"){
           $repository = $this->getDoctrine()
                       ->getRepository('ExtractionBundle:NerdFile');
        }
        else if($typefile=="textfile"){
           $repository = $this->getDoctrine()
                       ->getRepository('ExtractionBundle:TextFile');
        }
          $file=$repository->find($id);
       $response=new Response();
   
       $response->headers->set('Content-type', 'application/octet-stream');
       $response->headers->set('Content-Disposition', 
       sprintf('attachment; filename="%s"',$file->getName()));
        $response->headers->set('Content-Transfer-Encoding', 'binary');
       $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->setContent(file_get_contents($file->getUser()->getUploadUserDir().'/'.$file->getName()));
         
       return $response;

   }
 
     /**
     * Supprimer fichier.
     * **** json file et Text file, si le Json file a un seul fichier Text sinon 
     * *** sinon json file seuelement
     * *** 
     *
     **/
    public function deleteFileAction($id)
    {
         $em = $this->getDoctrine()->getManager();
             $reqdeleteJsonTextFile=$em->getRepository('ExtractionBundle:NerdFile')->deleteJsonText($id);
             //récupere méthode de NerdFileRepository qui supprime le Json file et son correspond text file
                if($reqdeleteJsonTextFile===1){
                 
                 //affiche message de succes
                   $this->get('session')->getFlashBag()->add(
                      'successdelete',
                      'You successfully delete JSON file with its corresponding Text file!'
                     );
                }
                else
                {
                 $reqdeleteJsonFile=$em->getRepository('ExtractionBundle:NerdFile')->deleteOnlyJsonFile($id);
              
                    if($reqdeleteJsonFile===1) {
                       //affiche message de succes
                         $this->get('session')->getFlashBag()->add(
                        'successdelete',
                        'You successfully delete only JSON file!'
                       );
                    }
                }
          
       return $this->redirect($this->generateUrl('eene_home'));
       
    }
    
    
     /**
    *méthode permet d'afficher le contenu de fichier log de l'utilisateur
    * contenant les traces de création et geolocalisation des entités
    *LEs résultats des operations (analyze, upload, geolocation entities not geocoded)
    */
    
    public function viewLogFileAction() {

       $user=$this->getUser();
       $file=$user->getUploadUserDir().'/'.$user->getUsername()."_logfile.log";
      //récuperer le contenu de fichier log
       $textContent=file_get_contents($file);
       
       // ... render the page operationcompleted
        return $this->render('ExtractionBundle:Operation:operationcompleted.html.twig',
       array(
            'textContent' =>$textContent,
        ));
      
     
        }

}
