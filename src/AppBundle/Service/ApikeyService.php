<?php
namespace AppBundle\Service;

/**
 * Central interface to generate and validate api keys.
 */
class ApikeyService {

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