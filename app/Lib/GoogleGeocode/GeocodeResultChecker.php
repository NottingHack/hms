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
 * @package       app.Lib.GoogleGeocode
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

require_once (dirname(__FILE__) . '/Error/Exception/GeocodeException.php');

/**
 * Class to fetch geocode urls
 * https://developers.google.com/maps/documentation/geocoding
 */
class GeocodeResultChecker {

/**
 * Given an array of data, return the status part converted to a bool.
 * @param array $data An array of decoded json data.
 * @return bool True if status indicates address is found, false if address is not found.
 * @throws GeocodeException If status indicates an error or status cannot be determined.
 */
	public function checkJsonResult($data) {
		if (!is_array($data)) {
			throw new GeocodeException('$data must be an array');
		}
		if (array_key_exists('status', $data)) {
			$status = $data['status'];
			switch (strtolower($status)) {
				case 'ok':
					return true;

				case 'zero_results':
					return false;

				default:
					throw new GeocodeException('Status-code error: ' . $status);
			}
		}

		$encodedJson = json_encode($data);
		throw new GeocodeException('Json is invalid: ' . $encodedJson);
	}
}