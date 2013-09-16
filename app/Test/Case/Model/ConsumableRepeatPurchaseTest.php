<?php

    App::uses('ConsumableRepeatPurchase', 'Model');

    class ConsumableRepeatPurchaseTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.ConsumableRepeatPurchase', 'app.ConsumableArea' );

        public function setUp() 
        {
        	parent::setUp();
            $this->ConsumableRepeatPurchase = ClassRegistry::init('ConsumableRepeatPurchase');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNullData_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->add(null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNonArrayData_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->add('thisIsNotAnArray');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoInnerArray_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->add(array(
                'ConsumableRepeatPurchase' => null
                )
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoName_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->add(
                array(
                    'ConsumableRepeatPurchase' => array(
                        'description' => 'valid description',
                        'min' => '0',
                        'max' => '10',
                        'area_id' => 1,
                    )
                )
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithInvalidName_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->add(
                array(
                    'ConsumableRepeatPurchase' => array(
                        'name' => null,
                        'description' => 'valid description',
                        'min' => '0',
                        'max' => '10',
                        'area_id' => 1,
                    )
                )
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoMin_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->add(
                array(
                    'ConsumableRepeatPurchase' => array(
                        'name' => 'valid name',
                        'description' => 'valid description',
                        'max' => '10',
                        'area_id' => 1,
                    )
                )
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithInvalidMin_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->add(
                array(
                    'ConsumableRepeatPurchase' => array(
                        'name' => 'valid name',
                        'description' => 'valid description',
                        'min' => null,
                        'max' => '10',
                        'area_id' => 1,
                    )
                )
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoMax_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->add(
                array(
                    'ConsumableRepeatPurchase' => array(
                        'name' => 'valid name',
                        'description' => 'valid description',
                        'min' => '0',
                        'area_id' => 1,
                    )
                )
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithInvalidMax_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->add(
                array(
                    'ConsumableRepeatPurchase' => array(
                        'name' => 'valid name',
                        'description' => 'valid description',
                        'min' => '0',
                        'max' => null,
                        'area_id' => 1,
                    )
                )
            );
        }

                /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoAreaId_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->add(
                array(
                    'ConsumableRepeatPurchase' => array(
                        'name' => 'valid name',
                        'description' => 'valid description',
                        'min' => '0',
                        'max' => '10',
                    )
                )
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNonNumericAreaId_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->add(
                array(
                    'ConsumableRepeatPurchase' => array(
                        'name' => 'valid name',
                        'description' => 'valid description',
                        'min' => '0',
                        'max' => '10',
                        'area_id' => 'a',
                    )
                )
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNegativeAreaId_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->add(
                array(
                    'ConsumableRepeatPurchase' => array(
                        'name' => 'valid name',
                        'description' => 'valid description',
                        'min' => '0',
                        'max' => '10',
                        'area_id' => -4,
                    )
                )
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithZeroAreaId_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->add(
                array(
                    'ConsumableRepeatPurchase' => array(
                        'name' => 'valid name',
                        'description' => 'valid description',
                        'min' => '0',
                        'max' => '10',
                        'area_id' => 0,
                    )
                )
            );
        }

        public function test_Add_CalledWithValidData_CallsSaveWithSameData()
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

            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRepeatPurchase', array('save'));

            $this->ConsumableRepeatPurchase->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($validData));

            $this->ConsumableRepeatPurchase->add($validData);
        }

        public function test_Add_CalledWithValidData_ReturnsTrue()
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

            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRepeatPurchase', array('save'));

            $this->ConsumableRepeatPurchase->expects($this->once())
                                     ->method('save')
                                     ->will($this->returnValue(true));

            $this->assertTrue($this->ConsumableRepeatPurchase->add($validData));
        }

        public function test_Add_WithSaveFailing_ReturnsFalse()
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

            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRepeatPurchase', array('save'));

            $this->ConsumableRepeatPurchase->expects($this->once())
                                     ->method('save')
                                     ->will($this->returnValue(false));

            $this->assertFalse($this->ConsumableRepeatPurchase->add($validData));
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

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Get_WithNullId_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->get(null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Get_WithStringId_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->get('invalidId');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Get_WithNegativeId_ThrowsException()
        {
            $this->ConsumableRepeatPurchase->get(-4);
        }

        public function test_Get_WithPositiveNumericId_CallsFindWithSameId()
        {
            $validId = 1;


            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRepeatPurchase', array('find'));

            $this->ConsumableRepeatPurchase->expects($this->once())
                                     ->method('find')
                                     ->with($this->anything(),
                                            $this->contains(array('ConsumableRepeatPurchase.repeat_purchase_id' => $validId)));

            $this->ConsumableRepeatPurchase->get($validId);
        }

        public function test_Get_WhenFindReturnsNull_ReturnsEmptyArray()
        {
            $validId = 1;

            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRepeatPurchase', array('find'));

            $this->ConsumableRepeatPurchase->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue(null));

            $this->assertIdentical(array(), $this->ConsumableRepeatPurchase->get($validId));
        }

        public function test_Get_WhenFindReturnsRecord_WillReturnFormattedRecord()
        {
            $recordFindReturns = array(
                'ConsumableRepeatPurchase' => array(
                    'repeat_purchase_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                    'min' => '0',
                    'max' => '10',
                    'area_id' => 1,
                ),
                'ConsumableArea' => array(
                    array(
                        'area_id' => 1,
                        'name' => 'a',
                        'description' => 'a',
                    ),
                ),
            );

            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRepeatPurchase', array('find'));
            $this->ConsumableRepeatPurchase->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue($recordFindReturns));

            $expectedResult = array(
                'repeat_purchase_id' => 1,
                'name' => 'a',
                'description' => 'a',
                'min' => '0',
                'max' => '10',
                'area_id' => 1,
                'area' => array(
                    array(
                        'area_id' => 1,
                        'name' => 'a',
                        'description' => 'a',
                    ),
                ),
            );

            $this->assertEqual($expectedResult, $this->ConsumableRepeatPurchase->get(1));
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

        public function test_GetAll_WhenFindReturnsEmptyArray_WillReturnEmptyArray()
        {
            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRepeatPurchase', array('find'));

            $this->ConsumableRepeatPurchase->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue(array()));

            $this->assertEqual(array(), $this->ConsumableRepeatPurchase->getAll());
        }

        public function test_GetAll_WhenFindReturnsSingleRecord_WillReturnSingleFormattedRecord()
        {
            $recordFindReturns = array(
                array(
                    'ConsumableRepeatPurchase' => array(
                        'repeat_purchase_id' => 1,
                        'name' => 'a',
                        'description' => 'a',
                        'min' => '0',
                        'max' => '10',
                        'area_id' => 1,
                    ),
                    'ConsumableArea' => array(
                        array(
                            'area_id' => 1,
                            'name' => 'a',
                            'description' => 'a',
                        ),
                    ),
                )
            );

            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRepeatPurchase', array('find'));
            $this->ConsumableRepeatPurchase->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue($recordFindReturns));

            $expectedResult = array(
                array(
                    'repeat_purchase_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                    'min' => '0',
                    'max' => '10',
                    'area_id' => 1,
                    'area' => array(
                        array(
                            'area_id' => 1,
                            'name' => 'a',
                            'description' => 'a',
                        ),
                    )
                )
            );

            $this->assertEqual($expectedResult, $this->ConsumableRepeatPurchase->getAll());
        }

        public function test_GetAll_WhenFindReturnsMultipleRecords_WillReturnMultipleFormattedRecords()
        {
            $recordFindReturns = array(
                array(
                    'ConsumableRepeatPurchase' => array(
                        'repeat_purchase_id' => 1,
                        'name' => 'a',
                        'description' => 'a',
                        'min' => '0',
                        'max' => '10',
                        'area_id' => 1,
                    ),
                    'ConsumableArea' => array(
                        array(
                            'area_id' => 1,
                            'name' => 'a',
                            'description' => 'a',
                        ),
                    ),
                ),
                array(
                    'ConsumableRepeatPurchase' => array(
                        'repeat_purchase_id' => 2,
                        'name' => 'b',
                        'description' => 'b',
                        'min' => '0',
                        'max' => '10',
                        'area_id' => 1,
                    ),
                    'ConsumableArea' => array(
                        array(
                            'area_id' => 1,
                            'name' => 'a',
                            'description' => 'a',
                        ),
                    ),
                ),
            );

            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRepeatPurchase', array('find'));
            $this->ConsumableRepeatPurchase->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue($recordFindReturns));

            $expectedResult = array(
                array(
                    'repeat_purchase_id' => 1,
                    'name' => 'a',
                    'description' => 'a',
                    'min' => '0',
                    'max' => '10',
                    'area_id' => 1,
                    'area' => array(
                        array(
                            'area_id' => 1,
                            'name' => 'a',
                            'description' => 'a',
                        ),
                    )
                ),
                array(
                    'repeat_purchase_id' => 2,
                    'name' => 'b',
                    'description' => 'b',
                    'min' => '0',
                    'max' => '10',
                    'area_id' => 1,
                    'area' => array(
                        array(
                            'area_id' => 1,
                            'name' => 'a',
                            'description' => 'a',
                        ),
                    )
                )
            );

            $this->assertEqual($expectedResult, $this->ConsumableRepeatPurchase->getAll());
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