<?php

namespace AppBundle\Twig;

use AppBundle\ShortUrl\TokenConverter;

class ShortUrlTokenFilter extends \Twig_Extension {

	public function getFilters() {
		return array(
			new \Twig_SimpleFilter('shorturltoken', array($this, 'shortUrlTokenFilter'))
		);
	}

	public function shortUrlTokenFilter($int) {
		return TokenConverter::convert_id_to_token($int);
	}

	public function getName() {
		return 'short_url_token_filter';
	}
}

?>