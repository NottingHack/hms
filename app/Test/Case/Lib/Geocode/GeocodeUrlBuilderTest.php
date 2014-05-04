<?php

App::uses('GeocodeUrlBuilder', 'Lib/GoogleGeocode');

class GeocodeUrlBuilderTest extends CakeTestCase {

	private $__urlBuilder;

	public function setUp() {
		parent::setUp();
		$this->__urlBuilder = new GeocodeUrlBuilder();
	}

/**
 * @expectedException GeocodeException
 * @expectedExceptionMessage $address must be an array
 */
	public function test_BuildAddressCheckUrl_WithNull_ThrowsGeocodeException() {
		$this->__urlBuilder->buildAddressCheckUrl(null);
	}

/**
 * @expectedException GeocodeException
 * @expectedExceptionMessage $address must be an array
 */
	public function test_BuildAddressCheckUrl_WithString_ThrowsGeocodeException() {
		$this->__urlBuilder->buildAddressCheckUrl('string');
	}

	public function test_BuildAddressCheckUrl_WithSingleLineAddress_ReturnsUrlEncodedStringContainingLine() {
		$address = array(
			'53 Fake Street',
		);

		$result = $this->__urlBuilder->buildAddressCheckUrl($address);

		$this->assertTextContains('53+Fake+Street', $result);
	}

	public function test_BuildAddressCheckUrl_WithMultiLineAddress_ReturnsUrlEncodedStringContainingAllLines() {
		$address = array(
			'53 Fake Street',
			'Faketon',
			'Not a country',
			'FA42 3KE',
		);

		$result = $this->__urlBuilder->buildAddressCheckUrl($address);

		$this->assertTextContains('53+Fake+Street+Faketon+Not+a+country+FA42+3KE', $result);
	}

	public function test_BuildAddressCheckUrl_WithApiKey_ReturnsUrlEncodedStringContainingApiKey() {
		$address = array(
			'53 Fake Street',
		);
		$key = '123456789';
		$result = $this->__urlBuilder->buildAddressCheckUrl($address, $key);

		$this->assertTextContains('123456789', $result);
	}
}