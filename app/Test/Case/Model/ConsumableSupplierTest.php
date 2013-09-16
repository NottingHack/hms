<?php

    App::uses('ConsumableSupplier', 'Model');

    class ConsumableSupplierUnitTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.ConsumableSupplier' );

        public function setUp() 
        {
        	parent::setUp();
            $this->ConsumableSupplier = ClassRegistry::init('ConsumableSupplier');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNullData_ThrowsException()
        {
            $this->ConsumableSupplier->add(null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNonArrayData_ThrowsException()
        {
            $this->ConsumableSupplier->add('thisIsNotAnArray');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoInnerArray_ThrowsException()
        {
            $this->ConsumableSupplier->add(array(
                'ConsumableSupplier' => null
                )
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoName_ThrowsException()
        {
            $this->ConsumableSupplier->add(
                array(
                    'ConsumableSupplier' => array(
                        'description' => 'valid description',
                        'address' => 'valid address',
                        'url' => 'valid url',
                    )
                )
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithInvalidName_ThrowsException()
        {
            $this->ConsumableSupplier->add(
                array(
                    'ConsumableSupplier' => array(
                        'name' => null,
                        'description' => 'valid description',
                        'address' => 'valid address',
                        'url' => 'valid url',
                    )
                )
            );
        }

        public function test_Add_CalledWithValidData_CallsSaveWithSameData()
        {
            $validData = array(
                'ConsumableSupplier' => array(
                    'name' => 'valid name',
                    'description' => 'valid description',
                    'address' => 'valid address',
                    'url' => 'valid url',
                )
            );

            $this->ConsumableSupplier = $this->getMockForModel('ConsumableSupplier', array('save'));

            $this->ConsumableSupplier->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($validData));

            $this->ConsumableSupplier->add($validData);
        }

        public function test_Add_CalledWithValidData_ReturnsTrue()
        {
            $validData = array(
                'ConsumableSupplier' => array(
                    'name' => 'valid name',
                    'description' => 'valid description',
                    'address' => 'valid address',
                    'url' => 'valid url',
                )
            );

            $this->ConsumableSupplier = $this->getMockForModel('ConsumableSupplier', array('save'));

            $this->ConsumableSupplier->expects($this->once())
                                     ->method('save')
                                     ->will($this->returnValue(true));

            $this->assertTrue($this->ConsumableSupplier->add($validData));
        }

        public function test_Add_WithSaveFailing_ReturnsFalse()
        {
            $validData = array(
                'ConsumableSupplier' => array(
                    'name' => 'valid name',
                    'description' => 'valid description',
                    'address' => 'valid address',
                    'url' => 'valid url',
                )
            );

            $this->ConsumableSupplier = $this->getMockForModel('ConsumableSupplier', array('save'));

            $this->ConsumableSupplier->expects($this->once())
                                     ->method('save')
                                     ->will($this->returnValue(false));

            $this->assertFalse($this->ConsumableSupplier->add($validData));
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

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Get_WithNullId_ThrowsException()
        {
            $this->ConsumableSupplier->get(null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Get_WithStringId_ThrowsException()
        {
            $this->ConsumableSupplier->get('invalidId');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Get_WithNegativeId_ThrowsException()
        {
            $this->ConsumableSupplier->get(-4);
        }

        public function test_Get_WithPositiveNumericId_CallsFindWithSameId()
        {
            $validId = 1;


            $this->ConsumableSupplier = $this->getMockForModel('ConsumableSupplier', array('find'));

            $this->ConsumableSupplier->expects($this->once())
                                     ->method('find')
                                     ->with($this->anything(),
                                            $this->contains(array('ConsumableSupplier.supplier_id' => $validId)));

            $this->ConsumableSupplier->get($validId);
        }

        public function test_Get_WhenFindReturnsNull_ReturnsEmptyArray()
        {
            $validId = 1;

            $this->ConsumableSupplier = $this->getMockForModel('ConsumableSupplier', array('find'));

            $this->ConsumableSupplier->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue(null));

            $this->assertIdentical(array(), $this->ConsumableSupplier->get($validId));
        }

        public function test_Get_WhenFindReturnsRecord_WillReturnFormattedRecord()
        {
            $recordFindReturns = array(
                'ConsumableSupplier' => array(
                    'supplier_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                    'address' => 'a',
                    'url' => 'a',
                )
            );

            $this->ConsumableSupplier = $this->getMockForModel('ConsumableSupplier', array('find'));
            $this->ConsumableSupplier->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue($recordFindReturns));

            $expectedResult = array(
                'supplier_id' => 1,
                'name' => 'a',
                'description' => 'a',
                'address' => 'a',
                'url' => 'a',
            );

            $this->assertEqual($expectedResult, $this->ConsumableSupplier->get(1));
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

        public function test_GetAll_WhenFindReturnsEmptyArray_WillReturnEmptyArray()
        {
            $this->ConsumableSupplier = $this->getMockForModel('ConsumableSupplier', array('find'));

            $this->ConsumableSupplier->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue(array()));

            $this->assertEqual(array(), $this->ConsumableSupplier->getAll());
        }

        public function test_GetAll_WhenFindReturnsSingleRecord_WillReturnSingleFormattedRecord()
        {
            $recordFindReturns = array(
                array(
                    'ConsumableSupplier' => array(
                        'supplier_id' => 1,
                        'name' => 'a',
                        'description' => 'a',
                        'address' => 'a',
                        'url' => 'a',
                    )
                )
            );

            $this->ConsumableSupplier = $this->getMockForModel('ConsumableSupplier', array('find'));
            $this->ConsumableSupplier->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue($recordFindReturns));

            $expectedResult = array(
                array(
                    'supplier_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                    'address' => 'a',
                    'url' => 'a',
                )
            );

            $this->assertEqual($expectedResult, $this->ConsumableSupplier->getAll());
        }

        public function test_GetAll_WhenFindReturnsMultipleRecords_WillReturnMultipleFormattedRecords()
        {
            $recordFindReturns = array(
                array(
                    'ConsumableSupplier' => array(
                        'supplier_id' => 1,
                        'name' => 'a',
                        'description' => 'a',
                        'address' => 'a',
                        'url' => 'a',
                    )
                ),
                array(
                    'ConsumableSupplier' => array(
                        'supplier_id' => 2,
                        'name' => 'b',
                        'description' => 'b',
                        'address' => 'b',
                        'url' => 'b',
                    )
                ),
            );

            $this->ConsumableSupplier = $this->getMockForModel('ConsumableSupplier', array('find'));
            $this->ConsumableSupplier->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue($recordFindReturns));

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
                )
            );

            $this->assertEqual($expectedResult, $this->ConsumableSupplier->getAll());
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