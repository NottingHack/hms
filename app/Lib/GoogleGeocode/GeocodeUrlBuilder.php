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
 * Class to build geocode urls
 * https://developers.google.com/maps/documentation/geocoding
 */
class GeocodeUrlBuilder {

/**
 * Given an array of data, return a string representing the URL to request
 * @param string[] $address An array of address data.
 * @param string $apiKey Optional api key to use in the request.
 * @return string The URL to query.
 * @throws GeocodeException If $address is invalid.
 */
	public function buildAddressCheckUrl($address, $apiKey = '') {
		if (!is_array($address)) {
			throw new GeocodeException('$address must be an array');
		}
		$joinedAddress = join($address, ' ');
		$baseUrl = 'https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=';
		$urlWithAddress = $baseUrl .= urlencode($joinedAddress);
		$finalUrl = $urlWithAddress;

		if (isset($apiKey)) {
			$apiKeyPart = '&key=' . $apiKey;
			$finalUrl = $urlWithAddress . $apiKeyPart;
		}

		return $finalUrl;
	}
}