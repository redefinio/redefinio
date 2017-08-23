<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Entity\CV;
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

    public function initializeCv(User $user, int $templateId) {
        $template = $this->em->getRepository('AppBundle:Template')->findOneById($templateId);
        $theme = $this->em->getRepository('AppBundle:Theme')->findOneById(1);

        $cv = new CV();
        $cv->setUser($user);
        $cv->setTemplate($template);
        $cv->setTheme($theme);
        $cv->setUrl("some_url");

        $this->initializeTemplate($cv, $template);

//        $this->em->persist($cv);
//        $this->em->flush();

    }

    private function initializeTemplate($cv, $template) {

    }
}