<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * CV
 *
 * @ORM\Table(name="cv")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CVRepository")
 */
class CV
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="cvs")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="cvs")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", nullable=false)
     */
    private $template;

    /**
     * @ORM\ManyToOne(targetEntity="Template")
     * @ORM\JoinColumn(name="public_template_id", referencedColumnName="id", nullable=false)
     */
    private $publicTemplate;

    /**
     * @ORM\ManyToOne(targetEntity="Theme", inversedBy="cvs")
     * @ORM\JoinColumn(name="theme_id", referencedColumnName="id", nullable=false)
     */
    private $theme;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, unique=true)
     */
    private $url;


    /**
     * @var String
     *
     * @ORM\Column(name="public_html", type="text", nullable=true)
     */
    private $publicHtml;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return mixed
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param mixed $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getPublicTemplate()
    {
        return $this->publicTemplate;
    }

    /**
     * @param mixed $publicTemplate
     */
    public function setPublicTemplate($publicTemplate)
    {
        $this->publicTemplate = $publicTemplate;
    }

    /**
     * @return mixed
     */
    public function getPublicHtml()
    {
        return $this->publicHtml;
    }

    /**
     * @param mixed $publicHtml
     */
    public function setPublicHtml($publicHtml)
    {
        $this->publicHtml = $publicHtml;
    }

}
