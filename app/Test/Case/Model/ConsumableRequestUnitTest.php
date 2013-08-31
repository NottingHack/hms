<?php

    App::uses('ConsumableRequest', 'Model');

    class ConsumableRequestUnitTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.ConsumableRequest' );

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
            $this->ConsumableRequest->add(null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNonArrayData_ThrowsException()
        {
            $this->ConsumableRequest->add('thisIsNotAnArray');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoInnerArray_ThrowsException()
        {
            $this->ConsumableRequest->add(array(
                'ConsumableRequest' => null
                )
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
                        'supplier_id' => 1,
                        'area_id' => 1,
                        'repeat_purchase_id' => 1,
                    ),
                )
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
                        'supplier_id' => 1,
                        'area_id' => 1,
                        'repeat_purchase_id' => 1,
                    ),
                )
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
                        'supplier_id' => -1,
                        'area_id' => 1,
                        'repeat_purchase_id' => 1,
                    ),
                )
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
                        'supplier_id' => 0,
                        'area_id' => 1,
                        'repeat_purchase_id' => 1,
                    ),
                )
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
                        'supplier_id' => 'a',
                        'area_id' => 1,
                        'repeat_purchase_id' => 1,
                    ),
                )
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
                        'supplier_id' => 1,
                        'area_id' => -1,
                        'repeat_purchase_id' => 1,
                    ),
                )
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
                        'supplier_id' => 1,
                        'area_id' => 0,
                        'repeat_purchase_id' => 1,
                    ),
                )
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
                        'supplier_id' => 1,
                        'area_id' => 'f',
                        'repeat_purchase_id' => 1,
                    ),
                )
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
                        'supplier_id' => 1,
                        'area_id' => 1,
                        'repeat_purchase_id' => -1,
                    ),
                )
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
                        'supplier_id' => 1,
                        'area_id' => 1,
                        'repeat_purchase_id' => 0,
                    ),
                )
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
                        'supplier_id' => 1,
                        'area_id' => 1,
                        'repeat_purchase_id' => 'f',
                    ),
                )
            );
        }

        public function test_Add_CalledWithAllValidData_CallsSaveWithSameData()
        {
            $expectedData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_status_id' => 1,
                    'supplier_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRequest', array('save'));
            $this->ConsumableRepeatPurchase->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($expectedData));

            $validData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'supplier_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRepeatPurchase->add($validData);
        }

        public function test_Add_CalledWithAllValidDataButNoUrl_CallsSaveWithSameData()
        {
            $expectedData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'request_status_id' => 1,
                    'supplier_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );
            

            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRequest', array('save'));
            $this->ConsumableRepeatPurchase->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($expectedData));

            $validData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'supplier_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRepeatPurchase->add($validData);
        }

        public function test_Add_CalledWithAllValidDataButNoSuppplierId_CallsSaveWithSameData()
        {
            $expectedData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_status_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRequest', array('save'));
            $this->ConsumableRepeatPurchase->expects($this->once())
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

            $this->ConsumableRepeatPurchase->add($validData);
        }

        public function test_Add_CalledWithAllValidDataButNoAreaId_CallsSaveWithSameData()
        {
            $expectedData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_status_id' => 1,
                    'supplier_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRequest', array('save'));
            $this->ConsumableRepeatPurchase->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($expectedData));

            $validData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'supplier_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRepeatPurchase->add($validData);
        }

        public function test_Add_CalledWithAllValidDataButRepeatPurchaseId_CallsSaveWithSameData()
        {
            $expectedData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_status_id' => 1,
                    'supplier_id' => 1,
                    'area_id' => 1,
                ),
            );

            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRequest', array('save'));
            $this->ConsumableRepeatPurchase->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($expectedData));

            $validData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'supplier_id' => 1,
                    'area_id' => 1,
                ),
            );

            $this->ConsumableRepeatPurchase->add($validData);
        }

        public function test_Add_CalledWithStatusId_CallsSaveWithStatusIdSetToPending()
        {
            $expectedData  = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_status_id' => 1,
                    'supplier_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRepeatPurchase = $this->getMockForModel('ConsumableRequest', array('save'));
            $this->ConsumableRepeatPurchase->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($expectedData));

            $inputData = array(
                'ConsumableRequest' => array(
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_status_id' => 2,
                    'supplier_id' => 1,
                    'area_id' => 1,
                    'repeat_purchase_id' => 1,
                ),
            );

            $this->ConsumableRepeatPurchase->add($inputData);
        }
    }
?>