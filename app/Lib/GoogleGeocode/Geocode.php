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

require_once (dirname(__FILE__) . './GeocodeDataFetcher.php');
require_once (dirname(__FILE__) . './GeocodeResultDecoder.php');
require_once (dirname(__FILE__) . './GeocodeResultChecker.php');
require_once (dirname(__FILE__) . './GeocodeUrlBuilder.php');

/**
 * Class that wrapps Googles Geocoding service
 * https://developers.google.com/maps/documentation/geocoding
 */
class Geocode {

/**
 * String containing the API key we should use for requests.
 * @var string
 */
	private $__apiKey;

/**
 * UrlBuilder to build our valid urls.
 * @var GeocodeUrlBuilder
 */
	private $__urlBuilder;

/**
 * ResultDecoder to serialise the data from the url into a json array.
 * @var GeocodeResultDecoder
 */
	private $__resultDecoder;

/**
 * ResultChecker To convert the json array into something meaningful
 * @var GeocodeResultChecker
 */
	private $__resultChecker;

/**
 * Constructor.
 * @param GeocodeUrlBuilder $urlBuilder The url builder to use, if null use the default.
 * @param GeocodeResultDecoder $resultDecoder The result decoder to use, if null use the default.
 * @param GeocodeResultChecker $resultChecker The result checker to use, if null use the default.
 */
	public function __construct($urlBuilder = null, $resultDecoder = null, $resultChecker = null) {
		try {
			Configure::load('geocode');
			$this->__apiKey = Configure::read('apikey');
		} catch(ConfigureException $e) {
			// Just use no key
			$this->__apiKey = null;
		}

		$this->__urlBuilder = $urlBuilder;
		if (!isset($this->__urlBuilder)) {
			$this->__urlBuilder = new GeocodeUrlBuilder();
		}

		$this->__resultDecoder = $resultDecoder;
		if (!isset($this->__resultDecoder)) {
			$this->__resultDecoder = new GeocodeResultDecoder();
		}

		$this->__resultChecker = $resultChecker;
		if (!isset($this->__resultChecker)) {
			$this->__resultChecker = new GeocodeResultChecker();
		}
	}

/**
 * Query the geocode service to check the validity of an address.
 * @param string[] $address An array of address data.
 * @return bool True if address exists, false if it does not.
 * @throws GeocodeException If any error occurs.
 */
	public function checkAddress($address) {
		$url = $this->__urlBuilder->buildAddressCheckUrl($address, $this->__apiKey);
		$data = $this->__resultDecoder->decodeJsonUrl($url, new GeocodeDataFetcher());
		return $this->__resultChecker->checkJsonResult($data);
	}
}