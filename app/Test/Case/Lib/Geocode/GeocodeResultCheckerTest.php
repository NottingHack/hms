<?php

App::uses('GeocodeResultChecker', 'Lib/GoogleGeocode');

App::uses('Geocode', 'Lib/GoogleGeocode');

class GeocodeResultCheckerTest extends CakeTestCase {

	private $__checker;

	public function setUp() {
		parent::setUp();
		$this->__checker = new GeocodeResultChecker();
	}

	private function __getTestJsonWithStatus($status) {
		return array('status' => $status);
	}

/**
 * @expectedException GeocodeException
 * @expectedExceptionMessage $data must be an array
 */
	public function test_CheckJsonResult_WithNullData_ThrowsGeocodeException() {
		$this->__checker->checkJsonResult(null);
	}

/**
 * @expectedException GeocodeException
 * @expectedExceptionMessage $data must be an array
 */
	public function test_CheckJsonResult_WithNonArrayData_ThrowsGeocodeException() {
		$this->__checker->checkJsonResult('');
	}

/**
 * @expectedException GeocodeException
 * @expectedExceptionMessage Json is invalid: ["results"]
 */
	public function test_CheckJsonResult_WithArrayWithNoStatus_ThrowsGeocodeException() {
		$this->__checker->checkJsonResult(array('results'));
	}

/**
 * @expectedException GeocodeException
 * @expectedExceptionMessage Status-code error: OVER_QUERY_LIMIT
 */
	public function test_CheckJsonResult_OverQueryLimitStatus_ThrowsGeocodeException() {
		$this->__checker->checkJsonResult($this->__getTestJsonWithStatus('OVER_QUERY_LIMIT'));
	}

/**
 * @expectedException GeocodeException
 * @expectedExceptionMessage Status-code error: REQUEST_DENIED
 */
	public function test_CheckJsonResult_RequestDeniedStatus_ThrowsGeocodeException() {
		$this->__checker->checkJsonResult($this->__getTestJsonWithStatus('REQUEST_DENIED'));
	}

/**
 * @expectedException GeocodeException
 * @expectedExceptionMessage Status-code error: INVALID_REQUEST
 */
	public function test_CheckJsonResult_InvalidRequestStatus_ThrowsGeocodeException() {
		$this->__checker->checkJsonResult($this->__getTestJsonWithStatus('INVALID_REQUEST'));
	}

/**
 * @expectedException GeocodeException
 * @expectedExceptionMessage Status-code error: UNKNOWN_ERROR
 */
	public function test_CheckJsonResult_UnknownErrorStatus_ThrowsGeocodeException() {
		$this->__checker->checkJsonResult($this->__getTestJsonWithStatus('UNKNOWN_ERROR'));
	}

	public function test_CheckJsonResult_ZeroResultsStatus_ReturnsFalse() {
		$result = $this->__checker->checkJsonResult($this->__getTestJsonWithStatus('ZERO_RESULTS'));

		$this->assertIdentical($result, false);
	}

	public function test_CheckJsonResult_OkStatus_ReturnsTrue() {
		$result = $this->__checker->checkJsonResult($this->__getTestJsonWithStatus('OK'));

		$this->assertIdentical($result, true);
	}
}