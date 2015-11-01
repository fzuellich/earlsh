<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\Tools\Pagination\Paginator;

use AppBundle\Entity\ShortUrl;

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
}

?>