<?php
/**
 *
 * PHP 5
 *
 * Copyright (C) HMS Team
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     HMS Team
 * @package       dev.Setup.Common
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Generate valid garbage email addresses.
 */
class EmailGenerator {

/**
 * Minimum length an e-mail can be.
 */
	const MIN_LENGTH = 4;

/**
 * Maximum length an e-mail can be.
 */
	const MAX_LENGTH = 12;

/**
 * List of domains to choose from.
 * @var array
 */
	private $__domains = array(
		'foo.org',
		'example.com',
		'bar.net',
		'loa.fr',
		'bmail.co.uk',
	);

/**
 * String of characters to use in the e-mail address.
 * @var string
 */
	private $__chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

/**
 * Generate a garbage e-mail address.
 * @return string A random garbage e-mail address.
 */
	public function generate() {
		$numCharsInEmail = rand(self::MIN_LENGTH, self::MAX_LENGTH);

		$email = '';
		for ($j = 0; $j < $numCharsInEmail; $j++) {
			$charIdx = rand(0, strlen($this->__chars));
			$email .= substr($this->__chars, $charIdx, 1);
		}

		$email .= '@';
		$email .= $this->__domains[array_rand($this->__domains)];

		return $email;
	}
}