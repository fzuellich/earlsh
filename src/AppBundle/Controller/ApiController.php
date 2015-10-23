<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

use AppBundle\Entity\Entry;

class ApiController extends Controller {
	/**
	 * @Route("/api")
	 */
	public function welcomeAction() {
		$version = $this->container->getParameter('application.version');

		$applciation_info = array(
			'application' => 'earlsh',
			'version' => $version
		);

		return new JsonResponse($applciation_info);
	}

	/**
	 * @Route("/api/url/create")
	 * @Method("PUT")
	 */
	public function shortenUrlAction($url) {
	}

	/**
	 * @Route("/api/url/resolve/{token}")
	 * @Method("GET")
	 */
	public function resolveTokenAction($token) {
		$entry = $this->getDoctrine()->getRepository('AppBundle:Entry')->find($token);

		if(!$entry) {
			$response = new JsonResponse(array('url' => null), JsonResponse::HTTP_NOT_FOUND);
		} else {
			$response = new JsonResponse(array('url' => $entry->getUrl()));
		}

		return $response;
	}
}
?>