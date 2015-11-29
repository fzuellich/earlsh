<?php

namespace AppBundle\Twig;

use AppBundle\Lib\TokenConverter;

/**
 * Filter to convert integer into an token string. Invalid ids are converted to empty
 * strings.
 */
class ShortUrlTokenFilter extends \Twig_Extension {

	public function getFilters() {
		return array(
			new \Twig_SimpleFilter('shorturltoken', array($this, 'shortUrlTokenFilter'))
		);
	}

	/**
	 * Convert a given integer into a token. If id not valid returns an empty string and
	 * logs the exception.
	 * @param int $int Id to convert.
	 * @return string Either token string or empty string on exception.
	 */
	public function shortUrlTokenFilter($int) {
		try {
			return TokenConverter::convert_id_to_token($int);
		} catch(\InvalidArgumentException $e) {
			error_log($e->getMessage());
		}

		return '';
	}

	public function getName() {
		return 'short_url_token_filter';
	}
}

?>