<?php 
namespace EENE\ExtractionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;
/**
 * @ORM\Entity()
 * @ORM\Table(name="geolocation")
  */
class Geolocation
{
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected  $id;
    
    /**
     *@var GeometryInterface $geometry
     *
     * @ORM\Column(name="geometry", type="geometry", nullable=true)
     */
     
    private $geometry;
     
     /**
     * @var string
     * @ORM\Column(name="geolocatedBy", type="string", length=255, nullable=true)
     */
     
    private $geolocatedBy;
     
     /**
     * @ORM\OneToMany(targetEntity="\EENE\ExtractionBundle\Entity\Entity",  mappedBy="geolocation", cascade={"persist", "remove"})
    */
    private $geoEntities;    
 
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->geoEntities = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set geometry
     *
     * @param geometry $geometry
     *
     * @return Geolocation
     */
    public function setGeometry($geometry)
    {
        $this->geometry = $geometry;

        return $this;
    }

    /**
     * Get geometry
     *
     * @return geometry
     */
    public function getGeometry()
    {
        return $this->geometry;
    }

    /**
     * Set geolocatedBy
     *
     * @param string $geolocatedBy
     *
     * @return Geolocation
     */
    public function setGeolocatedBy($geolocatedBy)
    {
        $this->geolocatedBy = $geolocatedBy;

        return $this;
    }

    /**
     * Get geolocatedBy
     *
     * @return string
     */
    public function getGeolocatedBy()
    {
        return $this->geolocatedBy;
    }

    /**
     * Add geoEntity
     *
     * @param \EENE\ExtractionBundle\Entity\Entity $geoEntity
     *
     * @return Geolocation
     */
    public function addGeoEntity(\EENE\ExtractionBundle\Entity\Entity $geoEntity)
    {
        $this->geoEntities[] = $geoEntity;

        return $this;
    }

    /**
     * Remove geoEntity
     *
     * @param \EENE\ExtractionBundle\Entity\Entity $geoEntity
     */
    public function removeGeoEntity(\EENE\ExtractionBundle\Entity\Entity $geoEntity)
    {
        $this->geoEntities->removeElement($geoEntity);
    }

    /**
     * Get geoEntities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGeoEntities()
    {
        return $this->geoEntities;
    }
}
