<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="CV", mappedBy="user")
     */
    protected $cvs;

    public function __construct()
    {
        parent::__construct();
        $this->cvs = new ArrayCollection();
    }

    public function setEmail($email) {
        parent::setEmail($email);
        parent::setUsername($email);
        parent::setUsernameCanonical($email);
        $this->setEmailCanonical($email);
        $this->setUsername($email);
        $this->setUsernameCanonical($email);
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
     * Add cv
     *
     * @param \AppBundle\Entity\CV $cv
     *
     * @return User
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
