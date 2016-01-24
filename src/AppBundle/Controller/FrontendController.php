<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Exception;
use AppBundle\Service\ShortUrlService;

class FrontendController extends Controller {

	/**
	 * @Route("/r/{token}", name="resolve")
	 */
	public function resolveAction($token) {
		$service = $this->get('short_url_service');

		try {
			$url = $service->resolve_token($token);
			return $this->redirect($url);
		} catch(Exception\TokenNotFoundException $e) {
			throw $this->createNotFoundException(sprintf("Token '%s' not found.", $token));
		}
	}

	/**
	 * @Route("/shorturl/{token}", name="shorten_result")
	 */
	public function showShortUrl($token) {
		return $this->render('shorten_result.html.twig', array('token' => $token));
	}

	/**
	 * @Route("/")
	 * @Method({"GET", "POST"})
	 */
	public function shortenAction(Request $request) {
		$service = $this->get('short_url_service');


		$form = $this->createFormBuilder()
				->add('url', 'url')
				->add('save', 'submit', array('label' => 'Go'))->getForm();
		$form->handleRequest($request);

		$method = $request->getMethod();
		if($method === 'GET') {
			return $this->render('shorten.html.twig', array('shorten_form' => $form->createView()));
		} elseif($method === 'POST') {
			$url = $form->getData()['url'];

			if($form->isValid()) {
				$service = $this->get('short_url_service');
				try {
					$token = $service->shorten_url($url);
					return $this->redirectToRoute('shorten_result', array('token' => $token));
				} catch(\Exception $e) {
					$form->get('url')->addError(new \Symfony\Component\Form\FormError($e->getMessage()));
				}
			}

			return $this->render('shorten.html.twig', array('shorten_form' => $form->createView()));
		}
	}

	/**
	 * @Route("/list")
	 */
	public function listAction() {
		$db = $this->getDoctrine()->getRepository('AppBundle:ShortUrl');
		$urls = $db->findAll();
		return $this->render('list.html.twig', array('urls' => $urls));
	}


}

?>
