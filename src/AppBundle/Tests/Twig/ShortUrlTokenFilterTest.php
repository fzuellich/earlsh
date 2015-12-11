<?php

namespace AppBundle\Tests\Twig;

use AppBundle\Twig\ShortUrlTokenFilter;

class ShortUrlTokenFilterTest extends \PHPUnit_Framework_TestCase {

	public function test_filter() {
		$instance = new ShortUrlTokenFilter();

		$expectation = array('dnh' => 12345, 'bkPtsG' => 1073741824, 'b5oA' => 458330);
		foreach ($expectation as $key => $value) {
			$this->assertEquals($key, $instance->shortUrlTokenFilter($value));
		}

		$this->assertEquals('', $instance->shortUrlTokenFilter(False));
		$this->assertEquals('', $instance->shortUrlTokenFilter(Null));

	}
}