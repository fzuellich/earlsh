<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\ShortUrl;
use AppBundle\Exception\InvalidTokenException;
use AppBundle\Exception\TokenNotFoundException;

class ApiController extends Controller {

	/**
	 * @Route("/api")
	 */
	public function welcomeAction() {
		$version = $this->container->getParameter('application.version');

		$application_info = array(
			'application' => 'earlsh',
			'version' => $version
		);

		return new JsonResponse($application_info);
	}

	/**
	 * @Route("/api/url/create")
	 * @Method("POST")
	 */
	public function createShortUrlAction(Request $request) {
		$response = null;

		$request_content = json_decode($request->getContent(), true);
		if(array_key_exists('url', $request_content) === false || empty(trim($request_content['url']))) {
			return new JsonResponse(array(
				'exception' => 'InvalidArgumentException'
				, 'message' => 'URL parameter not found.'
			), 400);
		}

		$url = $request_content['url'];

		try {
			$service = $this->container->get('short_url_service');
			$tag = $service->shorten_url($url);
			$response = new JsonResponse(array('token' => $tag));
		} catch (\InvalidArgumentException $e) {
			$response = new JsonResponse(array(
				'exception' => 'InvalidArgumentException'
				, 'message' => $e->getMessage()
			), 400);
		}

		return $response;
	}

	/**
	 * @Route("/api/url/resolve/{token}")
	 * @Method("GET")
	 */
	public function resolveTokenAction($token) {
		$response = null;

		try {
			$service = $this->get('short_url_service');
			$url = $service->resolve_token($token);
			$response = new JsonResponse(array('url' => $url));
		} catch (TokenNotFoundException $e) {
			$response = new JsonResponse(array(
				'exception' => 'TokenNotFoundException'
				, 'message' => $e->getMessage())
			, 404);
		} catch(InvalidTokenException $invalid) {
			$response = new JsonResponse(array(
				'exception' => 'InvalidTokenException'
				, 'message' => $invalid->getMessage())
			, 400);
		}

		return $response;
	}
}
?>