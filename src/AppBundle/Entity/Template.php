<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Template
 *
 * @ORM\Table(name="template")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TemplateRepository")
 */
class Template
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
     * @ORM\OneToMany(targetEntity="CV", mappedBy="template")
     */
    private $cvs;

    /**
     * @ORM\OneToMany(targetEntity="Theme", mappedBy="template")
     */
    private $themes;

    /**
     * @ORM\OneToMany(targetEntity="TemplateSlot", mappedBy="template")
     */
    private $template_slots;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var template_path Path to base template in public_html/templates directory.
     *
     * @ORM\Column(name="template_path", type="string", length=255)
     */
    private $templatePath;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    public function __construct() {
        $this->created_at = new \DateTime();
        $this->cvs = new ArrayCollection();
        $this->themes = new ArrayCollection();
        $this->template_slots = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Template
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
     * Set created_at
     *
     * @param \DateTime $created_at
     *
     * @return Template
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
     * Add cv
     *
     * @param \AppBundle\Entity\CV $cv
     *
     * @return Template
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

    /**
     * Add theme
     *
     * @param \AppBundle\Entity\Theme $theme
     *
     * @return Template
     */
    public function addTheme(\AppBundle\Entity\Theme $theme)
    {
        $this->themes[] = $theme;

        return $this;
    }

    /**
     * Remove theme
     *
     * @param \AppBundle\Entity\Theme $theme
     */
    public function removeTheme(\AppBundle\Entity\Theme $theme)
    {
        $this->themes->removeElement($theme);
    }

    /**
     * Get themes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * Add templateSlot
     *
     * @param \AppBundle\Entity\TemplateSlot $templateSlot
     *
     * @return Template
     */
    public function addTemplateSlot(\AppBundle\Entity\TemplateSlot $templateSlot)
    {
        $this->template_slots[] = $templateSlot;

        return $this;
    }

    /**
     * Remove templateSlot
     *
     * @param \AppBundle\Entity\TemplateSlot $templateSlot
     */
    public function removeTemplateSlot(\AppBundle\Entity\TemplateSlot $templateSlot)
    {
        $this->template_slots->removeElement($templateSlot);
    }

    /**
     * Get templateSlots
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTemplateSlots()
    {
        return $this->template_slots;
    }


    /**
     * Set templatePath
     *
     * @param string $templatePath
     *
     * @return Template
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    /**
     * Get templatePath
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }
}
