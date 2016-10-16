<?php 
namespace EENE\ExtractionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
/**
 * @ORM\Table(name="nerdFile")
 * @ORM\Entity(repositoryClass="EENE\ExtractionBundle\Entity\Repository\NerdFileRepository")
 */
class NerdFile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    private $size;

    /**
    * @var datetime $createdAt
    * @ORM\Column(name="createdAt", type="datetime")
    */
    private $createdAt;

    /**
     * @var UploadedFile
     */
    private $nerdFile;
    

    /**
    * @ORM\ManyToOne(targetEntity="\EENE\UserBundle\Entity\User", inversedBy="userNerdFiles", cascade={"persist"})
    * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
    */
    private $user;
    
    /**
    * @ORM\ManyToOne(targetEntity="\EENE\ExtractionBundle\Entity\TextFile", inversedBy="textNerdFiles", cascade={"persist"})
    * @ORM\JoinColumn(nullable=true,onDelete="CASCADE")
    */
    private $textFile;
    
    /**
     * @ORM\OneToMany(targetEntity="\EENE\ExtractionBundle\Entity\Entity", mappedBy="nerdFile",cascade={"persist"})
     */
    protected $nerdFileEntities;
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return NerdFile
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return NerdFile
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }


    /**
     * Set user
     *
     * @param \EENE\UserBundle\Entity\User $user
     *
     * @return NerdFile
     */
    public function setUser(\EENE\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \EENE\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->nerdFileEntities = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add nerdFileEntity
     *
     * @param \EENE\ExtractionBundle\Entity\Entity $nerdFileEntity
     *
     * @return NerdFile
     */
    public function addNerdFileEntity(\EENE\ExtractionBundle\Entity\Entity $nerdFileEntity)
    {
        $this->nerdFileEntities[] = $nerdFileEntity;

        return $this;
    }

    /**
     * Remove nerdFileEntity
     *
     * @param \EENE\ExtractionBundle\Entity\Entity $nerdFileEntity
     */
    public function removeNerdFileEntity(\EENE\ExtractionBundle\Entity\Entity $nerdFileEntity)
    {
        $this->nerdFileEntities->removeElement($nerdFileEntity);
    }
   
    
    /**
     * Get nerdFileEntities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNerdFileEntities()
    {
        return $this->nerdFileEntities;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return NerdFile
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
   

    /**
     * Set textFile
     *
     * @param \EENE\ExtractionBundle\Entity\TextFile $textFile
     *
     * @return NerdFile
     */
    public function setTextFile(\EENE\ExtractionBundle\Entity\TextFile $textFile)
    {
        $this->textFile = $textFile;

        return $this;
    }

    /**
     * Get textFile
     *
     * @return \EENE\ExtractionBundle\Entity\TextFile
     */
    public function getTextFile()
    {
        return $this->textFile;
    }
    
       /**
         * toString
         * @return string
         */
    public function __toString() 
    {
      return $this->getName();
    }
    
    /*
    * supprimer le nerd file en serveur
    */
     public function deleteNerdFile() {
        $fs = new Filesystem();
         /*
         * Vérifier que le fichier nerd existe
         */
         if($fs->exists($this->getUser()->getUploadUserDir().'/'.$this->getName())){
             //supprimer le fichier nerd de serveur
            unlink($this->getUser()->getUploadUserDir().'/'.$this->getName());
         }
        
    }
    
}
