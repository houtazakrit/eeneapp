<?php
/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EENE\UserBundle\Doctrine;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManager as BaseUserManager;
use FOS\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Filesystem\Filesystem;
class UserManager extends BaseUserManager
{
    protected $objectManager;
    protected $class;
    protected $repository;
    /**
     * Constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param CanonicalizerInterface  $usernameCanonicalizer
     * @param CanonicalizerInterface  $emailCanonicalizer
     * @param ObjectManager           $om
     * @param string                  $class
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer, ObjectManager $om, $class)
    {
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer);
        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);
        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }
    
     /**
     * Returns an empty user instance
     *
     * @return UserInterface
     *
    public function createUser()
    {
       // $class = $this->getClass();
      ///  $user = $this->class;
      $user=parent::createUser();
        var_dump($user);
       $this->createUserDirectory();
        return $user;
    }
    
     
     public function createUserDirectory(){
        
        $fs = new Filesystem();
         /*
         * Vérifier que le dossier de tous les utilisateus existe car il peut avoir 
         * été modifié dans le fichier de configuration parameters.yml
         *
         if(!$fs->exists($this->getUploadFileDir())){
            //create directory if not exist
             $fs->mkdir($this->getUploadFileDir());
              }
              
        // Créer le dossier utilisateur s'il n'existe pas déjà
          if(!$fs->exists($this->getUploadUserDir())){
            //create directory if not exist
              $fs->mkdir($this->getUploadUserDir());
             }
    }*/
    /**
     * {@inheritDoc}
     */
    public function deleteUser(UserInterface $user)
    {
        $this->objectManager->remove($user);
        $this->objectManager->flush();
    }

}