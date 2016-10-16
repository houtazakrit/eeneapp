<?php
/*
*Controller pour étendre le controller de bundle easy admin
*on a étendu fonction delete et new 
*/
namespace EENE\UserBundle\Controller;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\HttpFoundation\Request;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use EENE\UserBundle\Entity\User;
class AdminController extends BaseAdminController
{
   
    public function createNewUserEntity()
    {
       //$user=$this->get('fos_user.user_manager')->createUser();
         // $user->createUserDirectory();
        return $this->get('fos_user.user_manager')->createUser();
    }

    public function prePersistUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }
    public function preUpdateUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }
    
    
       /**
     * The method that is executed when the user performs a 'new' action on an entity.
     * create any entity.
     * if you create user entity ====>>>>> will create also user's directory
     * @return RedirectResponse|Response
     */
    protected function newAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_NEW);

        $entity = $this->executeDynamicMethod('createNew<EntityName>Entity');

        $easyadmin = $this->request->attributes->get('easyadmin');
        $easyadmin['item'] = $entity;
        $this->request->attributes->set('easyadmin', $easyadmin);

        $fields = $this->entity['new']['fields'];

        $newForm = $this->executeDynamicMethod('create<EntityName>NewForm', array($entity, $fields));

        $newForm->handleRequest($this->request);
        if ($newForm->isValid()) {
            $this->dispatch(EasyAdminEvents::PRE_PERSIST, array('entity' => $entity));

            $this->executeDynamicMethod('prePersist<EntityName>Entity', array($entity));
       
          //tester s'il s'agit d'une entité de l'utilisateur  (User)
            if ($this->entity['name']=='User'){
                 //appel méthode pour ajouter dossier de l'utilisateur
                     $entity->createUserDirectory();
                 //appel fonction pour creer fichier log de l'utilisateur
                      $entity->createUserFileLog();
            }
            $this->em->persist($entity);
            $this->em->flush();

            $this->dispatch(EasyAdminEvents::POST_PERSIST, array('entity' => $entity));

            $refererUrl = $this->request->query->get('referer', '');

            return !empty($refererUrl)
                ? $this->redirect(urldecode($refererUrl))
                : $this->redirect($this->generateUrl('easyadmin', array('action' => 'list', 'entity' => $this->entity['name'])));
        }

        $this->dispatch(EasyAdminEvents::POST_NEW, array(
            'entity_fields' => $fields,
            'form' => $newForm,
            'entity' => $entity,
        ));

        return $this->render($this->entity['templates']['new'], array(
            'form' => $newForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
        ));
    }
    
    
     /**
     * The method that is executed when the user performs a 'delete' action to
     * remove any entity.
     * if you delete user entity ====>>>>> will remove also user's directory
     * 
     * @return RedirectResponse
     */
    protected function deleteAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_DELETE);

        if ('DELETE' !== $this->request->getMethod()) {
            return $this->redirect($this->generateUrl('easyadmin', array('action' => 'list', 'entity' => $this->entity['name'])));
        }

        $id = $this->request->query->get('id');
        $form = $this->createDeleteForm($this->entity['name'], $id);
        $form->handleRequest($this->request);

        if ($form->isValid()) {
            $easyadmin = $this->request->attributes->get('easyadmin');
            $entity = $easyadmin['item'];

            $this->dispatch(EasyAdminEvents::PRE_REMOVE, array('entity' => $entity));

            $this->executeDynamicMethod('preRemove<EntityName>Entity', array($entity));
       
           //tester s'il s'agit d'une entité de l'utilisateur  (User)
            if ($this->entity['name']=='User'){
                 //appel méthode pour supprimer dossier de l'utilisateur
                   $entity->deleteUploadUserDir();
                }
            $this->em->remove($entity);
            $this->em->flush();

            $this->dispatch(EasyAdminEvents::POST_REMOVE, array('entity' => $entity));
        }

        $refererUrl = $this->request->query->get('referer', '');

        $this->dispatch(EasyAdminEvents::POST_DELETE);

        return !empty($refererUrl)
            ? $this->redirect(urldecode($refererUrl))
            : $this->redirect($this->generateUrl('easyadmin', array('action' => 'list', 'entity' => $this->entity['name'])));
    }
    
     private function executeDynamicMethod($methodNamePattern, array $arguments = array())
    {
        $methodName = str_replace('<EntityName>', $this->entity['name'], $methodNamePattern);
        if (!is_callable(array($this, $methodName))) {
            $methodName = str_replace('<EntityName>', '', $methodNamePattern);
        }

        return call_user_func_array(array($this, $methodName), $arguments);
    }

}