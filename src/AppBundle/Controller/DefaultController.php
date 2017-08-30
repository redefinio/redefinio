<?php
/**
 * Created by PhpStorm.
 * User: svidleo
 * Date: 03/08/2017
 * Time: 11:23
 */

namespace AppBundle\Controller;

use AppBundle\Service\CvService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->redirectToRoute('cv_index');
    }


    /**
     * @Route("/public/{identifier}", name="default_public_link")"
     */
    public function publicLink(Request $request) {
        $identifier = $request->get('identifier');

        return new Response($this->get(CvService::class)->getPublicHtml($this->getUser(), $identifier));
    }
}