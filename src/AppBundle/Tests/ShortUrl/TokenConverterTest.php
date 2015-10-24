<?php
namespace AppBundle\Tests\Lib;

use AppBundle\ShortUrl\TokenConverter;

class TokenConverterTest extends \PHPUnit_Framework_TestCase {

	// ////////////////////////////////////////////////////////////////////////
	//	TokenConverter::convert_id_to_token()
	// ////////////////////////////////////////////////////////////////////////

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_conver_id_to_token_invalid_input() {
		TokenConverter::convert_id_to_token(-123);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_conver_id_to_token_zero_input() {
		TokenConverter::convert_id_to_token(0);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_conver_id_to_token_invalid_datatype() {
		TokenConverter::convert_id_to_token('123');
	}

	public function test_conver_id_to_token_normal_int() {
		$expectation = array(12345 => 'dnh', 1073741824 => 'bkPtsG', 458330 => 'b5oA');
		foreach($expectation as $key => $value) {
			$result = TokenConverter::convert_id_to_token($key);
			$this->assertTrue(is_string($result));
			$this->assertEquals($value, $result);
		}
	}

	// ////////////////////////////////////////////////////////////////////////
	//	TokenConverter::convert_token_to_id()
	// ////////////////////////////////////////////////////////////////////////

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_convert_token_to_id_empty_string() {
		TokenConverter::convert_token_to_id('');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_convert_token_to_id_invalid_datatype() {
		TokenConverter::convert_token_to_id(null);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_convert_token_to_id_invalid_character() {
		TokenConverter::convert_token_to_id('abc123EDÖ');
	}

	public function test_convert_token_to_id() {
		$expectation = array('dnh' => 12345, 'bkPtsG' => 1073741824, 'b5oA' => 458330);
		foreach ($expectation as $key => $value) {
			$result = TokenConverter::convert_token_to_id($key);
			$this->assertTrue(is_int($result));
			$this->assertEquals($value, $result);
		}
	}
}

?>