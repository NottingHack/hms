<?php

	App::uses('EmailRecordsController', 'Controller');
	App::uses('EmailRecord', 'Model');

	App::build(array('TestController' => array('%s' . 'Test' . DS . 'Lib' . DS)), App::REGISTER);
	App::uses('HmsControllerTestBase', 'TestController');

	class EmailRecordsControllerTest extends HmsControllerTestBase
	{
		public $fixtures = array( 'app.Member', 'app.Status', 'app.Group', 'app.GroupsMember', 'app.Account', 'app.Pin', 'app.StatusUpdate', 'app.ForgotPassword', 'app.MailingLists', 'app.MailingListSubscriptions', 'app.EmailRecord' );

		public function setUp() 
        {
        	parent::setUp();

        	$this->EmailRecordsController = new EmailRecordsController();
        	$this->EmailRecordsController->constructClasses();
        }

        public function testIsAuthorized()
		{
			// Need fake requests for all the functions we need to test
			$fakeRequestDetails = array(
				array( 'name' => 'index', 	'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'membershipTeamMember' ) ),
				array( 'name' => 'view', 	'params' => array('ourId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'membershipTeamMember', 'normalMember' ) ),
				array( 'name' => 'view',	'params' => array('otherId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'membershipTeamMember' ) ),
			);

			$this->_testIsAuthorized($this->EmailRecordsController, $fakeRequestDetails);
		}

		public function testIndex()
		{
			$this->testAction('/emailRecords/index');

			$expectedEmails = array(
				array('id' => 4, 'member_id' => 4, 'subject' => 'Test email 2', 'timestamp' => '2013-06-05 13:51:04' ),
                array('id' => 8, 'member_id' => 4, 'subject' => 'Test email 7', 'timestamp' => '2013-04-13 22:51:09' ),
                array('id' => 3, 'member_id' => 3, 'subject' => 'Test email 2', 'timestamp' => '2013-03-23 05:42:21' ),
                array('id' => 6, 'member_id' => 4, 'subject' => 'Test email 5', 'timestamp' => '2013-03-16 15:15:23' ),
                array('id' => 7, 'member_id' => 4, 'subject' => 'Test email 6', 'timestamp' => '2013-02-19 17:47:26' ),
                array('id' => 5, 'member_id' => 4, 'subject' => 'Test email 3', 'timestamp' => '2013-01-23 09:23:42' ),
                array('id' => 2, 'member_id' => 2, 'subject' => 'Test email 1', 'timestamp' => '2012-12-17 19:20:00' ),
                array('id' => 1, 'member_id' => 1, 'subject' => 'Test email 1', 'timestamp' => '2012-12-17 19:19:59' ),
			);

			$this->_testViewVars($expectedEmails);
		}

		public function testViewInvalidData()
		{
			$this->controller = $this->generate('EmailRecords', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		)
            	)
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));

			// Should redirect
			$this->testAction('emailRecords/view/sdfasf');
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
		}

		public function testViewEmailsAnotherMember()
		{
			$this->_testViewMemberAsMemberId(4, 2);
			// Should redirect
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
		}

		public function testViewEmailsAsFullAccess()
		{
			$this->_testViewMemberAsMemberShouldSeeAll(4, 1);
		}

		public function testViewEmailsAsMemberAdmin()
		{
			$this->_testViewMemberAsMemberShouldSeeAll(4, 5);
		}

		public function testViewEmailsAsMembershipTeam()
		{
			$this->_testViewMemberAsMemberShouldSeeAll(3, 4);
		}

		public function testViewEmailsAsSameMember()
		{
			$this->_testViewMemberAsMemberShouldSeeAll(3, 3);
		}

		private function _getExpectedEmailsForMember($memberId)
		{
			switch ($memberId) 
			{
				case 3:
					return array(
							array('id' => 3, 'member_id' => 3, 'subject' => 'Test email 2', 'timestamp' => '2013-03-23 05:42:21' ),
						);

				case 4:
					return array(
							array('id' => 4, 'member_id' => 4, 'subject' => 'Test email 2', 'timestamp' => '2013-06-05 13:51:04' ),
			                array('id' => 8, 'member_id' => 4, 'subject' => 'Test email 7', 'timestamp' => '2013-04-13 22:51:09' ),
			                array('id' => 6, 'member_id' => 4, 'subject' => 'Test email 5', 'timestamp' => '2013-03-16 15:15:23' ),
			                array('id' => 7, 'member_id' => 4, 'subject' => 'Test email 6', 'timestamp' => '2013-02-19 17:47:26' ),
			                array('id' => 5, 'member_id' => 4, 'subject' => 'Test email 3', 'timestamp' => '2013-01-23 09:23:42' ),
						);
			}
			
			return null;
		}

		private function _testViewMemberAsMemberShouldSeeAll($memberId, $viewerId)
		{
			$this->_testViewMemberAsMemberId($memberId, $viewerId);
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->_testViewVars($this->_getExpectedEmailsForMember($memberId));
		}

		private function _testViewMemberAsMemberId($memberId, $viewerId)
		{
			$this->controller = $this->generate('EmailRecords', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		)
            	)
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue($viewerId));

			// Should redirect
			$this->testAction('emailRecords/view/' . $memberId);
		}

		private function _testViewVars($expectedEmails)
		{
			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );
			$this->assertArrayHasKey( 'emails', $this->vars, 'No view value for emails.' );
			$this->assertInternalType( 'array', $this->vars['emails'], 'Emails view value is not an array.' );

			$this->assertEqual( $this->vars['emails'], $expectedEmails, 'Emails vuew value is incorrect.' );
		}
	}

?>