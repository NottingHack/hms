<?php

    App::uses('ConsumableArea', 'Model');

    class ConsumableAreaIntegrationTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.ConsumableArea' );

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
            $this->assertEqual($validData, $record);
        }

        public function test_View_WithIdOfExistingRecord_CorrectlyRetrievesRecordFromFixture()
        {
            $expectedResult = array(
                'area_id' => 1,
                'name' => 'a',
                'description' => 'a',
            );

            $this->assertEqual($expectedResult, $this->ConsumableArea->get(1));
        }

        public function test_View_WithIdOfNonExistantRecord_ReturnsEmptyArray()
        {
            $this->assertEqual(array(), $this->ConsumableArea->get(10));
        }

        public function test_ViewAll_WhenCalled_ReturnsFormattedRecordsFromFixture()
        {
            $expectedResult = array(
                array(
                    'area_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                ),
                array(
                    'area_id' => 2,
                    'name' => 'b',
                    'description' => 'b',
                ),
            );

            $this->assertEqual($expectedResult, $this->ConsumableArea->getAll());
        }
    }

?>