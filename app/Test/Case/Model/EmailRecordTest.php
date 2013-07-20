<?php

    App::uses('EmailRecord', 'Model');

    class EmailRecordTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.EmailRecord' );

        public function setUp() 
        {
        	parent::setUp();
            $this->EmailRecord = ClassRegistry::init('EmailRecord');
        }

        public function testCreateReturnsFalseForInvalidData()
        {
            $this->assertFalse( $this->EmailRecord->createNewRecord(1, ''), 'creteNewRecord did not handle empty subject correctly.' );
            $this->assertFalse( $this->EmailRecord->createNewRecord(1, null), 'creteNewRecord did not handle null subject correctly.' );
            $this->assertFalse( $this->EmailRecord->createNewRecord(1, 34), 'creteNewRecord did not handle numeric subject correctly.' );
            $this->assertFalse( $this->EmailRecord->createNewRecord(1, array('foo')), 'creteNewRecord did not handle array subject correctly.' );

            $this->assertFalse( $this->EmailRecord->createNewRecord(null, 'Test subject'), 'creteNewRecord did not handle null id correctly.' );
            $this->assertFalse( $this->EmailRecord->createNewRecord('rwefd', 'Test subject'), 'creteNewRecord did not handle string id correctly.' );
            $this->assertFalse( $this->EmailRecord->createNewRecord(array( 4, 5, 6, 'dff'), 'Test subject'), 'creteNewRecord did not invalid array id correctly.' );
        }

        public function testCreateHandlesSingleRecord()
        {
            $lastRecordId = $this->_getLastRecordId();
            $memberId = 1;
            $subject = 'Test subject';
            $timeBefore = time();
            $this->assertTrue( $this->EmailRecord->createNewRecord($memberId, $subject), 'createNewRecord did not return true for valid data.' );
            $timeAfter = time();

            // Check the record was inserted
            $createdRecord = $this->EmailRecord->findByHmsEmailId($lastRecordId + 1);
            $this->_validateRecord($createdRecord, $memberId, $subject, $timeBefore, $timeAfter);
        }

        public function testCreateHandlesMultipleRecords()
        {
            $lastRecordId = $this->_getLastRecordId();
            $memberIdList = array(1, 2, 3, 6, 4, 7);
            $subject = 'Test subject';
            $timeBefore = time();
            $this->assertTrue( $this->EmailRecord->createNewRecord($memberIdList, $subject), 'createNewRecord did not return true for valid data.' );
            $timeAfter = time();

            $recordIdToCheck = $lastRecordId + 1;
            foreach ($memberIdList as $memberId) 
            {
                $createdRecord = $this->EmailRecord->findByHmsEmailId($recordIdToCheck);
                $this->_validateRecord($createdRecord, $memberId, $subject, $timeBefore, $timeAfter);
                $recordIdToCheck++;
            }
        }

        private function _getLastRecordId()
        {
            $lastRecord = $this->EmailRecord->find('first', array( 'order' => 'EmailRecord.timestamp DESC') );
            $lastRecordId = $lastRecord['EmailRecord']['hms_email_id'];
            return $lastRecordId;
        }

        private function _validateRecord($record, $expectedMemberId, $expectedSubject, $timeBefore, $timeAfter)
        {
            $this->assertInternalType( 'array', $record, 'Record was not created correctly.' );
            $this->assertArrayHasKey( 'EmailRecord', $record, 'Record was not created correctly.' );

            $this->assertInternalType( 'array', $record['EmailRecord'], 'Record was not created correctly.' );
            $this->assertArrayHasKey( 'hms_email_id', $record['EmailRecord'], 'Record was not created correctly.' );
            $this->assertArrayHasKey( 'member_id', $record['EmailRecord'], 'Record was not created correctly.' );
            $this->assertArrayHasKey( 'subject', $record['EmailRecord'], 'Record was not created correctly.' );
            $this->assertArrayHasKey( 'timestamp', $record['EmailRecord'], 'Record was not created correctly.' );

            $this->assertEqual( $expectedMemberId, $record['EmailRecord']['member_id'], 'Member id is incorrect in created record.' );
            $this->assertEqual( $expectedSubject, $record['EmailRecord']['subject'], 'Subject is incorrect in created record.' );
            $this->assertGreaterThanOrEqual( $timeBefore, strtotime($record['EmailRecord']['timestamp']), 'Record has incorrect timestamp.' );
            $this->assertLessThanOrEqual( $timeAfter, strtotime($record['EmailRecord']['timestamp']), 'Record has incorrect timestamp.' );
        }
    }

?>