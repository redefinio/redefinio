<?php
/**
 * Created by PhpStorm.
 * User: svidleo
 * Date: 03/08/2017
 * Time: 11:23
 */

namespace AppBundle\Controller;

use AppBundle\Service\CvService;
use Http\Discovery\Exception\NotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $response = new Response();
        $identifier = $request->get('identifier');

        $html = $this->get(CvService::class)->getPublicLinkHtml($identifier);

        if (is_null($html)) {
            throw new NotFoundHttpException();
        } else {
            $response->setContent($html);
        }

        return $response;
    }


    /**
     * @Route("/footer", name="default_footer")
     */
    public function footerHtml(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $cv = $this->get(CvService::class)->getUserCv($this->getUser());
        $service = $this->get(CvService::class);

        $template = $cv->getTemplate();
        $theme = $service->getRelations($this->getUser())->getTheme();

        $renderTempalte = "templates/footer_".$template->getTemplatePath().".twig";

        return $this->render($renderTempalte, array("theme" => $theme));
    }
}