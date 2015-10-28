<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\ShortUrl\ShortUrlService;

class FrontendController extends Controller {

	/**
	 * @Route("/r/{token}")
	 */
	public function resolveAction($token) {
		$service = $this->get('short_url_service');

		$url = $service->resolve_token($token);
		return $this->redirect($url);
	}

	/**
	 * @Route("/")
	 * @Method({"GET", "POST"})
	 */
	public function shortenAction(Request $request) {
		$service = $this->get('short_url_service');


		$form = $this->createFormBuilder()
				->add('url', 'text')
				->add('save', 'submit', array('label' => 'Shorten'))->getForm();
		$form->handleRequest($request);

		$method = $request->getMethod();
		if($method === 'GET') {
			return $this->render('shorten.html.twig', array('shorten_form' => $form->createView()));
		} elseif($method === 'POST') {
			if($form->isValid()) {
				$service = $this->get('short_url_service');

				$url = $form->getData()['url'];
				$token = $service->shorten_url($url);
				return $this->render('shorten_result.html.twig', array('token' => $token));
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