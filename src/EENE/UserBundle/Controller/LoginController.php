<?php

namespace EENE\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends Controller
{
    
    public function indexAction()
     {
	    
     
	    if (!$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') )
        {
              return $this->render('UserBundle:Login:index.html.twig');
        }
        else
        {    
		
      		   $bag=$this->get('session')->getFlashBag();
      
      		    $user=$this->getUser();
       		  if($user!=null){
       			 	   $lastname=$user->getLastname();
       			 	   $firstname=$user->getFirstname();
       			     $id=$user->getId();
       			     $bag->set('userInfo',array('id'  => $id,'nom'  => $lastname,'prenom'  => $firstname));
       		      }
                   $url = $this->container->get('router')->generate("eene_home");
                   return new RedirectResponse($url);
       		 
               }
	     
    }
    
    
    /**
     * Delete a text file entity.
     * *** json file qui n'ont pas associé à aucun fichier JSON
     *
     **/
    public function deleteTextFileAction($user)
    {
         $em = $this->getDoctrine()->getManager();
   
             $reqdeleteTextFile= $em->createQuery('delete FROM ExtractionBundle:TextFile textFile
              WHERE textFile.user=:user and textFile.nerdFile is NULL')
             ->setParameter('user',$user);
             
    }
}
