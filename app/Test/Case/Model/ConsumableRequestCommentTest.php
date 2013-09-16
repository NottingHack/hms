<?php

    App::uses('ConsumableRequestComment', 'Model');

    class ConsumableRequestCommentUnitTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.ConsumableRequestComment', 'app.ConsumableRepeatPurchase', 'app.ConsumableRequest', 'app.Member' );

        public function setUp() 
        {
        	parent::setUp();
            $this->ConsumableRequestComment = ClassRegistry::init('ConsumableRequestComment');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNullData_ThrowsException()
        {
            $this->ConsumableRequestComment->add(null, null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNonArrayData_ThrowsException()
        {
            $this->ConsumableRequestComment->add('thisIsNotAnArray', null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoInnerArray_ThrowsException()
        {
            $this->ConsumableRequestComment->add(array(
                'ConsumableRequestComment' => null
                ),
            null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNoText_ThrowsException()
        {
            $this->ConsumableRequestComment->add(array(
                'ConsumableRequestComment' => array(
                        'request_id' => 1,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithEmptyText_ThrowsException()
        {
            $this->ConsumableRequestComment->add(array(
                'ConsumableRequestComment' => array(
                        'text' => '',
                        'request_id' => 1,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithAllWhitespaceText_ThrowsException()
        {
            $this->ConsumableRequestComment->add(array(
                'ConsumableRequestComment' => array(
                        'text' => '      ',
                        'request_id' => 1,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNullRequestId_ThrowsException()
        {
            $this->ConsumableRequestComment->add(array(
                'ConsumableRequestComment' => array(
                        'text' => 'valid text',
                        'request_id' => null,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNonNumericRequestId_ThrowsException()
        {
            $this->ConsumableRequestComment->add(array(
                'ConsumableRequestComment' => array(
                        'text' => 'valid text',
                        'request_id' => 'string',
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithNegativeRequestId_ThrowsException()
        {
            $this->ConsumableRequestComment->add(array(
                'ConsumableRequestComment' => array(
                        'text' => 'valid text',
                        'request_id' => -1,
                    ),
                ),
                null
            );
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_Add_CalledWithZeroRequestId_ThrowsException()
        {
            $this->ConsumableRequestComment->add(array(
                'ConsumableRequestComment' => array(
                        'text' => 'valid text',
                        'request_id' => 0,
                    ),
                ),
                null
            );
        }

        public function test_Add_CalledWithAllValidData_CallsSaveWithSameData()
        {
            $expectedData = array(
                'ConsumableRequestComment' => array(
                    'text' => 'a',
                    'request_id' => 1,
                    'member_id' => null,
                ),
            );

            $this->ConsumableRequestComment = $this->getMockForModel('ConsumableRequestComment', array('save'));
            $this->ConsumableRequestComment->expects($this->once())
                                         ->method('save')
                                         ->with($this->equalTo($expectedData));

            $validData = array(
                'ConsumableRequestComment' => array(
                    'text' => 'a',
                    'request_id' => 1,
                ),
            );

            $this->ConsumableRequestComment->add($validData, null);
        }

        public function test_Add_CalledWithAllValidDataAndMemberId_CallsSaveWithSameData()
        {
            $expectedData = array(
                'ConsumableRequestComment' => array(
                    'text' => 'a',
                    'request_id' => 1,
                    'member_id' => 1,
                ),
            );

            $this->ConsumableRequestComment = $this->getMockForModel('ConsumableRequestComment', array('save'));
            $this->ConsumableRequestComment->expects($this->once())
                                         ->method('save')
                                         ->with($this->equalTo($expectedData));

            $validData = array(
                'ConsumableRequestComment' => array(
                    'text' => 'a',
                    'request_id' => 1,
                ),
            );

            $this->ConsumableRequestComment->add($validData, 1);
        }

        public function test_Add_CalledWithValidData_ReturnsTrue()
        {
            $validData = array(
                'ConsumableRequestComment' => array(
                    'text' => 'a',
                    'request_id' => 1,
                ),
            );

            $this->ConsumableRequestComment = $this->getMockForModel('ConsumableRequestComment', array('save'));

            $this->ConsumableRequestComment->expects($this->once())
                                     ->method('save')
                                     ->will($this->returnValue(true));

            $this->assertTrue($this->ConsumableRequestComment->add($validData, null));
        }

        public function test_Add_WithSaveFailing_ReturnsFalse()
        {
            $validData = array(
                'ConsumableRequestComment' => array(
                    'text' => 'a',
                    'request_id' => 1,
                ),
            );

            $this->ConsumableRequestComment = $this->getMockForModel('ConsumableRequestComment', array('save'));

            $this->ConsumableRequestComment->expects($this->once())
                                     ->method('save')
                                     ->will($this->returnValue(false));

            $this->assertFalse($this->ConsumableRequestComment->add($validData, null));
        }

        public function test_Add_WithValidData_CorrectlyCreatesRecord()
        {
            $inputData = array(
                'ConsumableRequestComment' => array(
                    'text' => 'a',
                    'request_id' => 1,
                ),
            );

            $prevCount = $this->ConsumableRequestComment->find('count');
            $this->ConsumableRequestComment->add($inputData, null);
            $this->assertGreaterThan($prevCount, $this->ConsumableRequestComment->find('count'));

            $record = $this->ConsumableRequestComment->findByRequestCommentId($this->ConsumableRequestComment->id);

            $expectedData = array(
                'ConsumableRequestComment' => array(
                    'request_comment_id' => $this->ConsumableRequestComment->id,
                    'text' => 'a',
                    'request_id' => 1,
                    'member_id' => null,
                    'timestamp' => '0000-00-00 00:00:00',
                ),
                'Member' => array(
                    'member_id' => null,
                    'username' => null,
                ),
                'ConsumableRequest' => array(
                    'request_id' => 1,
                    'title' => 'a',
                    'detail' => 'a',
                    'url' => 'a',
                    'request_status_id' => 1,
                    'supplier_id' => null,
                    'area_id' => null,
                    'repeat_purchase_id' => null,
                    'member_id' => null,
                    'timestamp' => '2013-08-31 09:00:00',
                ),
            );

            $this->assertEquals($expectedData, $record);
        }
    }
?>