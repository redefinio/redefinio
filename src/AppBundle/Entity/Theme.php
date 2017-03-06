<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Theme
 *
 * @ORM\Table(name="theme")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ThemeRepository")
 */
class Theme
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
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="themes")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    private $template;

    /**
     * @ORM\OneToMany(targetEntity="CV", mappedBy="theme")
     */
    private $cvs;

    /**
     * @var string
     *
     * @ORM\Column(name="background_color", type="string", length=6)
     */
    private $background_color;

    /**
     * @var string
     *
     * @ORM\Column(name="page_color", type="string", length=6)
     */
    private $page_color;

    /**
     * @var string
     *
     * @ORM\Column(name="title_color", type="string", length=6)
     */
    private $title_color;

    /**
     * @var string
     *
     * @ORM\Column(name="paragraph_color", type="string", length=6)
     */
    private $paragraph_color;

    /**
     * @var string
     *
     * @ORM\Column(name="primary_color", type="string", length=6)
     */
    private $primary_color;

    /**
     * @var css_source
     *
     * @ORM\Column(name="css_source", type="text")
     */
    private $css_source;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    public function __construct() {
        $this->created_at = new \DateTime();
        $this->cvs = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $created_at
     *
     * @return Theme
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set template
     *
     * @param \AppBundle\Entity\Template $template
     *
     * @return Theme
     */
    public function setTemplate(\AppBundle\Entity\Template $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return \AppBundle\Entity\Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set cssSource
     *
     * @param string $cssSource
     *
     * @return Theme
     */
    public function setCssSource($cssSource)
    {
        $this->css_source = $cssSource;

        return $this;
    }

    /**
     * Get cssSource
     *
     * @return string
     */
    public function getCssSource()
    {
        return $this->css_source;
    }

    /**
     * Set backgroundColor
     *
     * @param string $backgroundColor
     *
     * @return Theme
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->background_color = $backgroundColor;

        return $this;
    }

    /**
     * Get backgroundColor
     *
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->background_color;
    }

    /**
     * Set pageColor
     *
     * @param string $pageColor
     *
     * @return Theme
     */
    public function setPageColor($pageColor)
    {
        $this->page_color = $pageColor;

        return $this;
    }

    /**
     * Get pageColor
     *
     * @return string
     */
    public function getPageColor()
    {
        return $this->page_color;
    }

    /**
     * Set titleColor
     *
     * @param string $titleColor
     *
     * @return Theme
     */
    public function setTitleColor($titleColor)
    {
        $this->title_color = $titleColor;

        return $this;
    }

    /**
     * Get titleColor
     *
     * @return string
     */
    public function getTitleColor()
    {
        return $this->title_color;
    }

    /**
     * Set paragraphColor
     *
     * @param string $paragraphColor
     *
     * @return Theme
     */
    public function setParagraphColor($paragraphColor)
    {
        $this->paragraph_color = $paragraphColor;

        return $this;
    }

    /**
     * Get paragraphColor
     *
     * @return string
     */
    public function getParagraphColor()
    {
        return $this->paragraph_color;
    }

    /**
     * Set primaryColor
     *
     * @param string $primaryColor
     *
     * @return Theme
     */
    public function setPrimaryColor($primaryColor)
    {
        $this->primary_color = $primaryColor;

        return $this;
    }

    /**
     * Get primaryColor
     *
     * @return string
     */
    public function getPrimaryColor()
    {
        return $this->primary_color;
    }

    /**
     * Add cv
     *
     * @param \AppBundle\Entity\CV $cv
     *
     * @return Theme
     */
    public function addCv(\AppBundle\Entity\CV $cv)
    {
        $this->cvs[] = $cv;

        return $this;
    }

    /**
     * Remove cv
     *
     * @param \AppBundle\Entity\CV $cv
     */
    public function removeCv(\AppBundle\Entity\CV $cv)
    {
        $this->cvs->removeElement($cv);
    }

    /**
     * Get cvs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCvs()
    {
        return $this->cvs;
    }
}
