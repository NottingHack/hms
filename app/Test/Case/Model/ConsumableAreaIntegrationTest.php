<?php

    App::uses('ConsumableArea', 'Model');

    class ConsumableAreaIntegrationTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.ConsumableArea', 'app.ConsumableRepeatPurchase' );

        public function setUp() 
        {
        	parent::setUp();
            $this->ConsumableArea = ClassRegistry::init('ConsumableArea');
        }

        public function test_Add_WithValidData_CorrectlyCreatesRecord()
        {
            $validData = array(
                'ConsumableArea' => array(
                    'name' => 'valid name',
                    'description' => 'valid description',
                )
            );

            $prevCount = $this->ConsumableArea->find('count');
            $this->ConsumableArea->add($validData);

            $this->assertGreaterThan($prevCount, $this->ConsumableArea->find('count'));

            $record = $this->ConsumableArea->findByName('valid name');
            // Remove the area_id field, since we can't reliably know what it is
            unset($record['ConsumableArea']['area_id']);

            $expectedResult = array(
                'ConsumableArea' => array(
                    'name' => 'valid name',
                    'description' => 'valid description',
                ),
                'ConsumableRepeatPurchase' => array(),
            );

            $this->assertEqual($expectedResult, $record);
        }

        public function test_Get_WithIdOfExistingRecord_CorrectlyRetrievesRecordFromFixture()
        {
            $expectedResult = array(
                'area_id' => 1,
                'name' => 'a',
                'description' => 'a',
                'repeatPurchases' => array(
                    array(
                        'repeat_purchase_id' => 1,
                        'name' => 'a',
                        'description' => 'a',
                        'min' => '1',
                        'max' => '10',
                        'area_id' => 1,
                    ),
                    array(
                        'repeat_purchase_id' => 2,
                        'name' => 'b',
                        'description' => 'b',
                        'min' => '1',
                        'max' => '10',
                        'area_id' => 1,
                    ),
                ),
            );

            $this->assertEqual($expectedResult, $this->ConsumableArea->get(1));
        }

        public function test_Get_WithIdOfNonExistantRecord_ReturnsEmptyArray()
        {
            $this->assertEqual(array(), $this->ConsumableArea->get(10));
        }

        public function test_GetAll_WhenCalled_ReturnsFormattedRecordsFromFixture()
        {
            $expectedResult = array(
                array(
                    'area_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                    'repeatPurchases' => array(
                        array(
                            'repeat_purchase_id' => 1,
                            'name' => 'a',
                            'description' => 'a',
                            'min' => '1',
                            'max' => '10',
                            'area_id' => 1,
                        ),
                        array(
                            'repeat_purchase_id' => 2,
                            'name' => 'b',
                            'description' => 'b',
                            'min' => '1',
                            'max' => '10',
                            'area_id' => 1,
                        ),
                    ),
                ),
                array(
                    'area_id' => 2,
                    'name' => 'b',
                    'description' => 'b',
                    'repeatPurchases' => array(
                        array(
                            'repeat_purchase_id' => 3,
                            'name' => 'c',
                            'description' => 'c',
                            'min' => '1',
                            'max' => '10',
                            'area_id' => 2,
                        ),
                    ),
                ),
                array(
                    'area_id' => 3,
                    'name' => 'c',
                    'description' => 'c',
                    'repeatPurchases' => array(),
                ),
            );

            $this->assertEqual($expectedResult, $this->ConsumableArea->getAll());
        }
    }

?>