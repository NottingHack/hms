<?php

    App::uses('ConsumableRequest', 'Model');

    class ConsumableRequestTest extends CakeTestCase 
    {
        public $fixtures = array( 
            'app.ConsumableRequest',
            'app.ConsumableRequestStatus',
            'app.ConsumableSupplier',
            'app.ConsumableArea',
            'app.ConsumableRepeatPurchase',
            'app.ConsumableRequestComment',
            'app.ConsumableRequestStatusUpdate',
            'app.Member',
        );

        public function setUp() 
        {
        	parent::setUp();
            $this->ConsumableRequest = ClassRegistry::init('ConsumableRequest');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNullData_ThrowsException()
        {
            $this->ConsumableRequest->add(null, null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNonArrayData_ThrowsException()
        {
            $this->ConsumableRequest->add('thisIsNotAnArray', null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoInnerArray_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => null
                ),
            null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoTitle_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => array(
                        'detail' => 'a',
                        'url' => 'a',
                        'request_id' => 1,
                        'area_id' => 1,
                        'repeat_purchase_id' => 1,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoDetail_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => array(
                        'title' => 'a',
                        'url' => 'a',
                        'request_id' => 1,
                        'area_id' => 1,
                        'repeat_purchase_id' => 1,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNegatveSupplierId_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => array(
                        'title' => 'a',
                        'detail' => 'a',
                        'url' => 'a',
                        'request_id' => -1,
                        'area_id' => 1,
                        'repeat_purchase_id' => 1,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithAeroSupplierId_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => array(
                        'title' => 'a',
                        'detail' => 'a',
                        'url' => 'a',
                        'request_id' => 0,
                        'area_id' => 1,
                        'repeat_purchase_id' => 1,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNonNumericSupplierId_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => array(
                        'title' => 'a',
                        'detail' => 'a',
                        'url' => 'a',
                        'request_id' => 'a',
                        'area_id' => 1,
                        'repeat_purchase_id' => 1,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNegativeAreaId_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => array(
                        'title' => 'a',
                        'detail' => 'a',
                        'url' => 'a',
                        'request_id' => 1,
                        'area_id' => -1,
                        'repeat_purchase_id' => 1,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithZeroAreaId_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => array(
                        'title' => 'a',
                        'detail' => 'a',
                        'url' => 'a',
                        'request_id' => 1,
                        'area_id' => 0,
                        'repeat_purchase_id' => 1,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNonNumericAreaId_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => array(
                        'title' => 'a',
                        'detail' => 'a',
                        'url' => 'a',
                        'request_id' => 1,
                        'area_id' => 'f',
                        'repeat_purchase_id' => 1,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNegativeRepeatPurchaseId_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => array(
                        'title' => 'a',
                        'detail' => 'a',
                        'url' => 'a',
                        'request_id' => 1,
                        'area_id' => 1,
                        'repeat_purchase_id' => -1,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithZeroRepeatPurchaseId_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => array(
                        'title' => 'a',
                        'detail' => 'a',
                        'url' => 'a',
                        'request_id' => 1,
                        'area_id' => 1,
                        'repeat_purchase_id' => 0,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNonNumericRepeatPurchaseId_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => array(
                        'title' => 'a',
                        'detail' => 'a',
                        'url' => 'a',
                        'request_id' => 1,
                        'area_id' => 1,
                        'repeat_purchase_id' => 'f',
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNegativeMemberId_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => array(
                        'title' => 'a',
                        'detail' => 'a',
                        'url' => 'a',
                        'request_id' => 1,
                        'area_id' => 1,
                        'repeat_purchase_id' => 1,
                    ),
                ),
                -1
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithZeroMemberId_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => array(
                        'title' => 'a',
                        'detail' => 'a',
                        'url' => 'a',
                        'request_id' => 1,
                        'area_id' => 1,
                        'repeat_purchase_id' => 1,
                    ),
                ),
                0
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNonNumericMemberId_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => array(
                        'title' => 'a',
                        'detail' => 'a',
                        'url' => 'a',
                        'request_id' => 1,
                        'area_id' => 1,
                        'repeat_purchase_id' => 1,
                    ),
                ),
                'a'
            );
        }

        public function test_Add_CalledWithAllValidData_CallsSaveWithSameData()
        {
            $expectedData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRequest = $this->getMockForModel('ConsumableRequest', array('save'));
            $this->ConsumableRequest->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($expectedData));

            $validData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRequest->add($validData, null);
        }

        public function test_Add_CalledWithAllValidDataButNoUrl_CallsSaveWithSameData()
        {
            $expectedData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'request_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );
            

            $this->ConsumableRequest = $this->getMockForModel('ConsumableRequest', array('save'));
            $this->ConsumableRequest->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($expectedData));

            $validData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'request_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRequest->add($validData, null);
        }

        public function test_Add_CalledWithAllValidDataButNoSuppplierId_CallsSaveWithSameData()
        {
            $expectedData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRequest = $this->getMockForModel('ConsumableRequest', array('save'));
            $this->ConsumableRequest->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($expectedData));


            $validData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRequest->add($validData, null);
        }

        public function test_Add_CalledWithAllValidDataButNoAreaId_CallsSaveWithSameData()
        {
            $expectedData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRequest = $this->getMockForModel('ConsumableRequest', array('save'));
            $this->ConsumableRequest->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($expectedData));

            $validData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRequest->add($validData, null);
        }

        public function test_Add_CalledWithAllValidDataButNoRepeatPurchaseId_CallsSaveWithSameData()
        {
            $expectedData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_id' => 1,
                    'area_id' => 1,
                ),
            );

            $this->ConsumableRequest = $this->getMockForModel('ConsumableRequest', array('save'));
            $this->ConsumableRequest->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($expectedData));

            $validData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_id' => 1,
                    'area_id' => 1,
                ),
            );

            $this->ConsumableRequest->add($validData, null);
        }

        public function test_Add_CalledWithAllValidDataWithValidMemberId_CallsSaveWithSameData()
        {
            $expectedData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_id' => 1,
                    'area_id' => 1,
                ),
            );

            $this->ConsumableRequest = $this->getMockForModel('ConsumableRequest', array('save'));
            $this->ConsumableRequest->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($expectedData));

            $validData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_id' => 1,
                    'area_id' => 1,
                ),
            );

            $this->ConsumableRequest->add($validData, 1);
        }

        public function test_Add_CalledWithStatusId_CallsSaveWithStatusIdSetToPending()
        {
            $expectedData  = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRequest = $this->getMockForModel('ConsumableRequest', array('save'));
            $this->ConsumableRequest->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($expectedData));

            $inputData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRequest->add($inputData, null);
        }

        public function test_Add_CalledWithValidData_ReturnsTrue()
        {
            $validData  = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRequest = $this->getMockForModel('ConsumableRequest', array('save'));

            $this->ConsumableRequest->expects($this->once())
                                     ->method('save')
                                     ->will($this->returnValue(true));

            $this->assertTrue($this->ConsumableRequest->add($validData, null));
        }

        public function test_Add_WithSaveFailing_ReturnsFalse()
        {
            $validData  = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRequest = $this->getMockForModel('ConsumableRequest', array('save'));

            $this->ConsumableRequest->expects($this->once())
                                     ->method('save')
                                     ->will($this->returnValue(false));

            $this->assertFalse($this->ConsumableRequest->add($validData, null));
        }

        public function test_Add_WithRequestStatusUpdateSaveFailing_ReturnsFalse()
        {
            $validData  = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $statusUpdateMock = $this->getMockForModel('ConsumableRequestStatusUpdate', array('save'));

            $statusUpdateMock->expects($this->once())
                             ->method('save')
                             ->will($this->returnValue(false));

            $this->ConsumableRequest->ConsumableRequestStatusUpdate = $statusUpdateMock;

            $this->assertFalse($this->ConsumableRequest->add($validData, null));
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
                'ConsumableRequestStatusUpdate' => array(
                    array(
                        'request_status_update_id' => 5,
                        'request_id' => $this->ConsumableRequest->id,
                        'request_status_id' => 1,
                        'member_id' => null,
                        'timestamp' => '0000-00-00 00:00:00',
                    ),
                ),
            );

            $this->assertEquals($expectedData, $record);
        }

        public function test_Add_WithValidDataAndNonNullMemberId_CorrectlyCreatesRecord()
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
            $this->ConsumableRequest->add($inputData, 1);
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
                'ConsumableRequestStatusUpdate' => array(
                    array(
                        'request_status_update_id' => 5,
                        'request_id' => $this->ConsumableRequest->id,
                        'request_status_id' => 1,
                        'member_id' => 1,
                        'timestamp' => '0000-00-00 00:00:00',
                    ),
                ),
            );

            $this->assertEquals($expectedData, $record);
        }

        public function test_Add_WithRequestStatusUpdateSaveFailing_DoesNotCreateRecord()
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

            $statusUpdateMock = $this->getMockForModel('ConsumableRequestStatusUpdate', array('save'));

            $statusUpdateMock->expects($this->once())
                             ->method('save')
                             ->will($this->returnValue(false));

            $this->ConsumableRequest->ConsumableRequestStatusUpdate = $statusUpdateMock;
            $numRecords = $this->ConsumableRequest->find('count');
            $this->ConsumableRequest->add($inputData, null);
            $this->assertEquals($numRecords, $this->ConsumableRequest->find('count'));
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_AddFromRepeatPurchase_WithNullPurchaseId_ThrowsException()
        {
            $this->ConsumableRequest->addFromRepeatPurchase(null, null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_AddFromRepeatPurchase_WithNegativePurchaseId_ThrowsException()
        {
            $this->ConsumableRequest->addFromRepeatPurchase(-1, null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_AddFromRepeatPurchase_WithZeroPurchaseId_ThrowsException()
        {
            $this->ConsumableRequest->addFromRepeatPurchase(0, null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_AddFromRepeatPurchase_WithNonNumericPurchaseId_ThrowsException()
        {
            $this->ConsumableRequest->addFromRepeatPurchase(0, null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_AddFromRepeatPurchase_WithPurchaseIdOfNonExistantPurchase_ThrowsException()
        {
            $this->ConsumableRequest->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRepeatPurchase', array('find'));

            // Force the find not to return any results
            $this->ConsumableRequest->ConsumableRepeatPurchase->expects($this->once())
                                                             ->method('find')
                                                             ->will($this->returnValue(array()));

            $this->ConsumableRequest->addFromRepeatPurchase(1, null);
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
                'ConsumableRequestStatusUpdate' => array(
                    array(
                        'request_status_update_id' => 5,
                        'request_id' => $this->ConsumableRequest->id,
                        'request_status_id' => 1,
                        'member_id' => null,
                        'timestamp' => '0000-00-00 00:00:00',
                    ),
                ),
            );

            $this->assertEquals($expectedData, $record);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Get_WithNullId_ThrowsException()
        {
            $this->ConsumableRequest->get(null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Get_WithStringId_ThrowsException()
        {
            $this->ConsumableRequest->get('invalidId');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Get_WithNegativeId_ThrowsException()
        {
            $this->ConsumableRequest->get(-1);
        }

        public function test_Get_WithPositiveNumericId_CallsFindWithSameId()
        {
            $validId = 1;


            $this->ConsumableRequest = $this->getMockForModel('ConsumableRequest', array('find'));

            $this->ConsumableRequest->expects($this->once())
                                     ->method('find')
                                     ->with($this->anything(),
                                            $this->contains(array('ConsumableRequest.request_id' => $validId)));

            $this->ConsumableRequest->get($validId);
        }

        public function test_Get_WhenFindReturnsNull_ReturnsEmptyArray()
        {
            $validId = 1;

            $this->ConsumableRequest = $this->getMockForModel('ConsumableRequest', array('find'));

            $this->ConsumableRequest->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue(null));

            $this->assertIdentical(array(), $this->ConsumableRequest->get($validId));
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
                        'member_username' => null,
                    ),
                    array(
                        'request_comment_id' => 2,
                        'text' => 'b',
                        'member_id' => 1,
                        'timestamp' => '2013-08-31 10:00:00',
                        'request_id' => 1,
                        'member_username' => 'strippingdemonic',
                    ),
                ),
                'firstStatus' => array(
                    'request_status_update_id' => 1,
                    'request_id' => 1,
                    'request_status_id' => 1,
                    'member_id' => 1,
                    'timestamp' => '2013-08-31 09:00:00',
                    'request_status_name' => 'Pending',
                    'member_username' => 'strippingdemonic',
                ),
                'currentStatus' => array(
                    'request_status_update_id' => 3,
                    'request_id' => 1,
                    'request_status_id' => 3,
                    'member_id' => 2,
                    'timestamp' => '2013-08-31 11:00:00',
                    'request_status_name' => 'Rejected',
                    'member_username' => 'pecanpaella',
                ),
                'statuses' => array(
                    array(
                        'request_status_update_id' => 3,
                        'request_id' => 1,
                        'request_status_id' => 3,
                        'member_id' => 2,
                        'timestamp' => '2013-08-31 11:00:00',
                        'request_status_name' => 'Rejected',
                        'member_username' => 'pecanpaella',
                    ),
                    array(
                        'request_status_update_id' => 2,
                        'request_id' => 1,
                        'request_status_id' => 2,
                        'member_id' => 2,
                        'timestamp' => '2013-08-31 10:00:00',
                        'request_status_name' => 'Approved',
                        'member_username' => 'pecanpaella',
                    ),
                    array(
                        'request_status_update_id' => 1,
                        'request_id' => 1,
                        'request_status_id' => 1,
                        'member_id' => 1,
                        'timestamp' => '2013-08-31 09:00:00',
                        'request_status_name' => 'Pending',
                        'member_username' => 'strippingdemonic',
                    ),
                ),
            );

            $this->assertEquals($expectedResult, $this->ConsumableRequest->get(1));
        }

        public function test_Get_WithIdOfNonExistantRecord_ReturnsEmptyArray()
        {
            $this->assertEquals(array(), $this->ConsumableRequest->get(10));
        }

        public function test_GetAll_WhenFindReturnsEmptyArray_WillReturnEmptyArray()
        {
            $this->ConsumableRequest = $this->getMockForModel('ConsumableRequest', array('find'));

            $this->ConsumableRequest->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue(array()));

            $this->assertEqual(array(), $this->ConsumableRequest->getAll());
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
                        'member_username' => null,
                    ),
                    array(
                        'request_comment_id' => 2,
                        'text' => 'b',
                        'member_id' => 1,
                        'timestamp' => '2013-08-31 10:00:00',
                        'request_id' => 1,
                        'member_username' => 'strippingdemonic',
                    ),
                ),
                'firstStatus' => array(
                    'request_status_update_id' => 1,
                    'request_id' => 1,
                    'request_status_id' => 1,
                    'member_id' => 1,
                    'timestamp' => '2013-08-31 09:00:00',
                    'request_status_name' => 'Pending',
                    'member_username' => 'strippingdemonic',
                ),
                'currentStatus' => array(
                    'request_status_update_id' => 3,
                    'request_id' => 1,
                    'request_status_id' => 3,
                    'member_id' => 2,
                    'timestamp' => '2013-08-31 11:00:00',
                    'request_status_name' => 'Rejected',
                    'member_username' => 'pecanpaella',
                ),
                'statuses' => array(
                    array(
                        'request_status_update_id' => 3,
                        'request_id' => 1,
                        'request_status_id' => 3,
                        'member_id' => 2,
                        'timestamp' => '2013-08-31 11:00:00',
                        'request_status_name' => 'Rejected',
                        'member_username' => 'pecanpaella',
                    ),
                    array(
                        'request_status_update_id' => 2,
                        'request_id' => 1,
                        'request_status_id' => 2,
                        'member_id' => 2,
                        'timestamp' => '2013-08-31 10:00:00',
                        'request_status_name' => 'Approved',
                        'member_username' => 'pecanpaella',
                    ),
                    array(
                        'request_status_update_id' => 1,
                        'request_id' => 1,
                        'request_status_id' => 1,
                        'member_id' => 1,
                        'timestamp' => '2013-08-31 09:00:00',
                        'request_status_name' => 'Pending',
                        'member_username' => 'strippingdemonic',
                    ),
                ),
            );

            $this->assertEquals($expectedResult, $this->ConsumableRequest->getAll()[0]);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_GetAllWithStatus_WithNullId_ThrowsException()
        {
            $this->ConsumableRequest->getAllWithStatus(null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_GetAllWithStatus_WithStringId_ThrowsException()
        {
            $this->ConsumableRequest->getAllWithStatus('invalidId');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_GetAllWithStatus_WithNegativeId_ThrowsException()
        {
            $this->ConsumableRequest->getAllWithStatus(-1);
        }

        public function test_GetAllWithStatus_WithStatsThatDoesNotMatchRecord_ReturnsEmpotyArray()
        {
            $this->assertEquals(array(), $this->ConsumableRequest->getAllWithStatus(1));
        }

        public function test_GetAllWithStatus_WithStatsThatMatchesRecord_ReturnsFormattedRecordsFromFixture()
        {
            $expectedResult = array(
                'request_id' => 1,
                'title' => 'a',
                'detail' => 'a',
                'url' => 'a',
                'supplier_id' => null,
                'area_id' => null,
                'repeat_purchase_id' => null,
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
                        'member_username' => null,
                    ),
                    array(
                        'request_comment_id' => 2,
                        'text' => 'b',
                        'member_id' => 1,
                        'timestamp' => '2013-08-31 10:00:00',
                        'request_id' => 1,
                        'member_username' => 'strippingdemonic',
                    ),
                ),
                'firstStatus' => array(
                    'request_status_update_id' => 1,
                    'request_id' => 1,
                    'request_status_id' => 1,
                    'member_id' => 1,
                    'timestamp' => '2013-08-31 09:00:00',
                    'request_status_name' => 'Pending',
                    'member_username' => 'strippingdemonic',
                ),
                'currentStatus' => array(
                    'request_status_update_id' => 3,
                    'request_id' => 1,
                    'request_status_id' => 3,
                    'member_id' => 2,
                    'timestamp' => '2013-08-31 11:00:00',
                    'request_status_name' => 'Rejected',
                    'member_username' => 'pecanpaella',
                ),
                'statuses' => array(
                    array(
                        'request_status_update_id' => 3,
                        'request_id' => 1,
                        'request_status_id' => 3,
                        'member_id' => 2,
                        'timestamp' => '2013-08-31 11:00:00',
                        'request_status_name' => 'Rejected',
                        'member_username' => 'pecanpaella',
                    ),
                    array(
                        'request_status_update_id' => 2,
                        'request_id' => 1,
                        'request_status_id' => 2,
                        'member_id' => 2,
                        'timestamp' => '2013-08-31 10:00:00',
                        'request_status_name' => 'Approved',
                        'member_username' => 'pecanpaella',
                    ),
                    array(
                        'request_status_update_id' => 1,
                        'request_id' => 1,
                        'request_status_id' => 1,
                        'member_id' => 1,
                        'timestamp' => '2013-08-31 09:00:00',
                        'request_status_name' => 'Pending',
                        'member_username' => 'strippingdemonic',
                    ),
                ),
            );

            $this->assertEquals($expectedResult, $this->ConsumableRequest->getAllWithStatus(3)[0]);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_GetRequestCounts_WithNullId_ThrowsException()
        {
            $this->ConsumableRequest->getRequestCounts(null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_GetRequestCounts_WithStringId_ThrowsException()
        {
            $this->ConsumableRequest->getRequestCounts('invalidId');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_GetRequestCounts_WithNegativeId_ThrowsException()
        {
            $this->ConsumableRequest->getRequestCounts(-1);
        }

        public function test_GetRequestCounts_WhenFindReturnsEmpty_ReturnsEmptyResults()
        {
            $validId = 1;

            $this->ConsumableRequest = $this->getMockForModel('ConsumableRequest', array('find'));

            $this->ConsumableRequest->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue(array()));

            $expectedResult = array(
                array(
                    'id' => 0,
                    'name' => 'memberInvolved',
                    'count' => 0,
                ),
                array(
                    'id' => 1,
                    'name' => 'Pending',
                    'count' => 0,
                ),
                array(
                    'id' => 2,
                    'name' => 'Approved',
                    'count' => 0,
                ),
                array(
                    'id' => 3,
                    'name' => 'Rejected',
                    'count' => 0,
                ),
                array(
                    'id' => 4,
                    'name' => 'Fulfilled',
                    'count' => 0,
                ),
            );

            $this->assertEquals($expectedResult, $this->ConsumableRequest->getRequestCounts($validId));
        }

        public function test_GetRequestCounts_WithIdOfExistingRecord_CorrectlyRetrievesRecordFromFixture()
        {
            $expectedResult = array(
                 array(
                    'id' => 0,
                    'name' => 'memberInvolved',
                    'count' => 2,
                ),
                array(
                    'id' => 1,
                    'name' => 'Pending',
                    'count' => 0,
                ),
                array(
                    'id' => 2,
                    'name' => 'Approved',
                    'count' => 0,
                ),
                array(
                    'id' => 3,
                    'name' => 'Rejected',
                    'count' => 1,
                ),
                array(
                    'id' => 4,
                    'name' => 'Fulfilled',
                    'count' => 1,
                ),
            );

            $this->assertEquals($expectedResult, $this->ConsumableRequest->getRequestCounts(1));
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_GetRequestsInvolvingMember_WithNullId_ThrowsException()
        {
            $this->ConsumableRequest->getRequestsInvolvingMember(null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_GetRequestsInvolvingMember_WithStringId_ThrowsException()
        {
            $this->ConsumableRequest->getRequestsInvolvingMember('invalidId');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_GetRequestsInvolvingMember_WithNegativeId_ThrowsException()
        {
            $this->ConsumableRequest->getRequestsInvolvingMember(-1);
        }

        public function test_GetRequestsInvolvingMember_WhenFindReturnsEmpty_ReturnsEmptyResults()
        {
            $validId = 1;

            $this->ConsumableRequest = $this->getMockForModel('ConsumableRequest', array('find'));

            $this->ConsumableRequest->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue(array()));

            $expectedResult = array(
                'openedBy' => array(),
                'commentedOn' => array(),
            );
            $this->assertIdentical($expectedResult, $this->ConsumableRequest->getRequestsInvolvingMember($validId));
        }

        public function test_GetRequestsInvolvingMember_WithIdOfExistingRecord_CorrectlyRetrievesRecordFromFixture()
        {
            $expectedResult = array(
                'openedBy' => array(
                    array(
                        'request_id' => 1,
                        'title' => 'a',
                        'detail' => 'a',
                        'url' => 'a',
                        'supplier_id' => null,
                        'area_id' => null,
                        'repeat_purchase_id' => null,
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
                                'member_username' => null,
                            ),
                            array(
                                'request_comment_id' => 2,
                                'text' => 'b',
                                'member_id' => 1,
                                'timestamp' => '2013-08-31 10:00:00',
                                'request_id' => 1,
                                'member_username' => 'strippingdemonic',
                            ),
                        ),
                        'firstStatus' => array(
                            'request_status_update_id' => 1,
                            'request_id' => 1,
                            'request_status_id' => 1,
                            'member_id' => 1,
                            'timestamp' => '2013-08-31 09:00:00',
                            'request_status_name' => 'Pending',
                            'member_username' => 'strippingdemonic',
                        ),
                        'currentStatus' => array(
                            'request_status_update_id' => 3,
                            'request_id' => 1,
                            'request_status_id' => 3,
                            'member_id' => 2,
                            'timestamp' => '2013-08-31 11:00:00',
                            'request_status_name' => 'Rejected',
                            'member_username' => 'pecanpaella',
                        ),
                        'statuses' => array(
                            array(
                                'request_status_update_id' => 3,
                                'request_id' => 1,
                                'request_status_id' => 3,
                                'member_id' => 2,
                                'timestamp' => '2013-08-31 11:00:00',
                                'request_status_name' => 'Rejected',
                                'member_username' => 'pecanpaella',
                            ),
                            array(
                                'request_status_update_id' => 2,
                                'request_id' => 1,
                                'request_status_id' => 2,
                                'member_id' => 2,
                                'timestamp' => '2013-08-31 10:00:00',
                                'request_status_name' => 'Approved',
                                'member_username' => 'pecanpaella',
                            ),
                            array(
                                'request_status_update_id' => 1,
                                'request_id' => 1,
                                'request_status_id' => 1,
                                'member_id' => 1,
                                'timestamp' => '2013-08-31 09:00:00',
                                'request_status_name' => 'Pending',
                                'member_username' => 'strippingdemonic',
                            ),
                        ),
                    ),
                ),
                'commentedOn' => array(
                    array(
                        'request_id' => 2,
                        'title' => 'b',
                        'detail' => 'b',
                        'url' => 'b',
                        'supplier_id' => 1,
                        'area_id' => null,
                        'repeat_purchase_id' => null,
                        'supplier' => array(
                            'supplier_id' => 1,
                            'name' => 'a',
                            'description' => 'a',
                            'address' => 'a',
                            'url' => 'a',
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
                                'request_comment_id' => 3,
                                'text' => 'c',
                                'member_id' => 1,
                                'timestamp' => '2013-08-31 11:00:00',
                                'request_id' => 2,
                                'member_username' => 'strippingdemonic',
                            ),
                            array(
                                'request_comment_id' => 4,
                                'text' => 'c',
                                'member_id' => 2,
                                'timestamp' => '2013-08-31 13:00:00',
                                'request_id' => 2,
                                'member_username' => 'pecanpaella',
                            ),
                        ),
                        'firstStatus' => array(
                        ),
                        'currentStatus' => array(
                        ),
                        'statuses' => array(
                        ),
                    ),
                ),
            );

            $this->assertEquals($expectedResult, $this->ConsumableRequest->getRequestsInvolvingMember(1));
        }
    }
?>