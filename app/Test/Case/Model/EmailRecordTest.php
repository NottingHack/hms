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

        public function testGetAllEmails()
        {
            $expectedData = array(
                array('id' => 4, 'member_id' => 4, 'subject' => 'Test email 2', 'timestamp' => '2013-06-05 13:51:04' ),
                array('id' => 8, 'member_id' => 4, 'subject' => 'Test email 7', 'timestamp' => '2013-04-13 22:51:09' ),
                array('id' => 3, 'member_id' => 3, 'subject' => 'Test email 2', 'timestamp' => '2013-03-23 05:42:21' ),
                array('id' => 6, 'member_id' => 4, 'subject' => 'Test email 5', 'timestamp' => '2013-03-16 15:15:23' ),
                array('id' => 7, 'member_id' => 4, 'subject' => 'Test email 6', 'timestamp' => '2013-02-19 17:47:26' ),
                array('id' => 5, 'member_id' => 4, 'subject' => 'Test email 3', 'timestamp' => '2013-01-23 09:23:42' ),
                array('id' => 2, 'member_id' => 2, 'subject' => 'Test email 1', 'timestamp' => '2012-12-17 19:20:00' ),
                array('id' => 1, 'member_id' => 1, 'subject' => 'Test email 1', 'timestamp' => '2012-12-17 19:19:59' ),
            );
            $this->assertEqual( $this->EmailRecord->getAllEmails(), $expectedData, 'Incorrect data returned.' );
        }

        public function testGetAllEmailsForMemberInvalidData()
        {
            $this->assertEqual( $this->EmailRecord->getAllEmailsForMember(null), null, 'Null data not handled correctly.' );
            $this->assertEqual( $this->EmailRecord->getAllEmailsForMember('foo'), null, 'String data not handled correctly.' );
            $this->assertEqual( $this->EmailRecord->getAllEmailsForMember(array(3)), null, 'Array data not handled correctly.' );
            $this->assertEqual( $this->EmailRecord->getAllEmailsForMember(0), null, 'Invalid integer data not handled correctly.' );
        }

        public function testAllEmailsForMemberalidData()
        {
            $expectedData = array(
                array('id' => 4, 'member_id' => '4', 'subject' => 'Test email 2', 'timestamp' => '2013-06-05 13:51:04' ),
                array('id' => 8, 'member_id' => '4', 'subject' => 'Test email 7', 'timestamp' => '2013-04-13 22:51:09' ),
                array('id' => 6, 'member_id' => '4', 'subject' => 'Test email 5', 'timestamp' => '2013-03-16 15:15:23' ),
                array('id' => 7, 'member_id' => '4', 'subject' => 'Test email 6', 'timestamp' => '2013-02-19 17:47:26' ),
                array('id' => 5, 'member_id' => '4', 'subject' => 'Test email 3', 'timestamp' => '2013-01-23 09:23:42' ),
            );
            $this->assertEqual( $this->EmailRecord->getAllEmailsForMember(4), $expectedData, 'Valid data not handled correctly.' );
        }

        public function testGetMostRecentEmailForMemberInvalidData()
        {
            $this->assertEqual( $this->EmailRecord->getMostRecentEmailForMember(null), null, 'Null data not handled correctly.' );
            $this->assertEqual( $this->EmailRecord->getMostRecentEmailForMember('foo'), null, 'String data not handled correctly.' );
            $this->assertEqual( $this->EmailRecord->getMostRecentEmailForMember(array(3)), null, 'Array data not handled correctly.' );
            $this->assertEqual( $this->EmailRecord->getMostRecentEmailForMember(0), null, 'Invalid integer data not handled correctly.' );
        }

        public function testGetMostRecentEmailForMemberValidData()
        {
            $expectedData = array(
                'id' => '4',
                'member_id' => '4',
                'subject' => 'Test email 2',
                'timestamp' => '2013-06-05 13:51:04'
            );
            $this->assertEqual( $this->EmailRecord->getMostRecentEmailForMember(4), $expectedData, 'Valid data not handled correctly.' );
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
            $lastRecordId = $this->getLastRecordId();
            $memberId = 1;
            $subject = 'Test subject';
            $timeBefore = time();
            $this->assertTrue( $this->EmailRecord->createNewRecord($memberId, $subject), 'createNewRecord did not return true for valid data.' );
            $timeAfter = time();

            // Check the record was inserted
            $createdRecord = $this->EmailRecord->findByHmsEmailId($lastRecordId + 1);
            self::validateRecord($this, $createdRecord, $memberId, $subject, $timeBefore, $timeAfter);
        }

        public function testCreateHandlesMultipleRecords()
        {
            $lastRecordId = $this->getLastRecordId();
            $memberIdList = array(1, 2, 3, 6, 4, 7);
            $subject = 'Test subject';
            $timeBefore = time();
            $this->assertTrue( $this->EmailRecord->createNewRecord($memberIdList, $subject), 'createNewRecord did not return true for valid data.' );
            $timeAfter = time();

            $recordIdToCheck = $lastRecordId + 1;
            foreach ($memberIdList as $memberId) 
            {
                $createdRecord = $this->EmailRecord->findByHmsEmailId($recordIdToCheck);
                self::validateRecord($this, $createdRecord, $memberId, $subject, $timeBefore, $timeAfter);
                $recordIdToCheck++;
            }
        }

        public function getLastRecordId()
        {
            $lastRecord = $this->EmailRecord->find('first', array( 'order' => 'EmailRecord.hms_email_id DESC') );
            $lastRecordId = $lastRecord['EmailRecord']['hms_email_id'];
            return $lastRecordId;
        }

        public static function validateRecord($asserter, $record, $expectedMemberId, $expectedSubject, $timeBefore, $timeAfter)
        {
            $asserter->assertInternalType( 'array', $record, 'Record was not created correctly.' );
            $asserter->assertArrayHasKey( 'EmailRecord', $record, 'Record was not created correctly.' );

            $asserter->assertInternalType( 'array', $record['EmailRecord'], 'Record was not created correctly.' );
            $asserter->assertArrayHasKey( 'hms_email_id', $record['EmailRecord'], 'Record was not created correctly.' );
            $asserter->assertArrayHasKey( 'member_id', $record['EmailRecord'], 'Record was not created correctly.' );
            $asserter->assertArrayHasKey( 'subject', $record['EmailRecord'], 'Record was not created correctly.' );
            $asserter->assertArrayHasKey( 'timestamp', $record['EmailRecord'], 'Record was not created correctly.' );

            $asserter->assertEqual( $expectedMemberId, $record['EmailRecord']['member_id'], 'Member id is incorrect in created record.' );
            $asserter->assertEqual( $expectedSubject, $record['EmailRecord']['subject'], 'Subject is incorrect in created record.' );
            $asserter->assertGreaterThanOrEqual( $timeBefore, strtotime($record['EmailRecord']['timestamp']), 'Record has incorrect timestamp.' );
            $asserter->assertLessThanOrEqual( $timeAfter, strtotime($record['EmailRecord']['timestamp']), 'Record has incorrect timestamp.' );
        }
    }

?>