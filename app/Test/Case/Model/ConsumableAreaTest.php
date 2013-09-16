<?php

    App::uses('ConsumableArea', 'Model');

    class ConsumableAreaTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.ConsumableArea', 'app.ConsumableRepeatPurchase' );

        public function setUp() 
        {
        	parent::setUp();
            $this->ConsumableArea = ClassRegistry::init('ConsumableArea');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNullData_ThrowsException()
        {
            $this->ConsumableArea->add(null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNonArrayData_ThrowsException()
        {
            $this->ConsumableArea->add('thisIsNotAnArray');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoInnerArray_ThrowsException()
        {
            $this->ConsumableArea->add(array(
                'ConsumableArea' => null
                )
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoName_ThrowsException()
        {
            $this->ConsumableArea->add(
                array(
                    'ConsumableArea' => array(
                        'description' => 'valid description',
                    )
                )
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithInvalidName_ThrowsException()
        {
            $this->ConsumableArea->add(
                array(
                    'ConsumableArea' => array(
                        'name' => null,
                        'description' => 'valid description',
                    )
                )
            );
        }

        public function test_Add_CalledWithValidData_CallsSaveWithSameData()
        {
            $validData = array(
                'ConsumableArea' => array(
                    'name' => 'valid name',
                    'description' => 'valid description',
                )
            );

            $this->ConsumableArea = $this->getMockForModel('ConsumableArea', array('save'));

            $this->ConsumableArea->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($validData));

            $this->ConsumableArea->add($validData);
        }

        public function test_Add_CalledWithValidData_ReturnsTrue()
        {
            $validData = array(
                'ConsumableArea' => array(
                    'name' => 'valid name',
                    'description' => 'valid description',
                )
            );

            $this->ConsumableArea = $this->getMockForModel('ConsumableArea', array('save'));

            $this->ConsumableArea->expects($this->once())
                                     ->method('save')
                                     ->will($this->returnValue(true));

            $this->assertTrue($this->ConsumableArea->add($validData));
        }

        public function test_Add_WithSaveFailing_ReturnsFalse()
        {
            $validData = array(
                'ConsumableArea' => array(
                    'name' => 'valid name',
                    'description' => 'valid description',
                )
            );

            $this->ConsumableArea = $this->getMockForModel('ConsumableArea', array('save'));

            $this->ConsumableArea->expects($this->once())
                                     ->method('save')
                                     ->will($this->returnValue(false));

            $this->assertFalse($this->ConsumableArea->add($validData));
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

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Get_WithNullId_ThrowsException()
        {
            $this->ConsumableArea->get(null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Get_WithStringId_ThrowsException()
        {
            $this->ConsumableArea->get('invalidId');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Get_WithNegativeId_ThrowsException()
        {
            $this->ConsumableArea->get(-4);
        }

        public function test_Get_WithPositiveNumericId_CallsFindWithSameId()
        {
            $validId = 1;


            $this->ConsumableArea = $this->getMockForModel('ConsumableArea', array('find'));

            $this->ConsumableArea->expects($this->once())
                                     ->method('find')
                                     ->with($this->anything(),
                                            $this->contains(array('ConsumableArea.area_id' => $validId)));

            $this->ConsumableArea->get($validId);
        }

        public function test_Get_WhenFindReturnsNull_ReturnsEmptyArray()
        {
            $validId = 1;

            $this->ConsumableArea = $this->getMockForModel('ConsumableArea', array('find'));

            $this->ConsumableArea->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue(null));

            $this->assertIdentical(array(), $this->ConsumableArea->get($validId));
        }

        public function test_Get_WhenFindReturnsRecord_WillReturnFormattedRecord()
        {
            $recordFindReturns = array(
                'ConsumableArea' => array(
                    'area_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                ),
                'ConsumableRepeatPurchase' => array(
                    array(
                        'repeat_purchase_id' => 1,
                        'name' => 'a',
                        'description' => 'a',
                        'min' => '0',
                        'max' => '10',
                        'area_id' => 1,
                    )
                ),
            );

            $this->ConsumableArea = $this->getMockForModel('ConsumableArea', array('find'));
            $this->ConsumableArea->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue($recordFindReturns));

            $expectedResult = array(
                'area_id' => 1,
                'name' => 'a',
                'description' => 'a',
                'repeatPurchases' => array(
                    array(
                        'repeat_purchase_id' => 1,
                        'name' => 'a',
                        'description' => 'a',
                        'min' => '0',
                        'max' => '10',
                        'area_id' => 1,
                    ),
                ),
            );

            $this->assertEqual($expectedResult, $this->ConsumableArea->get(1));
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

        public function test_GetAll_WhenFindReturnsEmptyArray_WillReturnEmptyArray()
        {
            $this->ConsumableArea = $this->getMockForModel('ConsumableArea', array('find'));

            $this->ConsumableArea->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue(array()));

            $this->assertEqual(array(), $this->ConsumableArea->getAll());
        }

        public function test_GetAll_WhenFindReturnsSingleRecord_WillReturnSingleFormattedRecord()
        {
            $recordFindReturns = array(
                array(
                    'ConsumableArea' => array(
                        'area_id' => 1,
                        'name' => 'a',
                        'description' => 'a',
                    ),
                    'ConsumableRepeatPurchase' => array(
                        array(
                            'repeat_purchase_id' => 1,
                            'name' => 'a',
                            'description' => 'a',
                            'min' => '0',
                            'max' => '10',
                            'area_id' => 1,
                        )
                    ),
                )
            );

            $this->ConsumableArea = $this->getMockForModel('ConsumableArea', array('find'));
            $this->ConsumableArea->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue($recordFindReturns));

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
                            'min' => '0',
                            'max' => '10',
                            'area_id' => 1,
                        )
                    ),
                )
            );

            $this->assertEqual($expectedResult, $this->ConsumableArea->getAll());
        }

        public function test_GetAll_WhenFindReturnsMultipleRecords_WillReturnMultipleFormattedRecords()
        {
            $recordFindReturns = array(
                array(
                    'ConsumableArea' => array(
                        'area_id' => 1,
                        'name' => 'a',
                        'description' => 'a',
                    ),
                    'ConsumableRepeatPurchase' => array(
                        array(
                            'repeat_purchase_id' => 1,
                            'name' => 'a',
                            'description' => 'a',
                            'min' => '0',
                            'max' => '10',
                            'area_id' => 1,
                        )
                    ),
                ),
                array(
                    'ConsumableArea' => array(
                        'area_id' => 2,
                        'name' => 'b',
                        'description' => 'b',
                    ),
                    'ConsumableRepeatPurchase' => array(
                        array(
                            'repeat_purchase_id' => 2,
                            'name' => 'b',
                            'description' => 'b',
                            'min' => '0',
                            'max' => '10',
                            'area_id' => 2,
                        ),
                    ),
                ),
            );

            $this->ConsumableArea = $this->getMockForModel('ConsumableArea', array('find'));
            $this->ConsumableArea->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue($recordFindReturns));

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
                            'min' => '0',
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
                            'repeat_purchase_id' => 2,
                            'name' => 'b',
                            'description' => 'b',
                            'min' => '0',
                            'max' => '10',
                            'area_id' => 2,
                        ),
                    ),
                )
            );

            $this->assertEqual($expectedResult, $this->ConsumableArea->getAll());
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