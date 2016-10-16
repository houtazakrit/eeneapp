<?php 
namespace EENE\ExtractionBundle\Entity;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * @ORM\Table(name="textFile")
 * @ORM\Entity(repositoryClass="EENE\ExtractionBundle\Entity\Repository\TextFileRepository")
 */
class TextFile
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
    private $textFile;

    /**
    * @ORM\ManyToOne(targetEntity="\EENE\UserBundle\Entity\User", inversedBy="userTextFiles", cascade={"persist"})
    * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
    */
    private $user;
    /**
     * @ORM\OneToMany(targetEntity="\EENE\ExtractionBundle\Entity\NerdFile", mappedBy="textFile",cascade={"persist"})
     */
    private $textNerdFiles;
    
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
     * @return TextFile
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
     * @return TextFile
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
     * @return TextFile
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
        $this->textNerdFiles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add textNerdFile
     *
     * @param \EENE\ExtractionBundle\Entity\NerdFile $textNerdFile
     *
     * @return TextFile
     */
    public function addTextNerdFile(\EENE\ExtractionBundle\Entity\NerdFile $textNerdFile)
    {
        $this->textNerdFiles[] = $textNerdFile;

        return $this;
    }

    /**
     * Remove textNerdFile
     *
     * @param \EENE\ExtractionBundle\Entity\NerdFile $textNerdFile
     */
    public function removeTextNerdFile(\EENE\ExtractionBundle\Entity\NerdFile $textNerdFile)
    {
        $this->textNerdFiles->removeElement($textNerdFile);
    }

    /**
     * Get textNerdFiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTextNerdFiles()
    {
        return $this->textNerdFiles;
    }
    
    
    /*
    * supprimer le text file en serveur
    */
     public function deleteTextFile() {
        $fs = new Filesystem();
         /*
         * Vérifier que le fichier Text existe
         */
         if($fs->exists($this->getUser()->getUploadUserDir().'/'.$this->getName())){
             //supprimer le fichier nerd de serveur
            unlink($this->getUser()->getUploadUserDir().'/'.$this->getName());
         }
        
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return TextFile
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
}
