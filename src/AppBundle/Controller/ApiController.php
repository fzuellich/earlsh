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
	 * Verifies that the given request contains an apikey parameter and the given key is
	 * valid.
	 * @param Request $request As handed down by Symfony for routes.
	 * @return mixed True if valid key or creates a JSON response indicating the error.
	 */
	private function verifyApikey(Request $request) {
		$apikey_parameter = $request->get('apikey');
		$apikey_service = $this->get('apikey_service');

		$isValid = $apikey_service->verify_apikey($apikey_parameter);
		if($isValid === false) {
			$data = array('message' => 'Apikey not provided or invalid.');
			return new JsonResponse($data, JsonResponse::HTTP_UNAUTHORIZED);
		}

		return true;
	}

	/**
	 * @Route("/api")
	 */
	public function welcomeAction(Request $request) {
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
		$isValid = $this->verifyApikey($request);
		if($isValid !== true) {
			return $isValid;
		}

		$response = null;

		$url_parameter = $request->get('url');
		if($url_parameter === null || empty(trim($url_parameter))) {
			return new JsonResponse(array(
				'exception' => 'InvalidArgumentException'
				, 'message' => 'URL parameter not found.'
			), 400);
		}

		try {
			$service = $this->container->get('short_url_service');
			$tag = $service->shorten_url($url_parameter);
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