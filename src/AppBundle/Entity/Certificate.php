<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Certificate
 *
 * @ORM\Table(name="Certificate")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CertificateRepository")
 */
class Certificate
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="cv", inversedBy="certificates")
     * @ORM\JoinColumn(name="cv_id", referencedColumnName="id")
     */
    protected $cv;

    /**
     * @var datetime $date
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="certificator", type="string", length=255)
     */
    private $certificator;

    /**
     * @var string
     *
     * @ORM\Column(name="about", type="string", length=255)
     */
    private $about;

    public function __construct() {
        $this->date = new \DateTime();
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Certificate
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Certificate
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set certificator
     *
     * @param string $certificator
     *
     * @return Certificate
     */
    public function setCertificator($certificator)
    {
        $this->certificator = $certificator;

        return $this;
    }

    /**
     * Get certificator
     *
     * @return string
     */
    public function getCertificator()
    {
        return $this->certificator;
    }

    /**
     * Set about
     *
     * @param string $about
     *
     * @return Certificate
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set cv
     *
     * @param \AppBundle\Entity\cv $cv
     *
     * @return Certificate
     */
    public function setCv(\AppBundle\Entity\cv $cv = null)
    {
        $this->cv = $cv;

        return $this;
    }

    /**
     * Get cv
     *
     * @return \AppBundle\Entity\cv
     */
    public function getCv()
    {
        return $this->cv;
    }
}
