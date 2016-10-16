<?php 
namespace EENE\ExtractionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;
/**
 * @ORM\Entity(repositoryClass="EENE\ExtractionBundle\Entity\Repository\EntityClassRepository")
 * @ORM\Table(name="entity")
  */
class Entity
{
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private  $id;
    
     /**
     *
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;
    
      /**
     *
     * @var string
     *
     * @ORM\Column(name="nerdType", type="string", length=255)
     */
     
     private $nerdType;

      /**
     *
     * @var string
     *
     * @ORM\Column(name="extractor", type="string", length=255)
     */
     
     private $extractor;
      /**
     *
     * @var string
     *
     * @ORM\Column(name="extractorType", type="string", length=255, nullable=true)
     */
    private $extractorType;
     

   /**
     *
     * @var string
     *
     * @ORM\Column(name="uri", type="string", length=255, nullable=true)
     */
     private $uri;
  
   /**
     *
     * @var float
     *
     * @ORM\Column(name="relevance", type="float")
     */
     
     private $relevance;
    /**
     * @var float
     *
     * @ORM\Column(name="confidence", type="float")
     */
     private $confidence;
     
         /**
     * @var integer
     *
     * @ORM\Column(name="startChar", type="integer")
     */
     private $startChar;
     
    /**
     * @var integer
     *
     * @ORM\Column(name="endChar", type="integer")
     */
     private $endChar;
     
    /**
     * @ORM\ManyToOne(targetEntity="\EENE\ExtractionBundle\Entity\NerdFile", inversedBy="nerdFileEntities", cascade={"persist","remove"})
    * @ORM\JoinColumn(nullable=false)
    */
    private $nerdFile;

    /**
     * @ORM\ManyToOne(targetEntity="\EENE\ExtractionBundle\Entity\Geolocation", inversedBy="geoEntities", cascade={"persist", "remove"})
    * @ORM\JoinColumn(nullable=true)
    */
    private $geolocation;
    
    /**
     * @ORM\ManyToMany(targetEntity="\EENE\ExtractionBundle\Entity\Time",  mappedBy="timeentities", cascade={"persist", "remove"})
    */
    private $times;

    /**
    * ORM\PreRemove
    */
    public function deleteAllChildren()
    {
         //prevent 
        // foreach ($this->getGeolocation() as $child) {
           // $this->geolocation->removeElement($geolocation);
           // $child->remove;
            $this->geolocation->removeGeoEntity($this);
        //}
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->times = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set label
     *
     * @param string $label
     *
     * @return Entity
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set nerdType
     *
     * @param string $nerdType
     *
     * @return Entity
     */
    public function setNerdType($nerdType)
    {
        $this->nerdType = $nerdType;

        return $this;
    }

    /**
     * Get nerdType
     *
     * @return string
     */
    public function getNerdType()
    {
        return $this->nerdType;
    }

    /**
     * Set extractor
     *
     * @param string $extractor
     *
     * @return Entity
     */
    public function setExtractor($extractor)
    {
        $this->extractor = $extractor;

        return $this;
    }

    /**
     * Get extractor
     *
     * @return string
     */
    public function getExtractor()
    {
        return $this->extractor;
    }

    /**
     * Set extractorType
     *
     * @param string $extractorType
     *
     * @return Entity
     */
    public function setExtractorType($extractorType)
    {
        $this->extractorType = $extractorType;

        return $this;
    }

    /**
     * Get extractorType
     *
     * @return string
     */
    public function getExtractorType()
    {
        return $this->extractorType;
    }

    /**
     * Set uri
     *
     * @param string $uri
     *
     * @return Entity
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set relevance
     *
     * @param float $relevance
     *
     * @return Entity
     */
    public function setRelevance($relevance)
    {
        $this->relevance = $relevance;

        return $this;
    }

    /**
     * Get relevance
     *
     * @return float
     */
    public function getRelevance()
    {
        return $this->relevance;
    }

    /**
     * Set confidence
     *
     * @param float $confidence
     *
     * @return Entity
     */
    public function setConfidence($confidence)
    {
        $this->confidence = $confidence;

        return $this;
    }

    /**
     * Get confidence
     *
     * @return float
     */
    public function getConfidence()
    {
        return $this->confidence;
    }

    /**
     * Set startChar
     *
     * @param integer $startChar
     *
     * @return Entity
     */
    public function setStartChar($startChar)
    {
        $this->startChar = $startChar;

        return $this;
    }

    /**
     * Get startChar
     *
     * @return integer
     */
    public function getStartChar()
    {
        return $this->startChar;
    }

    /**
     * Set endChar
     *
     * @param integer $endChar
     *
     * @return Entity
     */
    public function setEndChar($endChar)
    {
        $this->endChar = $endChar;

        return $this;
    }

    /**
     * Get endChar
     *
     * @return integer
     */
    public function getEndChar()
    {
        return $this->endChar;
    }

    /**
     * Set nerdFile
     *
     * @param \EENE\ExtractionBundle\Entity\NerdFile $nerdFile
     *
     * @return Entity
     */
    public function setNerdFile(\EENE\ExtractionBundle\Entity\NerdFile $nerdFile)
    {
        $this->nerdFile = $nerdFile;

        return $this;
    }

    /**
     * Get nerdFile
     *
     * @return \EENE\ExtractionBundle\Entity\NerdFile
     */
    public function getNerdFile()
    {
        return $this->nerdFile;
    }

    /**
     * Set geolocation
     *
     * @param \EENE\ExtractionBundle\Entity\Geolocation $geolocation
     *
     * @return Entity
     */
    public function setGeolocation(\EENE\ExtractionBundle\Entity\Geolocation $geolocation = null)
    {
        $this->geolocation = $geolocation;

        return $this;
    }

    /**
     * Get geolocation
     *
     * @return \EENE\ExtractionBundle\Entity\Geolocation
     */
    public function getGeolocation()
    {
        return $this->geolocation;
    }

    /**
     * Add time
     *
     * @param \EENE\ExtractionBundle\Entity\Time $time
     *
     * @return Entity
     */
    public function addTime(\EENE\ExtractionBundle\Entity\Time $time)
    {
        $this->times[] = $time;

        return $this;
    }

    /**
     * Remove time
     *
     * @param \EENE\ExtractionBundle\Entity\Time $time
     */
    public function removeTime(\EENE\ExtractionBundle\Entity\Time $time)
    {
        $this->times->removeElement($time);
    }

    /**
     * Get times
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTimes()
    {
        return $this->times;
    }
}
