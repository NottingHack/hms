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
class GeocodeResultDecoder {

/**
 * Given an URL, return the contents as an array of JSON data.
 * @param string $url The URL to query.
 * @param GeocodeDataFetcher $fetcher The fetcher used to retrieve the contents of the URL.
 * @return array Array of JSON data.
 * @throws GeocodeException If data cannot be retrieved.
 */
	public function decodeJsonUrl($url, $fetcher) {
		if (!is_string($url)) {
			throw new GeocodeException('$url must be a string');
		}

		if (!isset($fetcher)) {
			throw new GeocodeException('$fetcher must must be set');
		}

		$fileData = $fetcher->fetchUrl($url);
		if ($fileData === false) {
			throw new GeocodeException('Unable to query url: ' . $url);
		}

		$jsonData = json_decode($fileData, true);
		if ($jsonData === null) {
			throw new GeocodeException('Unable to decode json: ' . $fileData);
		}
		return $jsonData;
	}
}