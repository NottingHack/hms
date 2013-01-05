<?php

    App::uses('Status', 'Model');

    class StatusTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.Status', 'app.Member', 'app.Account', 'app.Pin', 'app.Group', 'app.GroupsMember' );

        public function setUp() 
        {
        	parent::setUp();
            $this->Status = ClassRegistry::init('Status');
        }

        public function testGetStatusSummaryAll()
        {
            $statusList = $this->Status->getStatusSummaryAll();

            $this->assertIdentical( count($statusList), $this->Status->find('count'), 'All statuses not included.' );
            $this->assertInternalType( 'array', $statusList, 'statusList is not an array.' );

            foreach ($statusList as $statusInfo)
            {
                $this->assertArrayHasKey( 'id', $statusInfo, 'Status has no id.' ); 
                $this->assertGreaterThan( 0, $statusInfo['id'], 'Status id is invalid.' );

                $this->assertArrayHasKey( 'name', $statusInfo, 'Status has no name.' ); 
                $this->assertArrayHasKey( 'description', $statusInfo, 'Status has no description.' ); 
                $this->assertArrayHasKey( 'count', $statusInfo, 'Status has no count.' ); 

                $expectedMemberCount = $this->Status->Member->find('count', array('conditions' => array('Member.member_status' => $statusInfo['id'])));
                $this->assertIdentical( $statusInfo['count'], $expectedMemberCount, 'Status count is incorrect for id: ' . $statusInfo['id'] . '.' );
            }
        }
    }

?>