<?php

    App::uses('ConsumableRequest', 'Model');

    class ConsumableRequestIntegrationTest extends CakeTestCase 
    {
        public $fixtures = array( 
            'app.ConsumableRequest',
            'app.ConsumableRequestStatus',
            'app.ConsumableSupplier',
            'app.ConsumableArea',
            'app.ConsumableRepeatPurchase',
            'app.ConsumableRequestComment',
            'app.Member',
        );

        public function setUp() 
        {
        	parent::setUp();
            $this->ConsumableRequest = ClassRegistry::init('ConsumableRequest');
        }

        public function test_Add_WithValidData_CorrectlyCreatesRecord()
        {
            $inputData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'supplier_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $prevCount = $this->ConsumableRequest->find('count');
            $this->ConsumableRequest->add($inputData, null);
            $this->assertGreaterThan($prevCount, $this->ConsumableRequest->find('count'));

            $record = $this->ConsumableRequest->findByRequestId($this->ConsumableRequest->id);

            $expectedData = array(
                'ConsumableRequest' => array(
                    'request_id' => $this->ConsumableRequest->id,
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'supplier_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                    'request_status_id' => 1,
                    'member_id' => null,
                    'timestamp' => '0000-00-00 00:00:00',
                ),
                'ConsumableRequestStatus' => array(
                    'request_status_id' => 1,
                    'name' => 'Pending',
                ),
                'ConsumableSupplier' => array(
                    'supplier_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                    'address' => 'a',
                    'url' => 'a',
                ),
                'ConsumableArea' => array(
                    'area_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                ),
                'ConsumableRepeatPurchase' => array(
                    'repeat_purchase_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                    'min' => '1',
                    'max' => '10',
                    'area_id' => '1',
                ),
                'ConsumableRequestComment' => array(
                ),
                'Member' => array(
                    'member_id' => null,
                    'username' => null,
                ),
            );

            $this->assertEquals($expectedData, $record);
        }

        public function test_AddFromRepeatPurchase_WithValidData_CorrectlyCreatesRecord()
        {
            $prevCount = $this->ConsumableRequest->find('count');
            $this->ConsumableRequest->addFromRepeatPurchase(1, null);
            $this->assertGreaterThan($prevCount, $this->ConsumableRequest->find('count'));

            $record = $this->ConsumableRequest->findByRequestId($this->ConsumableRequest->id);

            $expectedData = array(
                'ConsumableRequest' => array(
                    'request_id' => $this->ConsumableRequest->id,
                    'title' => 'a',
                    'detail' => 'a' . PHP_EOL . 'Min: 1' . PHP_EOL . 'Max: 10',
                    'url' => null,
                    'supplier_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                    'request_status_id' => 1,
                    'member_id' => null,
                    'timestamp' => '0000-00-00 00:00:00',
                ),
                'ConsumableRequestStatus' => array(
                    'request_status_id' => 1,
                    'name' => 'Pending',
                ),
                'ConsumableSupplier' => array(
                    'supplier_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                    'address' => 'a',
                    'url' => 'a',
                ),
                'ConsumableArea' => array(
                    'area_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                ),
                'ConsumableRepeatPurchase' => array(
                    'repeat_purchase_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                    'min' => '1',
                    'max' => '10',
                    'area_id' => '1',
                ),
                'ConsumableRequestComment' => array(
                ),
                'Member' => array(
                    'member_id' => null,
                    'username' => null,
                ),
            );

            $this->assertEquals($expectedData, $record);
        }

        public function test_Get_WithIdOfExistingRecord_CorrectlyRetrievesRecordFromFixture()
        {
            $expectedResult = array(
                'request_id' => 1,
                'title' => 'a',
                'detail' => 'a',
                'url' => 'a',
                'supplier_id' => null,
                'area_id' => null,
                'repeat_purchase_id' => null,
                'request_status_id' => 1,
                'member_id' => null,
                'timestamp' => '2013-08-31 09:00:00',
                'status' => array(
                    'request_status_id' => 1,
                    'name' => 'Pending',
                ),
                'supplier' => array(
                    'supplier_id' => null,
                    'name' => null,
                    'description' => null,
                    'address' => null,
                    'url' => null,
                ),
                'area' => array(
                    'area_id' => null,
                    'name' => null,
                    'description' => null,
                ),
                'repeatPurchase' => array(
                    'repeat_purchase_id' => null,
                    'name' => null,
                    'description' => null,
                    'min' => null,
                    'max' => null,
                    'area_id' => null,
                ),
                'comments' => array(
                    array(
                        'request_comment_id' => 1,
                        'text' => 'a',
                        'member_id' => null,
                        'timestamp' => '2013-08-31 09:00:00',
                        'request_id' => 1,
                    ),
                    array(
                        'request_comment_id' => 2,
                        'text' => 'b',
                        'member_id' => 1,
                        'timestamp' => '2013-08-31 10:00:00',
                        'request_id' => 1,
                    ),
                ),
                'member' => array(
                    'member_id' => null,
                    'username' => null,
                ),
            );

            $this->assertEquals($expectedResult, $this->ConsumableRequest->get(1));
        }

        public function test_Get_WithIdOfNonExistantRecord_ReturnsEmptyArray()
        {
            $this->assertEquals(array(), $this->ConsumableRequest->get(10));
        }

        public function test_GetAll_WhenCalled_ReturnsFormattedRecordsFromFixture()
        {
            $expectedResult = array(
                'request_id' => 1,
                'title' => 'a',
                'detail' => 'a',
                'url' => 'a',
                'supplier_id' => null,
                'area_id' => null,
                'repeat_purchase_id' => null,
                'request_status_id' => 1,
                'member_id' => null,
                'timestamp' => '2013-08-31 09:00:00',
                'status' => array(
                    'request_status_id' => 1,
                    'name' => 'Pending',
                ),
                'supplier' => array(
                    'supplier_id' => null,
                    'name' => null,
                    'description' => null,
                    'address' => null,
                    'url' => null,
                ),
                'area' => array(
                    'area_id' => null,
                    'name' => null,
                    'description' => null,
                ),
                'repeatPurchase' => array(
                    'repeat_purchase_id' => null,
                    'name' => null,
                    'description' => null,
                    'min' => null,
                    'max' => null,
                    'area_id' => null,
                ),
                'comments' => array(
                    array(
                        'request_comment_id' => 1,
                        'text' => 'a',
                        'member_id' => null,
                        'timestamp' => '2013-08-31 09:00:00',
                        'request_id' => 1,
                    ),
                    array(
                        'request_comment_id' => 2,
                        'text' => 'b',
                        'member_id' => 1,
                        'timestamp' => '2013-08-31 10:00:00',
                        'request_id' => 1,
                    ),
                ),
                'member' => array(
                    'member_id' => null,
                    'username' => null,
                ),
            );

            $this->assertEquals($expectedResult, $this->ConsumableRequest->getAll()[0]);
        }
    }

?>