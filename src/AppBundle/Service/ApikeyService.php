<?php
namespace AppBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
/**
 * Central interface to generate and verify api keys.
 */
class ApikeyService {

	const APIKEY_MODEL = 'AppBundle:Apikey';

	private $em;

	public function __construct(ObjectManager $entityManager) {
		$this->em = $entityManager;
	}

	public function verify_apikey($apikey) {
		if(is_null($apikey) === True
			|| empty($apikey) === True
			|| is_string($apikey) === False) {
			return False;
		}

		$instance = $this->em->getRepository(self::APIKEY_MODEL)->findOneByApikey($apikey);
		$now = new \DateTime();
		return $instance !== Null && $instance->getExpires() >= $now;
	}

	/**
	 * Generate a new api key based on UUIDv4. Taken from:
	 * http://php.net/manual/en/function.com-create-guid.php#99425
	 * @return string New api key containg of url save characters.
	 */
	public function generate_apikey() {
		return strtolower(sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535),
			mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479),
			mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535),
			mt_rand(0, 65535)));
	}

}
?>