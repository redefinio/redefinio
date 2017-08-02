<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

/**
 * CVService
 *
 * @Service for manipulating cv's.
 */
class CvService {

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getUserCv(User $user) {
        $userCV = $this->em->getRepository('AppBundle:CV')->findOneByUser($user);

        return $userCV;
    }
}