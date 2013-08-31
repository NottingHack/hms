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
                    'member_id' => null,
                ),
            );

            $prevCount = $this->ConsumableRequest->find('count');
            $this->ConsumableRequest->add($inputData);

            $this->assertGreaterThan($prevCount, $this->ConsumableRequest->find('count'));

            $record = $this->ConsumableRequest->findByRequestId(8);
            // Remove the request_id field, since we can't reliably know what it is
            unset($record['ConsumableRequest']['request_id']);

            $expectedData = array(
                'ConsumableRequest' => array(
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
                    'firstname' => null,
                    'surname' => null,
                    'email' => null,
                    'join_date' => null,
                    'unlock_text' => null,
                    'balance' => null,
                    'credit_limit' => null,
                    'member_status' => null,
                    'username' => null,
                    'account_id' => null,
                    'address_1' => null,
                    'address_2' => null,
                    'address_city' => null,
                    'address_postcode' => null,
                    'contact_number' => null,
                ),
            );

            $this->assertEqual($expectedData, $record);
        }
    }

?>