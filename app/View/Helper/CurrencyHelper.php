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
 * @package       app.View.Helper
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppHelper', 'View/Helper');

/**
 * A helper to display currency nicely.
 */
class CurrencyHelper extends AppHelper {

/**
 * Helpers this helper uses.
 * @var array
 */
	public $helpers = array('Number');

/**
 * Given a number in pence, output the value in GBP, with special styling if the amount is negative.
 * @param  int $pennys Amount in pence.
 * @return string The HTML required to render this amount.
 */
	public function output($pennys) {
		$output = '';
		$moneyStr = $this->Number->currency($pennys / 100, 'GBP', array( 'places' => 2, 'negative' => '-') );
		if ($pennys < 0) {
			$output = sprintf('<span class="currency_negative">%s</span>', $moneyStr);
		} else {
			$output = $moneyStr;
		}

		return $output;
	}
}