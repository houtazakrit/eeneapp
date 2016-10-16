<?php 
namespace EENE\ExtractionBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;
/**
 * @ORM\Entity()
 * @ORM\Table(name="time")
  */
class Time
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
     * @var integer
     *
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    private $year;
     
    /**
     * @var integer
     *
     * @ORM\Column(name="month", type="integer", nullable=true)
     */
     private $month;
     
    /**
     * @var integer
     *
     * @ORM\Column(name="day", type="integer", nullable=true)
     */
     private $day;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="hour", type="integer", nullable=true)
     */
     private $hour;
     
      /**
     * @var integer
     *
     * @ORM\Column(name="minute", type="integer", nullable=true)
     */
   
     private $minute;
     
      /**
     * @var integer
     *
     * @ORM\Column(name="second", type="integer", nullable=true)
     */
     private $second;

     /**
     * @ORM\ManyToMany(targetEntity="\EENE\ExtractionBundle\Entity\Entity",  inversedBy="times", cascade={"persist"})
    *  @ORM\JoinTable(name="entities_times")
    */
    private $timeentities;    

   
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->timeentities = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set year
     *
     * @param integer $year
     *
     * @return Time
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set month
     *
     * @param integer $month
     *
     * @return Time
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return integer
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set day
     *
     * @param integer $day
     *
     * @return Time
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return integer
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set hour
     *
     * @param integer $hour
     *
     * @return Time
     */
    public function setHour($hour)
    {
        $this->hour = $hour;

        return $this;
    }

    /**
     * Get hour
     *
     * @return integer
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * Set minute
     *
     * @param integer $minute
     *
     * @return Time
     */
    public function setMinute($minute)
    {
        $this->minute = $minute;

        return $this;
    }

    /**
     * Get minute
     *
     * @return integer
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * Set second
     *
     * @param integer $second
     *
     * @return Time
     */
    public function setSecond($second)
    {
        $this->second = $second;

        return $this;
    }

    /**
     * Get second
     *
     * @return integer
     */
    public function getSecond()
    {
        return $this->second;
    }



    /**
     * Add timeentity
     *
     * @param \EENE\ExtractionBundle\Entity\Entity $timeentity
     *
     * @return Time
     */
    public function addTimeentity(\EENE\ExtractionBundle\Entity\Entity $timeentity)
    {
        $this->timeentities[] = $timeentity;

        return $this;
    }

    /**
     * Remove timeentity
     *
     * @param \EENE\ExtractionBundle\Entity\Entity $timeentity
     */
    public function removeTimeentity(\EENE\ExtractionBundle\Entity\Entity $timeentity)
    {
        $this->timeentities->removeElement($timeentity);
    }

    /**
     * Get timeentities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTimeentities()
    {
        return $this->timeentities;
    }
}
