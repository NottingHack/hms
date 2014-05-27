<?php

App::uses('GeocodeResultDecoder', 'Lib/GoogleGeocode');
App::uses('GeodoceDataFetcher', 'Lib/GoogleGeocode');

class GeocodeResultDecoderTest extends CakeTestCase {

	private $__decoder;

	public function setUp() {
		parent::setUp();
		$this->__decoder = new GeocodeResultDecoder();
	}

	private function __getMockDataFetcher() {
		return $this->getMock('GeodoceDataFetcher', array('fetchUrl'));
	}

	private function __getMockDataFetcherThatReturns($value) {
		$mock = $this->__getMockDataFetcher();
		$mock->expects($this->any())
			->method('fetchUrl')
			->will($this->returnValue($value));

		return $mock;
	}

/**
 * @expectedException GeocodeException
 * @expectedExceptionMessage $url must be a string
 */
	public function test_DecodeJsonUrl_WithNullUrl_ThrowsGeocodeException() {
		$mockDataFetcher = $this->__getMockDataFetcher();
		$this->__decoder->decodeJsonUrl(null, $mockDataFetcher);
	}

/**
 * @expectedException GeocodeException
 * @expectedExceptionMessage $url must be a string
 */
	public function test_DecodeJsonUrl_WithIntUrl_ThrowsGeocodeException() {
		$mockDataFetcher = $this->__getMockDataFetcher();
		$this->__decoder->decodeJsonUrl(1, $mockDataFetcher);
	}

/**
 * @expectedException GeocodeException
 * @expectedExceptionMessage $fetcher must must be set
 */
	public function test_DecodeJsonUrl_WithNullFetcher_ThrowsGeocodeException() {
		$this->__decoder->decodeJsonUrl('', null);
	}

/**
 * @expectedException GeocodeException
 * @expectedExceptionMessage Unable to query url: http://example.com
 */
	public function test_DecodeJsonUrl_WithFetcherThatReturnsFalse_ThrowsGeocodeException() {
		$mockDataFetcher = $this->__getMockDataFetcherThatReturns(false);
		$this->__decoder->decodeJsonUrl('http://example.com', $mockDataFetcher);
	}

	public function test_DecodeJsonUrl_WithFetcherThatReturnsFalsyValue_ReturnsFalsyJson() {
		$mockDataFetcher = $this->__getMockDataFetcherThatReturns(0);

		$result = $this->__decoder->decodeJsonUrl('http://example.com', $mockDataFetcher);

		$this->assertIdentical($result, 0);
	}

/**
 * @expectedException GeocodeException
 * @expectedExceptionMessage Unable to decode json: ({"id":"foo"})
 */
	public function test_DecodeJsonUrl_WithFetcherThatReturnsInvalidJson_ThrowsGeocodeException() {
		$mockDataFetcher = $this->__getMockDataFetcherThatReturns('({"id":"foo"})');

		$this->__decoder->decodeJsonUrl('http://example.com', $mockDataFetcher);
	}

	public function test_DecodeJsonUrl_WithFetcherThatReturnsValidJson_ReturnsCorrectArray() {
		$mockDataFetcher = $this->__getMockDataFetcherThatReturns('{"results" : { "foo" : [ { "bar" : 20 }, { "spug" : 40 } ] } }');

		$result = $this->__decoder->decodeJsonUrl('http://example.com', $mockDataFetcher);

		$expected = array(
			'results' => array(
				'foo' => array(
					array('bar' => 20),
					array('spug' => 40),
				),
			),
		);

		$this->assertEqual($result, $expected);
	}
}