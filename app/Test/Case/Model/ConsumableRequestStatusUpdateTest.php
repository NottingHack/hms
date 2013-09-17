<?php

    App::uses('ConsumableRequestStatusUpdate', 'Model');

    class ConsumableRequestStatusUpdateTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.ConsumableRequestStatusUpdate', 'app.ConsumableRequest', 'app.ConsumableRequestStatus', 'app.Member' );

        public function setUp() 
        {
        	parent::setUp();
            $this->ConsumableRequestStatusUpdate = ClassRegistry::init('ConsumableRequestStatusUpdate');
        }

        

        public function test_Add_CalledWithValidData_CallsSaveWithSameData()
        {
            $requestId = 1;
            $statusId = 1;

            $expectedData = array(
                'ConsumableRequestStatusUpdate' => array(
                    'request_id' => $requestId,
                    'request_status_id' => $statusId,
                )
            );

            $this->ConsumableRequestStatusUpdate = $this->getMockForModel('ConsumableRequestStatusUpdate', array('save'));

            $this->ConsumableRequestStatusUpdate->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($expectedData));

            $this->ConsumableRequestStatusUpdate->add($requestId, $statusId, null);
        }

        public function test_Add_CalledWithValidDataAndNonNullMemberId_CallsSaveWithSameData()
        {
            $requestId = 1;
            $statusId = 1;
            $memberId = 1;

            $expectedData = array(
                'ConsumableRequestStatusUpdate' => array(
                    'request_id' => $requestId,
                    'request_status_id' => $statusId,
                    'member_id' => $memberId,
                )
            );

            $this->ConsumableRequestStatusUpdate = $this->getMockForModel('ConsumableRequestStatusUpdate', array('save'));

            $this->ConsumableRequestStatusUpdate->expects($this->once())
                                     ->method('save')
                                     ->with($this->equalTo($expectedData));

            $this->ConsumableRequestStatusUpdate->add($requestId, $statusId, $memberId);
        }

        public function test_Add_CalledWithValidData_ReturnsTrue()
        {
            $this->ConsumableRequestStatusUpdate = $this->getMockForModel('ConsumableRequestStatusUpdate', array('save'));

            $this->ConsumableRequestStatusUpdate->expects($this->once())
                                     ->method('save')
                                     ->will($this->returnValue(true));

            $this->assertTrue($this->ConsumableRequestStatusUpdate->add(1, 1, null));
        }

        public function test_Add_CalledWithValidDataAndNonNullMemberId_ReturnsTrue()
        {
            $this->ConsumableRequestStatusUpdate = $this->getMockForModel('ConsumableRequestStatusUpdate', array('save'));

            $this->ConsumableRequestStatusUpdate->expects($this->once())
                                     ->method('save')
                                     ->will($this->returnValue(true));

            $this->assertTrue($this->ConsumableRequestStatusUpdate->add(1, 1, 1));
        }

        public function test_Add_WithSaveFailing_ReturnsFalse()
        {
            $this->ConsumableRequestStatusUpdate = $this->getMockForModel('ConsumableRequestStatusUpdate', array('save'));

            $this->ConsumableRequestStatusUpdate->expects($this->once())
                                     ->method('save')
                                     ->will($this->returnValue(false));

            $this->assertFalse($this->ConsumableRequestStatusUpdate->add(1, 1, null));
        }

        public function test_Add_WithValidData_CorrectlyCreatesRecord()
        {
            $prevCount = $this->ConsumableRequestStatusUpdate->find('count');
            $this->ConsumableRequestStatusUpdate->add(1, 1, null);
            $this->assertGreaterThan($prevCount, $this->ConsumableRequestStatusUpdate->find('count'));

            $record = $this->ConsumableRequestStatusUpdate->findByRequestStatusUpdateId($this->ConsumableRequestStatusUpdate->id);

            $expectedData = array(
                'ConsumableRequestStatusUpdate' => array(
                    'request_status_update_id' => $this->ConsumableRequestStatusUpdate->id,
                    'request_id' => 1,
                    'member_id' => null,
                    'request_status_id' => 1,
                    'timestamp' => '0000-00-00 00:00:00',
                ),
                'Member' => array(
                    'member_id' => null,
                    'username' => null,
                ),
                'ConsumableRequestStatus' => array(
                    'request_status_id' => 1,
                    'name' => 'Pending',
                ),
            );

            $this->assertEquals($expectedData, $record);
        }

        public function test_Add_WithValidDataAndNonNullMemberId_CorrectlyCreatesRecord()
        {
            $prevCount = $this->ConsumableRequestStatusUpdate->find('count');
            $this->ConsumableRequestStatusUpdate->add(1, 1, 1);
            $this->assertGreaterThan($prevCount, $this->ConsumableRequestStatusUpdate->find('count'));

            $record = $this->ConsumableRequestStatusUpdate->findByRequestStatusUpdateId($this->ConsumableRequestStatusUpdate->id);

            $expectedData = array(
                'ConsumableRequestStatusUpdate' => array(
                    'request_status_update_id' => $this->ConsumableRequestStatusUpdate->id,
                    'request_id' => 1,
                    'member_id' => 1,
                    'request_status_id' => 1,
                    'timestamp' => '0000-00-00 00:00:00',
                ),
                'Member' => array(
                    'member_id' => 1,
                    'username' => 'strippingdemonic',
                ),
                'ConsumableRequestStatus' => array(
                    'request_status_id' => 1,
                    'name' => 'Pending',
                ),
            );

            $this->assertEquals($expectedData, $record);
        }
    }

?>