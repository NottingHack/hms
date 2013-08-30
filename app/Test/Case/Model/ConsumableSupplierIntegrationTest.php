<?php

    App::uses('ConsumableSupplier', 'Model');

    class ConsumableSupplierIntegrationTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.ConsumableSupplier' );

        public function setUp() 
        {
        	parent::setUp();
            $this->ConsumableSupplier = ClassRegistry::init('ConsumableSupplier');
        }

        public function test_Add_WithValidData_CorrectlyCreatesRecord()
        {
            $validData = array(
                'ConsumableSupplier' => array(
                    'name' => 'valid name',
                    'description' => 'valid description',
                    'address' => 'valid address',
                    'url' => 'valid url',
                )
            );

            $prevCount = $this->ConsumableSupplier->find('count');
            $this->ConsumableSupplier->add($validData);

            $this->assertGreaterThan($prevCount, $this->ConsumableSupplier->find('count'));

            $record = $this->ConsumableSupplier->findByName('valid name');
            // Remove the supplier_id field, since we can't reliably know what it is
            unset($record['ConsumableSupplier']['supplier_id']);
            $this->assertEqual($validData, $record);
        }

        public function test_Get_WithIdOfExistingRecord_CorrectlyRetrievesRecordFromFixture()
        {
            $expectedResult = array(
                'supplier_id' => 1,
                'name' => 'a',
                'description' => 'a',
                'address' => 'a',
                'url' => 'a',
            );

            $this->assertEqual($expectedResult, $this->ConsumableSupplier->get(1));
        }

        public function test_Get_WithIdOfNonExistantRecord_ReturnsEmptyArray()
        {
            $this->assertEqual(array(), $this->ConsumableSupplier->get(10));
        }

        public function test_GetAll_WhenCalled_ReturnsFormattedRecordsFromFixture()
        {
            $expectedResult = array(
                array(
                    'supplier_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                    'address' => 'a',
                    'url' => 'a',
                ),
                array(
                    'supplier_id' => 2,
                    'name' => 'b',
                    'description' => 'b',
                    'address' => 'b',
                    'url' => 'b',
                ),
            );

            $this->assertEqual($expectedResult, $this->ConsumableSupplier->getAll());
        }
    }

?>