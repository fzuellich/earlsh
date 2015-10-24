<?php
namespace AppBundle\Lib;
/**
 * Class implementing the algorithm to convert between token and integer (id from database).
 *
 * Following the article:
 * http://www.geeksforgeeks.org/how-to-design-a-tiny-url-or-url-shortener/
 */
class TokenConverter {

	// ////////////////////////////////////////////////////////////////////////
	// Constants
	// ////////////////////////////////////////////////////////////////////////

	// all the allowed characters to constitute a token
	const ALLOWED_CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	// length of ALLOWED_CHARS used for conversion
	const BASE = 62;

	// ////////////////////////////////////////////////////////////////////////
	// Static methods
	// ////////////////////////////////////////////////////////////////////////

	/**
	 * Convert the given integer into a token.
	 * @param int Integer to convert.
	 * @return String A unique token for the integer.
	 */
	public static function convert_id_to_token($id) {
		if(!is_int($id)) {
			throw new \InvalidArgumentException('Argument id has to be of type integer.');
		} elseif($id <= 0) {
			throw new \InvalidArgumentException('Id can not be <= 0.');
		}

		$token = '';
		while($id > 0) {
			$token .= substr(self::ALLOWED_CHARS, ($id % self::BASE), 1);
			$id = intval($id / self::BASE); // make sure we get an integer
		}

		return strrev($token);
	}

	/**
	 * Convert a token back into its integer.
	 * @param String $token Token to convert into an integer.
	 * @return int Id that is the base for the token.
	 * @throws InvalidArgumentException When token not of type string, empty string or
	 *         contains invalid characters.
	 */
	public static function convert_token_to_id($token) {
		if(!is_string($token)) {
			throw new \InvalidArgumentException('Argument token has to be of type string.');
		} elseif(empty($token)) {
			throw new \InvalidArgumentException('Argument token can not be empty.');
		}

		$id = 0;
		for($i = 0; $i < strlen($token); $i++) {
			$character = substr($token, $i, 1);
			$position = strpos(self::ALLOWED_CHARS, $character);

			if($position === False) {
				throw new \InvalidArgumentException('Invalid character ´'.$character.'´.');
			}

			$id = intval($id * self::BASE + $position);
		}

		return $id;
	}
}
?>