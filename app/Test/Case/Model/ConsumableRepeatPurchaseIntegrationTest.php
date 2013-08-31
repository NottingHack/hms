<?php

    App::uses('ConsumableRepeatPurchase', 'Model');

    class ConsumableRepeatPurchaseIntegrationTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.ConsumableRepeatPurchase', 'app.ConsumableArea' );

        public function setUp() 
        {
        	parent::setUp();
            $this->ConsumableRepeatPurchase = ClassRegistry::init('ConsumableRepeatPurchase');
        }

        public function test_Add_WithValidData_CorrectlyCreatesRecord()
        {
            $validData = array(
                'ConsumableRepeatPurchase' => array(
                    'name' => 'valid name',
                    'description' => 'valid description',
                    'min' => '0',
                    'max' => '10',
                    'area_id' => 1,
                )
            );

            $prevCount = $this->ConsumableRepeatPurchase->find('count');
            $this->ConsumableRepeatPurchase->add($validData);

            $this->assertGreaterThan($prevCount, $this->ConsumableRepeatPurchase->find('count'));

            $record = $this->ConsumableRepeatPurchase->findByName('valid name');
            // Remove the repeat_purchase_id field, since we can't reliably know what it is
            unset($record['ConsumableRepeatPurchase']['repeat_purchase_id']);

            $expectedResult = array(
                'ConsumableRepeatPurchase' => array(
                    'name' => 'valid name',
                    'description' => 'valid description',
                    'min' => '0',
                    'max' => '10',
                    'area_id' => 1,
                ),
                'ConsumableArea' => array(
                    'area_id' => 1,
                    'name' => 'a',
                    'description' => 'a'
                ),
            );

            $this->assertEqual($expectedResult, $record);
        }

        public function test_Get_WithIdOfExistingRecord_CorrectlyRetrievesRecordFromFixture()
        {
            $expectedResult = array(
                'repeat_purchase_id' => 1,
                'name' => 'a',
                'description' => 'a',
                'min' => '1',
                'max' => '10',
                'area_id' => 1,
                'area' => array(
                    'area_id' => 1,
                    'name' => 'a',
                    'description' => 'a'
                ),
            );

            $this->assertEqual($expectedResult, $this->ConsumableRepeatPurchase->get(1));
        }

        public function test_Get_WithIdOfNonExistantRecord_ReturnsEmptyArray()
        {
            $this->assertEqual(array(), $this->ConsumableRepeatPurchase->get(10));
        }

        public function test_GetAll_WhenCalled_ReturnsFormattedRecordsFromFixture()
        {
            $expectedResult = array(
                array(
                    'repeat_purchase_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                    'min' => '1',
                    'max' => '10',
                    'area_id' => 1,
                    'area' => array(
                        'area_id' => 1,
                        'name' => 'a',
                        'description' => 'a'
                    ),
                ),
                array(
                    'repeat_purchase_id' => 2,
                    'name' => 'b',
                    'description' => 'b',
                    'min' => '1',
                    'max' => '10',
                    'area_id' => 1,
                    'area' => array(
                        'area_id' => 1,
                        'name' => 'a',
                        'description' => 'a'
                    ),
                ),
                array(
                    'repeat_purchase_id' => 3,
                    'name' => 'c',
                    'description' => 'c',
                    'min' => '1',
                    'max' => '10',
                    'area_id' => 2,
                    'area' => array(
                        'area_id' => 2,
                        'name' => 'b',
                        'description' => 'b'
                    ),
                ),
            );

            $this->assertEqual($expectedResult, $this->ConsumableRepeatPurchase->getAll());
        }
    }

?>