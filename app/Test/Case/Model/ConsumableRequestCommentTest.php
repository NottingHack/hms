<?php

    App::uses('ConsumableRequestComment', 'Model');

    class ConsumableRequestCommentTest extends CakeTestCase 
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
            );

            $this->assertEquals($expectedData, $record);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_GetAllForRequest_WithNullId_ThrowsException()
        {
            $this->ConsumableRequestComment->getAllForRequest(null);
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_GetAllForRequest_WithStringId_ThrowsException()
        {
            $this->ConsumableRequestComment->getAllForRequest('invalidId');
        }

        /**
         * @expectedException InvalidArgumentException
         */
        public function test_GetAllForRequest_WithNegativeId_ThrowsException()
        {
            $this->ConsumableRequestComment->getAllForRequest(-4);
        }

        public function test_GetAllForRequest_WithPositiveNumericId_CallsFindWithSameId()
        {
            $validId = 1;


            $this->ConsumableRequestComment = $this->getMockForModel('ConsumableRequestComment', array('find'));

            $this->ConsumableRequestComment->expects($this->once())
                                     ->method('find')
                                     ->with($this->anything(),
                                            $this->contains(array('ConsumableRequestComment.comment_request_id' => $validId)));

            $this->ConsumableRequestComment->getAllForRequest($validId);
        }

        public function test_GetAllForRequest_WhenFindReturnsNull_ReturnsEmptyArray()
        {
            $validId = 1;

            $this->ConsumableRequestComment = $this->getMockForModel('ConsumableRequestComment', array('find'));

            $this->ConsumableRequestComment->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue(null));

            $this->assertIdentical(array(), $this->ConsumableRequestComment->getAllForRequest($validId));
        }

        public function test_GetAllForRequest_WhenFindReturnsRecord_WillReturnFormattedRecord()
        {
            $recordFindReturns = array(
                'ConsumableRequestComment' => array(
                    'request_comment_id' => 1,
                    'text' => 'a',
                    'member_id' => 1,
                    'timestamp' => '2013-08-31 09:00:00',
                    'request_id' => 1,
                ),
                'Member' => array(
                    'member_id' => 1,
                    'username' => 'a',
                ),
            );

            $this->ConsumableRequestComment = $this->getMockForModel('ConsumableRequestComment', array('find'));
            $this->ConsumableRequestComment->expects($this->once())
                                     ->method('find')
                                     ->will($this->returnValue($recordFindReturns));

            $expectedResult = array(
                'request_comment_id' => 1,
                'text' => 'a',
                'member_id' => 1,
                'timestamp' => '2013-08-31 09:00:00',
                'request_id' => 1,
                'member' => array(
                    'member_id' => 1,
                    'username' => 'a',
                ),
            );

            $this->assertEqual($expectedResult, $this->ConsumableRequestComment->getAllForRequest(1));
        }
    }
?>