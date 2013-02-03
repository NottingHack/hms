<?php

    App::uses('StatusUpdate', 'Model');

    class StatusUpdateTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.StatusUpdate', 'app.Member', 'app.Status' );

        public function setUp() 
        {
        	parent::setUp();
            $this->StatusUpdate = ClassRegistry::init('StatusUpdate');
        }

        public function testCreateNewRecordInvalidData()
        {
            $this->assertFalse( $this->StatusUpdate->createNewRecord(null, null, null, null), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->StatusUpdate->createNewRecord(1, null, null, null), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->StatusUpdate->createNewRecord(1, 2, null, null), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->StatusUpdate->createNewRecord(1, 2, 0, null), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->StatusUpdate->createNewRecord('foo', 2, 0, 1), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->StatusUpdate->createNewRecord('foo', 'bar', 0, 1), 'Invalid data not handled correctly.' );
        }

        public function testCreateNewRecordWithAdmin()
        {
            $beforeTimestamp = time();

            $this->assertTrue( $this->StatusUpdate->createNewRecord(11, 3, 4, 5), 'Valid data was not handled correctly.' );

            $afterTimestamp = time();

            $record = $this->StatusUpdate->findById(1);

            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );
            $this->assertArrayHasKey( 'StatusUpdate', $record, 'Record does not have status update key.' );

            $this->assertArrayHasKey( 'id', $record['StatusUpdate'], 'Record does not have id key.' );
            $this->assertArrayHasKey( 'member_id', $record['StatusUpdate'], 'Record does not have member_id key.' );
            $this->assertArrayHasKey( 'admin_id', $record['StatusUpdate'], 'Record does not have admin_id key.' );
            $this->assertArrayHasKey( 'old_status', $record['StatusUpdate'], 'Record does not have old_status key.' );
            $this->assertArrayHasKey( 'new_status', $record['StatusUpdate'], 'Record does not have new_status key.' );
            $this->assertArrayHasKey( 'timestamp', $record['StatusUpdate'], 'Record does not have timestamp key.' );

            $this->assertEqual( $record['StatusUpdate']['id'], 1, 'Record has incorrect id.' );
            $this->assertEqual( $record['StatusUpdate']['member_id'], 11, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['StatusUpdate']['admin_id'], 3, 'Record has incorrect admin_id.' );
            $this->assertEqual( $record['StatusUpdate']['old_status'], 4, 'Record has incorrect old_status.' );
            $this->assertEqual( $record['StatusUpdate']['new_status'], 5, 'Record has incorrect new_status.' );
            $this->assertGreaterThanOrEqual( $beforeTimestamp, strtotime($record['StatusUpdate']['timestamp']), 'Record has incorrect timestamp.' );
            $this->assertLessThanOrEqual( $afterTimestamp, strtotime($record['StatusUpdate']['timestamp']), 'Record has incorrect timestamp.' );
        }

        public function testCreateNewRecordWithSameAdmin()
        {
            $beforeTimestamp = time();

            $this->assertTrue( $this->StatusUpdate->createNewRecord(12, 12, 3, 2), 'Valid data was not handled correctly.' );

            $afterTimestamp = time();

            $record = $this->StatusUpdate->findById(1);

            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );
            $this->assertArrayHasKey( 'StatusUpdate', $record, 'Record does not have status update key.' );

            $this->assertArrayHasKey( 'id', $record['StatusUpdate'], 'Record does not have id key.' );
            $this->assertArrayHasKey( 'member_id', $record['StatusUpdate'], 'Record does not have member_id key.' );
            $this->assertArrayHasKey( 'admin_id', $record['StatusUpdate'], 'Record does not have admin_id key.' );
            $this->assertArrayHasKey( 'old_status', $record['StatusUpdate'], 'Record does not have old_status key.' );
            $this->assertArrayHasKey( 'new_status', $record['StatusUpdate'], 'Record does not have new_status key.' );
            $this->assertArrayHasKey( 'timestamp', $record['StatusUpdate'], 'Record does not have timestamp key.' );

            $this->assertEqual( $record['StatusUpdate']['id'], 1, 'Record has incorrect id.' );
            $this->assertEqual( $record['StatusUpdate']['member_id'], 12, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['StatusUpdate']['admin_id'], 12, 'Record has incorrect admin_id.' );
            $this->assertEqual( $record['StatusUpdate']['old_status'], 3, 'Record has incorrect old_status.' );
            $this->assertEqual( $record['StatusUpdate']['new_status'], 2, 'Record has incorrect new_status.' );
            $this->assertGreaterThanOrEqual( $beforeTimestamp, strtotime($record['StatusUpdate']['timestamp']), 'Record has incorrect timestamp.' );
            $this->assertLessThanOrEqual( $afterTimestamp, strtotime($record['StatusUpdate']['timestamp']), 'Record has incorrect timestamp.' );
        }

        public function testCreateNewRecordWithNoAdmin()
        {
            $beforeTimestamp = time();

            $this->assertTrue( $this->StatusUpdate->createNewRecord(13, 0, 1, 2), 'Valid data was not handled correctly.' );

            $afterTimestamp = time();

            $record = $this->StatusUpdate->findById(1);

            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );
            $this->assertArrayHasKey( 'StatusUpdate', $record, 'Record does not have status update key.' );

            $this->assertArrayHasKey( 'id', $record['StatusUpdate'], 'Record does not have id key.' );
            $this->assertArrayHasKey( 'member_id', $record['StatusUpdate'], 'Record does not have member_id key.' );
            $this->assertArrayHasKey( 'admin_id', $record['StatusUpdate'], 'Record does not have admin_id key.' );
            $this->assertArrayHasKey( 'old_status', $record['StatusUpdate'], 'Record does not have old_status key.' );
            $this->assertArrayHasKey( 'new_status', $record['StatusUpdate'], 'Record does not have new_status key.' );
            $this->assertArrayHasKey( 'timestamp', $record['StatusUpdate'], 'Record does not have timestamp key.' );

            $this->assertEqual( $record['StatusUpdate']['id'], 1, 'Record has incorrect id.' );
            $this->assertEqual( $record['StatusUpdate']['member_id'], 13, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['StatusUpdate']['admin_id'], 0, 'Record has incorrect admin_id.' );
            $this->assertEqual( $record['StatusUpdate']['old_status'], 1, 'Record has incorrect old_status.' );
            $this->assertEqual( $record['StatusUpdate']['new_status'], 2, 'Record has incorrect new_status.' );
            $this->assertGreaterThanOrEqual( $beforeTimestamp, strtotime($record['StatusUpdate']['timestamp']), 'Record has incorrect timestamp.' );
            $this->assertLessThanOrEqual( $afterTimestamp, strtotime($record['StatusUpdate']['timestamp']), 'Record has incorrect timestamp.' );
        }

        public function testCreateNewRecordWithNoOldStatus()
        {
            $beforeTimestamp = time();

            $this->assertTrue( $this->StatusUpdate->createNewRecord(8, 8, 0, 1), 'Valid data was not handled correctly.' );

            $afterTimestamp = time();

            $record = $this->StatusUpdate->findById(1);

            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );
            $this->assertArrayHasKey( 'StatusUpdate', $record, 'Record does not have status update key.' );

            $this->assertArrayHasKey( 'id', $record['StatusUpdate'], 'Record does not have id key.' );
            $this->assertArrayHasKey( 'member_id', $record['StatusUpdate'], 'Record does not have member_id key.' );
            $this->assertArrayHasKey( 'admin_id', $record['StatusUpdate'], 'Record does not have admin_id key.' );
            $this->assertArrayHasKey( 'old_status', $record['StatusUpdate'], 'Record does not have old_status key.' );
            $this->assertArrayHasKey( 'new_status', $record['StatusUpdate'], 'Record does not have new_status key.' );
            $this->assertArrayHasKey( 'timestamp', $record['StatusUpdate'], 'Record does not have timestamp key.' );

            $this->assertEqual( $record['StatusUpdate']['id'], 1, 'Record has incorrect id.' );
            $this->assertEqual( $record['StatusUpdate']['member_id'], 8, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['StatusUpdate']['admin_id'], 8, 'Record has incorrect admin_id.' );
            $this->assertEqual( $record['StatusUpdate']['old_status'], 0, 'Record has incorrect old_status.' );
            $this->assertEqual( $record['StatusUpdate']['new_status'], 1, 'Record has incorrect new_status.' );
            $this->assertGreaterThanOrEqual( $beforeTimestamp, strtotime($record['StatusUpdate']['timestamp']), 'Record has incorrect timestamp.' );
            $this->assertLessThanOrEqual( $afterTimestamp, strtotime($record['StatusUpdate']['timestamp']), 'Record has incorrect timestamp.' );
        }
    }

?>