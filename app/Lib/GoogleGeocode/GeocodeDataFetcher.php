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

/**
 * Class to fetch geocode urls
 * https://developers.google.com/maps/documentation/geocoding
 */
class GeocodeDataFetcher {

/**
 * Given an URL, return the contents as an array of JSON data.
 * @param string $url The URL to query.
 * @return mixed string containing the data at $url, or false on error
 */
	public function fetchUrl($url) {
		return file_get_contents($url);
	}
}