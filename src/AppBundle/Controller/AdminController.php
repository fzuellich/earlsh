<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\Tools\Pagination\Paginator;

use AppBundle\Entity\ShortUrl;
use AppBundle\Entity\Apikey;

class AdminController extends Controller {

	const RESULT_SIZE = 100;

	/**
	 * Maybe extend the Doctrine EntityRepository and create an own method to retrieve
	 * the entities from the database. This might help encapsulate the below query.
	 *
	 * The method returns all short urls from the database paginated.
	 *
	 * @return Doctrine\ORM\Tools\Pagination\Paginator A paginator instance that can be
	 * iterated over with a simple foreach (also in a twig template).
	 */
	private function findAllShortUrlsPaginated($offset=0) {
		$em = $this->getDoctrine()->getManager();

		// create query
		$qb = $em->createQueryBuilder();
		$query = $qb->select('url')
					->from('AppBundle:ShortUrl', 'url')
					->setFirstResult($offset)
					->setMaxResults(self::RESULT_SIZE);

		// feed to the paginator
		$paginator = new Paginator($query, $fetchJoinCollection=false);
		return $paginator;
	}

	/**
	 * @Route("/admin/list/{page}", name="admin_list")
	 */
	public function listShorturls($page=1) {
		if($page <= 0) {
			throw new \InvalidArgumentException('Page parameter should be >= 1!');
		}

		$urls = $this->findAllShortUrlsPaginated(($page-1) * self::RESULT_SIZE);
		return $this->render('admin_list.html.twig', array('urls' => $urls));
	}

	/**
	 * @Route("/admin/remove/{shorturlid}", name="admin_rm_shorturl")
	 */
	public function removeShorturl($shorturlid) {
		$em = $this->getDoctrine()->getManager();

		$shorturl =	$em->getRepository('AppBundle:ShortUrl')->findOneById($shorturlid);
		if(!$shorturl) {
			throw $this->createNotFoundException('The shorturl with id ´'.$shorturlid.'´ could not be found.');
		}

		$em->remove($shorturl);
		$em->flush();

		$this->addFlash('info', 'The url was removed!');
		return $this->redirectToRoute('admin_list');
	}

	/**
	 * @Route("/admin/apikeys/generate", name="admin_generate_apikeys")
	 * @Method({"GET", "POST"})
	 */
	public function generateApikey(Request $request) {
		$apikey_service = $this->get('apikey_service');
		$apikey = new Apikey($apikey_service->generate_apikey());

		$form = $this->createFormBuilder($apikey)

			->add('comment', 'text')
			->add('expires', 'date', array('widget' => 'single_text', 'format' => 'dd.MM.yyyy'))
			->add('save', 'submit', array('label' => 'Generate'))
			->getForm();

		$form->handleRequest($request);
		$method = $request->getMethod();
		if($method === 'POST' && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($apikey);
			$em->flush();

			return $this->redirectToRoute('apikey_details',
				array('apikey' => $apikey->getApikey()));
		}

		return $this->render('admin/apikeys_generate.html.twig',
			array('generator_form' => $form->createView()));
	}

	/**
	 * @Route("/admin/apikeys/{apikey}", name="apikey_details")
	 */
	public function apikeyDetails($apikey) {
		$em = $this->getDoctrine()->getManager();
		$apikey_instance = $em->getRepository('AppBundle:Apikey')->findOneByApikey($apikey);
		return $this->render('admin/apikey_details.html.twig',
			array('apikey' => $apikey_instance));
	}

}

?>