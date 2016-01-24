<?php
namespace AppBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\ShortUrl;
use AppBundle\Lib\TokenConverter;
use AppBundle\Exception\UrlNotFoundException;
use AppBundle\Exception\TokenNotFoundException;
use AppBundle\Config\EarlshConfiguration;

/**
 * Class implements all the necessary functions to interact with the database to save URLs.
 */
class ShortUrlService {

	const SHORT_URL_MODEL = 'AppBundle:ShortUrl';

	private $em;
	private $config;

	public function __construct(ObjectManager $entityManager, EarlshConfiguration $config) {
		$this->em = $entityManager;
		$this->config = $config;
	}

	public function is_local_url($url) {
		$regex = sprintf('#%s/r#i', $this->config->getHostname());
		return preg_match($regex, $url) === 1;
	}

	/**
	 * Return true if the given URL is valid according to PHPs filter function. If not False
	 * is returned. This method throws no exceptions. Therefore empty URLs are also considered
	 * false.
	 * @param String $url URL to check.
	 * @return boolean true if valid URL.
	 */
	public function is_valid_url($url) {
		if(!is_string($url) || empty($url)) {
			return false;
		}

		if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) === false) {
			return false;
		}

		if($this->config->isPreventLocalUrls() && $this->is_local_url($url) === true) {
			return false;
		}

		// we are sure that the current url can only be valid or a rejected site
		$rejected_sites = $this->config->getRejectedSites();
		if(!empty($rejected_sites)) {
			foreach ($rejected_sites as $site) {
				$match_result = preg_match($site, $url);
				if ($match_result === 1) {
					return false;
				} else if($match_result === false) {
					error_log('Error when using regex <'.$site.'>!');
				}
			}
		}

		return true;
	}

	/**
	 * Check if the given URL is in the database.
	 * @param String $url Url to find in the database.
	 * @return boolean True if found.
	 * @throws InvalidArgumentException if invalid URL is given (empty, invalid syntax).
	 */
	public function is_url_in_database($url) {
		if(!is_string($url) || empty($url) || !$this->is_valid_url($url)) {
			throw new \InvalidArgumentException('Invalid URL given!');
		}

		$entity = $this->em->getRepository(self::SHORT_URL_MODEL)->findOneByUrl($url);
		return $entity != False;
	}

	/**
	 * Get the token from the database for the given url.
	 * @param String $url Url to obtain a token for.
	 * @return String Token for the primary key in the database.
	 * @throws InvalidArgumentExcetion if invalid URL. See is_url_in_database().
	 * @throws UrlNotFoundException if URL is not to be found.
	 */
	public function get_token_for_url($url) {
		if(!$this->is_url_in_database($url)) {
			throw new UrlNotFoundException('Could not find the URL!');
		}

		$shortUrl = $this->em->getRepository(self::SHORT_URL_MODEL)->findOneByUrl($url);
		if($shortUrl) {
			$id = $shortUrl->getId();
			return TokenConverter::convert_id_to_token($id);
		}
	}

	/**
	 * Add the given url to the database and return a token to access it again. If the url
	 * is already in database the method will just return the existing token.
	 * @param String $url Url to shorten.
	 * @return String Token as shortened url.
	 */
	public function shorten_url($url) {
		if(!is_string($url) || empty($url) || !$this->is_valid_url($url)) {
			throw new \InvalidArgumentException('Invalid URL given!');
		}

		if($this->is_url_in_database($url)) {
			return $this->get_token_for_url($url);
		}

		// save the url
		$shortUrl = new ShortUrl();
		$shortUrl->setUrl($url);

		$this->em->persist($shortUrl);
		$this->em->flush();

		// create a token and return it
		$token = TokenConverter::convert_id_to_token($shortUrl->getId());
		return $token;
	}

	public function resolve_token($token) {
		if(!is_string($token) || empty($token)) {
			throw new \InvalidArgumentException('Invalid token given!');
		}

		// conver token to id
		$url_id = TokenConverter::convert_token_to_id($token);

		// search the url with the id
		$shortUrl = $this->em->getRepository(self::SHORT_URL_MODEL)->findOneById($url_id);
		if($shortUrl) {
			return $shortUrl->getUrl();
		} else {
			throw new TokenNotFoundException('Token was not found.');
		}
	}
}

?>