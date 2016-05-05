<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Skill
 *
 * @ORM\Table(name="Skill")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SkillRepository")
 */
class Skill
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
     * @ORM\ManyToOne(targetEntity="cv", inversedBy="skills")
     * @ORM\JoinColumn(name="cv_id", referencedColumnName="id")
     */
    protected $cv;
   
    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=255)
     */
    private $skill;

    /**    
     * @var integer
     *
     * @ORM\Column(name="rate", type="integer")
     * @Assert\Range(
     *      min = 1,
     *      max = 100,
     *      minMessage = "Your skill must be at least {{ limit }} to enter",
     *      maxMessage = "Your skill cannot be bigger than {{ limit }} to enter"
     * )
     */
    private $rate;

    public function __construct() {

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
     * Set skill
     *
     * @param string $skill
     *
     * @return Skill
     */
    public function setSkill($skill)
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * Get skill
     *
     * @return string
     */
    public function getSkill()
    {
        return $this->skill;
    }

    /**
     * Set rate
     *
     * @param integer $rate
     *
     * @return Skill
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return integer
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set cv
     *
     * @param \AppBundle\Entity\cv $cv
     *
     * @return Skill
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
