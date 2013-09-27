<?php

	App::uses('MembersController', 'Controller');
	App::uses('Member', 'Model');
	App::uses('Account', 'Model');

	App::build(array('TestCase' => array('%s' . 'Test' . DS . 'Case' . DS . 'Model' . DS)), App::REGISTER);
	App::uses('EmailRecordTest', 'TestCase');

	App::build(array('TestController' => array('%s' . 'Test' . DS . 'Lib' . DS)), App::REGISTER);
	App::uses('HmsControllerTestBase', 'TestController');

	App::uses('PhpReader', 'Configure');
	Configure::config('default', new PhpReader());
	Configure::load('hms', 'default');

	class MembersControllerTest extends HmsControllerTestBase
	{
		public $fixtures = array( 'app.Member', 'app.Status', 'app.Group', 'app.GroupsMember', 'app.Account', 'app.Pin', 'app.StatusUpdate', 'app.ForgotPassword', 'app.MailingLists', 'app.MailingListSubscriptions', 'app.EmailRecord' );

		public function setUp() 
        {
        	parent::setUp();

            $this->MembersController = new MembersController();
            $this->MembersController->constructClasses();
        }

        private function _testMailingListView($expectedResults)
        {
        	$this->assertArrayHasKey( 'mailingLists', $this->vars, 'No view value called \'mailingLists\'.' );
        	$this->assertInternalType('array', $this->vars['mailingLists'], 'Mailing lists is not of array type.');

        	$this->assertArrayHasKey('total', $this->vars['mailingLists'], 'Mailing lists has no total.');
        	$this->assertEqual($this->vars['mailingLists']['total'], 2, 'Mailing list total is incorrect.');

        	$this->assertArrayHasKey('data', $this->vars['mailingLists'], 'Mailing lists has no data');

        	foreach ($this->vars['mailingLists']['data'] as $listData) 
        	{
        		$this->assertInternalType('array', $listData, 'List data is not of array type.');
        		$this->assertArrayHasKey('subscribed', $listData, 'List data has no subscribed info for id ' . $listData['id']);
        		$this->assertEqual($listData['subscribed'], $expectedResults[$listData['id']], 'List data subscribed has incorrect value for id ' . $listData['id']);
        	}
        }

        private function _constructMailingList()
        {
        	App::uses('MailingList', 'Model');
			$this->controller->expects($this->any())->method('getMailingList')->will($this->returnValue(new MailingList(false, null, 'test')));
			$this->controller->Member->mailingList = new MailingList(false, null, 'test');
        }

        private function _mockMemberEmail()
		{
			$this->controller = $this->generate('Members', array(
				'methods' => array(
					'getMailingList',
				),
				'components' => array(
					'Auth' => array(
						'user',
					),
					'Session' => array(
						'setFlash',
					),
				),
			));

			$this->_constructMailingList();

			$mockEmail = $this->getMock('CakeEmail');
			$this->controller->email = $mockEmail;

			return $mockEmail;
		}

		private function _testRegisterMailingListViewVars()
		{
			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );
			$this->assertArrayHasKey( 'mailingLists', $this->vars, 'No view value called \'memberList\'.' );
		}

		public function testIsAuthorized()
		{
			// Need fake requests for all the functions we need to test
			$fakeRequestDetails = array(
				array( 'name' => 'index', 							'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'membershipTeamMember' ) ),
				array( 'name' => 'listMembers', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'membershipTeamMember' ) ),
				array( 'name' => 'listMembersWithStatus', 			'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'membershipTeamMember' ) ),
				array( 'name' => 'emailMembersWithStatus', 			'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'search', 							'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'membershipTeamMember' ) ),
				array( 'name' => 'revokeMembership', 				'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'reinstateMembership', 			'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'acceptDetails', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'rejectDetails', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'approveMember', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'membershipTeamMember' ) ),
				array( 'name' => 'sendMembershipReminder', 			'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'membershipTeamMember' ) ),
				array( 'name' => 'sendContactDetailsReminder', 		'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'membershipTeamMember' ) ),
				array( 'name' => 'sendSoDetailsReminder', 			'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'membershipTeamMember' ) ),
				array( 'name' => 'addExistingMember', 				'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'uploadCsv', 						'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),

				array( 'name' => 'changePassword', 					'params' => array('ourId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember', 'membershipTeamMember' ) ),
				array( 'name' => 'changePassword', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),

				array( 'name' => 'view', 							'params' => array('ourId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember', 'membershipTeamMember' ) ),
				array( 'name' => 'view', 							'params' => array('otherId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'membershipTeamMember' ) ),

				array( 'name' => 'edit', 							'params' => array('ourId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember', 'membershipTeamMember' ) ),
				array( 'name' => 'edit', 							'params' => array('otherId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),

				array( 'name' => 'setupDetails', 					'params' => array('ourId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember', 'membershipTeamMember' ) ),
				array( 'name' => 'setupDetails', 					'params' => array('otherId'), 	'access' => array( 'fullAccessMember' ) ),
			);

			$this->_testIsAuthorized($this->MembersController, $fakeRequestDetails);
		}

		public function testIsRequestLocal()
		{
			$testIps = array(
				'10.0.0.32' => true,
				'192.0.0.4' => false,
			);

			foreach ($testIps as $ip => $expectedResult) 
			{
				$this->controller = $this->generate('Members', array(
		        	'methods' => array(
					    'getRequestIpAddress',
					),
		        ));

		        $this->controller->expects($this->once())->method('getRequestIpAddress')->will($this->returnValue($ip));
		        
		        $this->assertEqual($this->controller->isRequestLocal(), $expectedResult, 'Ip address ' . $ip . ' was not handled correctly.');
			}
		}

		private function _testBeforeFilterRegister($userId, $local, $result)
		{
			$this->controller = $this->generate('Members', array(
				'components' => array(
            		'Auth' => array(
            			'user',
            		)
            	),
	        	'methods' => array(
				    'isRequestLocal'
				),
	        ));

	        $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue($userId));
		    $this->controller->expects($this->any())->method('isRequestLocal')->will($this->returnValue($local));

		    $this->controller->beforeFilter();

			$actualResult = in_array('register', $this->controller->Auth->allowedActions);
			$this->assertEqual($actualResult, $result, sprintf('Combination handled incorrectly: userId - %s local - %s.', $userId, ($local ? 'true' : 'false')));
		}

		public function testBeforeFilterRegisterLocalMembershipAdmin()
		{
			$this->_testBeforeFilterRegister(5, true, true);
		}

		public function testBeforeFilterRegisterLocalMembershipTeam()
		{
			$this->_testBeforeFilterRegister(4, true, true);
		}

		public function testBeforeFilterRegisterNonLocalMembershipAdmin()
		{
			$this->_testBeforeFilterRegister(5, false, true);
		}

		public function testBeforeFilterRegisterNonLocalMembershipTeam()
		{
			$this->_testBeforeFilterRegister(4, false, true);
		}

		public function testBeforeFilterRegisterLocalCurrentMember()
		{
			$this->_testBeforeFilterRegister(3, true, true);
		}

		public function testBeforeFilterRegisterNonLocalCurrentMember()
		{
			$this->_testBeforeFilterRegister(3, false, false);
		}

		public function testBeforeFilter()
		{
			$prevAllowedActions = $this->MembersController->Auth->allowedActions;
			$this->assertIdentical( count($prevAllowedActions), 0, 'Prior to calling \'beforeFilter\' the allowed actions array was not empty.' );

			$this->MembersController->beforeFilter();
			$afterAllowedActions = $this->MembersController->Auth->allowedActions;
			$this->assertTrue( in_array('logout', $afterAllowedActions), 'Allowed actions does not contain \'logout\'.' );
			$this->assertTrue( in_array('login', $afterAllowedActions), 'Allowed actions does not contain \'login\'.' );
			$this->assertTrue( in_array('forgotPassword', $afterAllowedActions), 'Allowed actions does not contain \'forgot_password\'.' );
			$this->assertTrue( in_array('setupLogin', $afterAllowedActions), 'Allowed actions does not contain \'setupLogin\'.' );
			$this->assertTrue( in_array('setupDetails', $afterAllowedActions), 'Allowed actions does not contain \'setupDetails\'.' );
		}

		public function testIndex()
		{
			$this->testAction('/members/index');

			$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
			$this->assertArrayHasKey( 'memberStatusInfo', $this->vars, 'No view value called \'memberStatusInfo\'.' ); 
			$this->assertArrayHasKey( 'memberTotalCount', $this->vars, 'No view value called \'memberTotalCount\'.' ); 

			$this->assertInternalType( 'array', $this->vars['memberStatusInfo'], 'No array by the name of memberStatusInfo.' );
			$this->assertIdentical( count($this->vars['memberStatusInfo']), $this->MembersController->Member->Status->find('count'), 'All statuses are not included in index.' );


			$this->assertInternalType( 'int', $this->vars['memberTotalCount'], 'No int by the name of memberTotalCount.' );
			$this->assertIdentical( $this->vars['memberTotalCount'], $this->MembersController->Member->find('count'), 'Total member count is incorrect.' );
		}

		public function testListMembers()
		{
			$this->testAction('/members/listMembers');

			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );
			$this->assertArrayHasKey( 'memberList', $this->vars, 'No view value called \'memberList\'.' ); 

			$this->assertInternalType( 'array', $this->vars['memberList'], 'No array by the name of memberInfo' );
			$this->assertIdentical( count($this->vars['memberList']), $this->MembersController->Member->find('count'), 'All members not included.' );

			foreach ($this->vars['memberList'] as $memberInfo)
			{
				$this->assertArrayHasKey( 'id', $memberInfo, 'Member has no id.' ); 
				$this->assertGreaterThan( 0, $memberInfo['id'], 'Member id is invalid.' );

				$this->assertArrayHasKey( 'firstname', $memberInfo, 'Member has no firstname.' ); 
                $this->assertArrayHasKey( 'surname', $memberInfo, 'Member has no surname.' ); 
				$this->assertArrayHasKey( 'email', $memberInfo, 'Member has no email.' ); 
				$this->assertArrayHasKey( 'groups', $memberInfo, 'Member has no groups.' ); 

				foreach ($memberInfo['groups'] as $group) 
				{
					$this->assertArrayHasKey( 'id', $group, 'Group has no id.' ); 
					$this->assertArrayHasKey( 'description', $group, 'Group has no description.' );
					$this->assertInternalType( 'string', $group['description'], 'Group description is not a string.' );
				}

				$this->assertArrayHasKey( 'status', $memberInfo, 'Member has no status.' ); 
				$this->assertInternalType( 'array', $memberInfo['status'], 'No array by the name of status' );

				$this->assertArrayHasKey( 'actions', $memberInfo, 'Member has no actions.' ); 

				foreach ($memberInfo['actions'] as $action) 
				{
					$this->assertArrayHasKey( 'title', $action, 'Action has no title.' ); 
					$this->assertArrayHasKey( 'controller', $action, 'Action has no controller.' ); 
					$this->assertArrayHasKey( 'action', $action, 'Action has no action.' ); 
					$this->assertArrayHasKey( 'params', $action, 'Action has no params.' ); 					
				}
			}
		}

		public function testListMembersWithStatus()
		{
			$invalidStatus = array( 0, 7, -1 );

			foreach($invalidStatus as $status)
			{
				$this->testAction('/members/listMembersWithStatus/' . $status);
				$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
				$this->assertArrayHasKey( 'memberList', $this->vars, 'No view value called \'memberList\'.' ); 
				$this->assertArrayHasKey( 'statusInfo', $this->vars, 'No view value called \'statusInfo\'.' );
				$this->assertIdentical( count($this->vars['memberList']), 0, 'MemberList is not empty.' );

				$this->assertFalse( isset($this->vars['statusInfo']['id']), 'Status info has id.' );
				$this->assertFalse( isset($this->vars['statusInfo']['name']), 'Status info has name.' );
			}

			$validStatus = array( 1, 2, 3, 4, 5, 6 );
			foreach($invalidStatus as $status)
			{
				$this->testAction('/members/listMembersWithStatus/1');
				$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
				$this->assertArrayHasKey( 'memberList', $this->vars, 'No view value called \'memberList\'.' ); 
				$this->assertArrayHasKey( 'statusInfo', $this->vars, 'No view value called \'statusInfo\'.' );
				$this->assertGreaterThan( 0, count($this->vars['memberList']), 'MemberList is empty.' );

				foreach ($this->vars['memberList'] as $memberInfo)
				{
					$this->assertArrayHasKey( 'id', $memberInfo, 'Member has no id.' ); 
					$this->assertGreaterThan( 0, $memberInfo['id'], 'Member id is invalid.' );

					$this->assertArrayHasKey( 'firstname', $memberInfo, 'Member has no firstname.' ); 
                	$this->assertArrayHasKey( 'surname', $memberInfo, 'Member has no surname.' ); 
					$this->assertArrayHasKey( 'email', $memberInfo, 'Member has no email.' ); 
					$this->assertArrayHasKey( 'groups', $memberInfo, 'Member has no groups.' ); 

					foreach ($memberInfo['groups'] as $group) 
					{
						$this->assertArrayHasKey( 'id', $group, 'Group has no id.' ); 
						$this->assertArrayHasKey( 'description', $group, 'Group has no description.' );
						$this->assertInternalType( 'string', $group['description'], 'Group description is not a string.' );
					}

					$this->assertArrayHasKey( 'status', $memberInfo, 'Member has no status.' ); 
					$this->assertInternalType( 'array', $memberInfo['status'], 'No array by the name of status' );

					$this->assertArrayHasKey( 'actions', $memberInfo, 'Member has no actions.' ); 

					foreach ($memberInfo['actions'] as $action) 
					{
						$this->assertArrayHasKey( 'title', $action, 'Action has no title.' ); 
						$this->assertArrayHasKey( 'controller', $action, 'Action has no controller.' ); 
						$this->assertArrayHasKey( 'action', $action, 'Action has no action.' ); 
						$this->assertArrayHasKey( 'params', $action, 'Action has no params.' ); 					
					}
				}
			}
		}

		public function testSearch()
		{
			$data = array(
		        'query' => 'teleworm',
		    );

		    $this->testAction('/members/search', array('data' => $data, 'method' => 'get'));

			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );
			$this->assertArrayHasKey( 'memberList', $this->vars, 'No view value called \'memberList\'.' ); 

			$this->assertInternalType( 'array', $this->vars['memberList'], 'No array by the name of memberInfo' );
			$this->assertGreaterThan( 0, count($this->vars['memberList']), 'No search results returned.' );

			foreach ($this->vars['memberList'] as $memberInfo)
			{
				$this->assertArrayHasKey( 'id', $memberInfo, 'Member has no id.' ); 
				$this->assertGreaterThan( 0, $memberInfo['id'], 'Member id is invalid.' );

				$this->assertArrayHasKey( 'firstname', $memberInfo, 'Member has no firstname.' ); 
				$this->assertArrayHasKey( 'surname', $memberInfo, 'Member has no surname.' ); 
				$this->assertArrayHasKey( 'email', $memberInfo, 'Member has no email.' ); 
				$this->assertArrayHasKey( 'groups', $memberInfo, 'Member has no groups.' ); 

				foreach ($memberInfo['groups'] as $group) 
				{
					$this->assertArrayHasKey( 'id', $group, 'Group has no id.' ); 
					$this->assertArrayHasKey( 'description', $group, 'Group has no description.' );
					$this->assertInternalType( 'string', $group['description'], 'Group description is not a string.' );
				}

				$this->assertArrayHasKey( 'status', $memberInfo, 'Member has no status.' ); 
				$this->assertInternalType( 'array', $memberInfo['status'], 'No array by the name of status' );

				$this->assertArrayHasKey( 'actions', $memberInfo, 'Member has no actions.' ); 

				foreach ($memberInfo['actions'] as $action) 
				{
					$this->assertArrayHasKey( 'title', $action, 'Action has no title.' ); 
					$this->assertArrayHasKey( 'controller', $action, 'Action has no controller.' ); 
					$this->assertArrayHasKey( 'action', $action, 'Action has no action.' ); 
					$this->assertArrayHasKey( 'params', $action, 'Action has no params.' ); 					
				}
			}

			$data = array(
		    );


			// This should redirect
		    $this->testAction('/members/search', array('data' => $data, 'method' => 'get'));
		    $this->assertContains('/members/listMembers', $this->headers['Location']);
		}

		public function testRegisterNoData()
		{
			$this->_mockMemberEmail();

			// Test with no data
			$this->testAction('/members/register', array('data' => null, 'method' => null));

			$this->_testRegisterMailingListViewVars();
		}

		public function testRegisterNewMember()
		{
			// Test with a new e-mail
			$emailAddress = 'foo@bar.org';

			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->exactly(2))->method('config');
			$mockEmail->expects($this->exactly(2))->method('from');
			$mockEmail->expects($this->exactly(2))->method('sender');
			$mockEmail->expects($this->exactly(2))->method('emailFormat');
			$mockEmail->expects($this->exactly(2))->method('to');
			$mockEmail->expects($this->exactly(2))->method('subject');
			$mockEmail->expects($this->exactly(2))->method('template');
			$mockEmail->expects($this->exactly(2))->method('viewVars');
			$mockEmail->expects($this->exactly(2))->method('send')->will($this->returnValue(true));

			$mockEmail->expects($this->at(0))->method('config')->with('smtp');
			$mockEmail->expects($this->at(1))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(2))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(3))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(4))->method('to')->with(array('j.easterwood@googlemail.com'));
			$mockEmail->expects($this->at(5))->method('subject')->with('New Prospective Member Notification');
			$mockEmail->expects($this->at(6))->method('template')->with('notify_admins_member_added');
			$mockEmail->expects($this->at(7))->method('viewVars')->with(array('email' => $emailAddress));
			$mockEmail->expects($this->at(8))->method('send')->will($this->returnValue(true));

			$mockEmail->expects($this->at(9))->method('config')->with('smtp');
			$mockEmail->expects($this->at(10))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(11))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(12))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(13))->method('to')->with($emailAddress);
			$mockEmail->expects($this->at(14))->method('subject')->with('Welcome to Nottingham Hackspace');
			$mockEmail->expects($this->at(15))->method('template')->with('to_prospective_member');
			$mockEmail->expects($this->at(16))->method('viewVars')->with(array('memberId' => 15));
			$mockEmail->expects($this->at(17))->method('send')->will($this->returnValue(true));

			$this->controller->Session->expects($this->once())->method('setFlash')->with('Registration successful, please check your inbox.\nSuccessfully subscribed to Nottingham Hackspace The Other List\n');

			$expectedIdsAndSubjects = array(
				5 => 'New Prospective Member Notification',
				15 => 'Welcome to Nottingham Hackspace',
			);
			$data = array(
				'Member' => array(
					'email' => $emailAddress
				), 
				'MailingLists' => array(
					'MailingLists' => array(
						'455de2ac56'
					)
				)
			);
			$this->_testRecordedEmailAction('/members/register', $data, $expectedIdsAndSubjects);

			$this->_testRegisterMailingListViewVars();

			// Should have created a new member
			$memberInfo = $this->MembersController->Member->findByEmail('foo@bar.org');

			$this->assertInternalType( 'array', $memberInfo, 'Member record is not an array.' );
			$this->assertEqual( Hash::get($memberInfo, 'Member.member_id'), 15, 'Member has incorrect id.' );
			$this->assertEqual( Hash::get($memberInfo, 'Member.email'), $emailAddress, 'Member has incorrect email.' );

			$this->assertContains('/pages/home', $this->headers['Location']);
		}

		public function testRegisterMemberDoesNotUnsubscribe()
		{
			$emailAddress = 'alreadySubscribed@dayrep.com';

			$mockEmail = $this->_mockMemberEmail();

			$this->controller->Session->expects($this->once())->method('setFlash')->with('Registration successful, please check your inbox.');

			$this->testAction('/members/register', array('data' => array('Member' => array('email' => $emailAddress), 'MailingLists' => array('MailingLists' => '')), 'method' => 'post'));
		}

		
		public function testRegisterExistingPropspectiveMember()
		{
			// Test with an email address belonging to a member who is currently a prospective member
			$emailAddress = 'CherylLCarignan@teleworm.us';

			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->exactly(1))->method('config');
			$mockEmail->expects($this->exactly(1))->method('from');
			$mockEmail->expects($this->exactly(1))->method('sender');
			$mockEmail->expects($this->exactly(1))->method('emailFormat');
			$mockEmail->expects($this->exactly(1))->method('to');
			$mockEmail->expects($this->exactly(1))->method('subject');
			$mockEmail->expects($this->exactly(1))->method('template');
			$mockEmail->expects($this->exactly(1))->method('viewVars');
			$mockEmail->expects($this->exactly(1))->method('send')->will($this->returnValue(true));

			$mockEmail->expects($this->at(0))->method('config')->with('smtp');
			$mockEmail->expects($this->at(1))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(2))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(3))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(4))->method('to')->with($emailAddress);
			$mockEmail->expects($this->at(5))->method('subject')->with('Welcome to Nottingham Hackspace');
			$mockEmail->expects($this->at(6))->method('template')->with('to_prospective_member');
			$mockEmail->expects($this->at(7))->method('viewVars')->with(array('memberId' => 7));
			$mockEmail->expects($this->at(8))->method('send')->will($this->returnValue(true));

			$this->controller->Session->expects($this->once())->method('setFlash')->with('Registration successful, please check your inbox.');

			$expectedIdsAndSubjects = array(
				7 => 'Welcome to Nottingham Hackspace',
			);
			$data = array(
				'Member' => array(
					'email' => $emailAddress
				), 
				'MailingLists' => array(
					'MailingLists' => array(
					)
				)
			);
			$this->_testRecordedEmailAction('/members/register', $data, $expectedIdsAndSubjects);

			$this->_testRegisterMailingListViewVars();

			$this->assertContains('/pages/home', $this->headers['Location']);
		}

		public function testRegisterCurrentMember()
		{
			// Test with an email address belonging to a member who is currently an existing member
			$emailAddress = 'm.pryce@example.org';

			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$this->testAction('/members/register', array('data' => array('Member' => array('email' => $emailAddress)), 'method' => 'post'));

			$this->_testRegisterMailingListViewVars();

			$this->assertContains('/members/login', $this->headers['Location'], 'Redirect to login page did not occur.' );
		}

		public function testRegisterExMember()
		{
			// Test with an email address belonging to a member who is currently an ex member
			$emailAddress = 'g.garratte@foobar.org';

			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$this->testAction('/members/register', array('data' => array('Member' => array('email' => $emailAddress)), 'method' => 'post'));

			$this->_testRegisterMailingListViewVars();
			$this->assertContains('/members/login', $this->headers['Location'], 'Redirect to login page did not occur.' );
		}

		public function testSetupLoginWithInvalidMember()
		{
			$invalidMemberIds = array(1, 2, 3, 4, 5, 6, 9, 10, 11, 12, 13, 14);
			foreach ($invalidMemberIds as $memberId) 
			{
				$this->testAction('/members/setupLogin/' . $memberId);
				
				$this->assertTrue( isset($this->headers), 'Redirect to home page did not occur for member: ' . $memberId . '.' );
				$this->assertInternalType( 'array', $this->headers, 'Redirect to home page did not occur for member: ' . $memberId . '.' );
				$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect to home page did not occur for member: ' . $memberId . '.' );
				$this->assertContains('/pages/home', $this->headers['Location'], 'Redirect to home page did not occur for member: ' . $memberId . '.' );
			}

			// This shouldn't redirect, as it invalid data rather than an invalid action.
			$invalidData = array(
				8 => array(
					'Member' => array(
						'firstname' => 'Tony',
						'surname' => 'Benett',
						'username' => 'dayrep',
						'email' => 'aefsarwgesthbbs@easwrgtu.com',
						'password' => 'hunter2',
						'password_confirm' => 'hunter2'
					),
				),
			);

			foreach ($invalidData as $memberId => $data) 
			{
				$this->testAction('/members/setupLogin/' . $memberId, array('data' => $data, 'method' => 'post'));
				$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect to home page occurred for member: ' . $memberId . '.' );
			}
		}

		public function testSetupLoginWithValidMember()
		{
			$validData = array(
				7 => array(
					'Member' => array(
						'firstname' => 'Cheryl',
						'surname' => 'Thompson',
						'username' => 'dayrep',
						'email' => 'CherylLCarignan@teleworm.us',
						'password' => 'hunter2',
						'password_confirm' => 'hunter2'
					),
				),
				8 => array(
					'Member' => array(
						'firstname' => 'Melvin',
						'surname' => 'Android',
						'username' => 'retgar',
						'email' => 'MelvinJFerrell@dayrep.com',
						'password' => 'q1w2e3r4t5',
						'password_confirm' => 'q1w2e3r4t5'
					),
				),
			);

			foreach ($validData as $memberId => $data) 
			{
				$this->testAction('/members/setupLogin/' . $memberId, array('data' => $data, 'method' => 'post'));
				
				$this->assertTrue( isset($this->headers), 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				$this->assertInternalType( 'array', $this->headers, 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				$this->assertContains('/members/login', $this->headers['Location'], 'Redirect to login page did not occur for member: ' . $memberId . '.' );
			}
		}

		public function testSetupDetailsWithInvalidMembers()
		{
			$data = array(
                'Member' => array(
                    'address_1' => '27A The Mews',
                    'address_2' => 'Test Road',
                    'address_city' => 'Testington',
                    'address_postcode' => 'DE22 7BU',
                    'contact_number' => '07973 235786',
                )
            );

            $memberList = array( 1, 2, 3, 4, 5, 6, 7, 8, 11, 12, 13, 14 );

            foreach ($memberList as $memberId)
            {
            	$mockEmail = $this->_mockMemberEmail();

				$mockEmail->expects($this->never())->method('config');
				$mockEmail->expects($this->never())->method('from');
				$mockEmail->expects($this->never())->method('sender');
				$mockEmail->expects($this->never())->method('emailFormat');
				$mockEmail->expects($this->never())->method('to');
				$mockEmail->expects($this->never())->method('subject');
				$mockEmail->expects($this->never())->method('template');
				$mockEmail->expects($this->never())->method('viewVars');
				$mockEmail->expects($this->never())->method('send');

            	$this->testAction('/members/setupDetails/' . $memberId, array('data' => $data, 'method' => 'post'));
				
				$this->assertTrue( isset($this->headers), 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				$this->assertInternalType( 'array', $this->headers, 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				$this->assertContains('/pages/home', $this->headers['Location'], 'Redirect to login page did not occur for member: ' . $memberId . '.' );
            }

		}

		public function testSetupDetails()
		{
			$data = array(
                'Member' => array(
                    'address_1' => '27A The Mews',
                    'address_2' => 'Test Road',
                    'address_city' => 'Testington',
                    'address_postcode' => 'DE22 7BU',
                    'contact_number' => '07973 235786',
                )
            );

			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->exactly(2))->method('config');
			$mockEmail->expects($this->exactly(2))->method('from');
			$mockEmail->expects($this->exactly(2))->method('sender');
			$mockEmail->expects($this->exactly(2))->method('emailFormat');
			$mockEmail->expects($this->exactly(2))->method('to');
			$mockEmail->expects($this->exactly(2))->method('subject');
			$mockEmail->expects($this->exactly(2))->method('template');
			$mockEmail->expects($this->exactly(2))->method('viewVars');
			$mockEmail->expects($this->exactly(2))->method('send')->will($this->returnValue(true));

			$mockEmail->expects($this->at(0))->method('config')->with('smtp');
			$mockEmail->expects($this->at(1))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(2))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(3))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(4))->method('to')->with(array('j.easterwood@googlemail.com'));
			$mockEmail->expects($this->at(5))->method('subject')->with('New Member Contact Details');
			$mockEmail->expects($this->at(6))->method('template')->with('notify_admins_check_contact_details');
			$mockEmail->expects($this->at(7))->method('viewVars')->with(array('email' => 'DorothyDRussell@dayrep.com', 'id' => 9));
			$mockEmail->expects($this->at(8))->method('send')->will($this->returnValue(true));

			$mockEmail->expects($this->at(9))->method('config')->with('smtp');
			$mockEmail->expects($this->at(10))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(11))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(12))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(13))->method('to')->with('DorothyDRussell@dayrep.com');
			$mockEmail->expects($this->at(14))->method('subject')->with('Contact Information Completed');
			$mockEmail->expects($this->at(15))->method('template')->with('to_member_post_contact_update');
			$mockEmail->expects($this->at(16))->method('viewVars')->with(array());
			$mockEmail->expects($this->at(17))->method('send')->will($this->returnValue(true));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(9));

			$expectedIdsAndSubjects = array(
				5 => 'New Member Contact Details',
				9 => 'Contact Information Completed',
			);
			$this->_testRecordedEmailAction('/members/setupDetails/9', $data, $expectedIdsAndSubjects);

			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect did not occurred.' );
			$this->assertContains('/members/view/9', $this->headers['Location'], 'Redirect to member view did not occur.' );


			$record = $this->MembersController->Member->findByMemberId(9);

            $this->assertNotIdentical( $record, null, 'Could not find record for member id.' );
            $this->assertInternalType( 'array', $record, 'Could not find record for member id.' );

            $this->assertArrayHasKey( 'Member', $record, 'Record does not have member key for member id.' );

            $this->assertArrayHasKey( 'member_id', $record['Member'], 'Record Member does not have member_id key for member id.' );
            $this->assertArrayHasKey( 'address_1', $record['Member'], 'Record Member does not have address_1 key for member id.' );
            $this->assertIdentical( $record['Member']['address_1'], $data['Member']['address_1'], 'Record address_1 is incorrect for member id.' );

            $this->assertArrayHasKey( 'member_status', $record['Member'], 'Record Member does not have member_status key for member id.' );
            $this->assertEqual( $record['Member']['member_status'], Status::PRE_MEMBER_2, 'Record has incorrect status for member id.' );

            $this->assertArrayHasKey( 'address_2', $record['Member'], 'Record Member does not have address_2 key for member id.' );
            $this->assertEqual( $record['Member']['address_2'], $data['Member']['address_2'], 'Record has incorrect address_2 for member id.' );

            $this->assertArrayHasKey( 'address_city', $record['Member'], 'Record Member does not have address_city key for member id.' );
            $this->assertEqual( $record['Member']['address_city'], $data['Member']['address_city'], 'Record has incorrect address_city for member id.' );

            $this->assertArrayHasKey( 'address_postcode', $record['Member'], 'Record Member does not have address_postcode key for member id.' );
            $this->assertEqual( $record['Member']['address_postcode'], $data['Member']['address_postcode'], 'Record has incorrect address_postcode for member id.' );

            $this->assertArrayHasKey( 'contact_number', $record['Member'], 'Record Member does not have contact_number key for member id.' );
            $this->assertEqual( $record['Member']['contact_number'], $data['Member']['contact_number'], 'Record has incorrect contact_number for member id.' );
		}

		public function testRejectDetailsWithInvalidMembers()
		{
			$data = array(
                'MemberEmail' => array(
                    'subject' => 'Fooooooooooooooooooo',
                    'message' => 'barrrrrrrrrrrr',
                )
            );

			$memberList = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 13, 14 );

            foreach ($memberList as $memberId)
            {
            	$this->controller = $this->generate('Members', array(
	            	'components' => array(
	            		'Auth' => array(
	            			'user',
	            		)
	            	)
	            ));

				$mockEmail = $this->getMock('CakeEmail');
				$this->controller->email = $mockEmail;
				$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));

				$mockEmail->expects($this->never())->method('config');
				$mockEmail->expects($this->never())->method('from');
				$mockEmail->expects($this->never())->method('sender');
				$mockEmail->expects($this->never())->method('emailFormat');
				$mockEmail->expects($this->never())->method('to');
				$mockEmail->expects($this->never())->method('subject');
				$mockEmail->expects($this->never())->method('template');
				$mockEmail->expects($this->never())->method('viewVars');
				$mockEmail->expects($this->never())->method('send');

            	$this->testAction('/members/rejectDetails/' . $memberId, array('data' => $data, 'method' => 'post'));
				
				$this->assertTrue( isset($this->headers), 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				$this->assertInternalType( 'array', $this->headers, 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				$this->assertContains('/pages/home', $this->headers['Location'], 'Redirect to login page did not occur for member: ' . $memberId . '.' );
            }
		}

		public function testRejectDetails()
		{
			$data = array(
                'MemberEmail' => array(
                    'subject' => 'Fooooooooooooooooooo',
                    'message' => 'barrrrrrrrrrrr',
                )
            );

            $this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		)
            	)
            ));

			$mockEmail = $this->getMock('CakeEmail');
			$this->controller->email = $mockEmail;
            $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));

            $mockEmail->expects($this->exactly(1))->method('config');
			$mockEmail->expects($this->exactly(1))->method('from');
			$mockEmail->expects($this->exactly(1))->method('sender');
			$mockEmail->expects($this->exactly(1))->method('emailFormat');
			$mockEmail->expects($this->exactly(1))->method('to');
			$mockEmail->expects($this->exactly(1))->method('subject');
			$mockEmail->expects($this->exactly(1))->method('template');
			$mockEmail->expects($this->exactly(1))->method('viewVars');
			$mockEmail->expects($this->exactly(1))->method('send')->will($this->returnValue(true));

			$mockEmail->expects($this->at(0))->method('config')->with('smtp');
			$mockEmail->expects($this->at(1))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(2))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(3))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(4))->method('to')->with('BettyCParis@teleworm.us');
			$mockEmail->expects($this->at(5))->method('subject')->with('Issue With Contact Information');
			$mockEmail->expects($this->at(6))->method('template')->with('to_member_contact_details_rejected');
			$mockEmail->expects($this->at(7))->method('viewVars')->with(array('reason' => 'barrrrrrrrrrrr'));
			$mockEmail->expects($this->at(8))->method('send')->will($this->returnValue(true));

            $this->_testRecordedEmailAction('/members/rejectDetails/11', $data, array( 11 => 'Issue With Contact Information'));

			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect did not occurred.' );
			$this->assertContains('/members/view/11', $this->headers['Location'], 'Redirect to member view did not occur.' );
		}

		private function _testRecordedEmailAction($action, $data, $expectedData)
		{
			$emailRecord = ClassRegistry::init('EmailRecord');
			$emailRecord->setDataSource('test');

			$lastEmailRecord = $emailRecord->find('first', array( 'order' => 'EmailRecord.hms_email_id DESC') );
            $lastEmailRecordId = $lastEmailRecord['EmailRecord']['hms_email_id'];

			$beforeTime = time();
			if($data == null)
			{
				$this->testAction($action);	
			}
			else
			{
				$this->testAction($action, array('data' => $data, 'method' => 'post'));
			}
            
            $afterTime = time();

            $recordToCheck = $lastEmailRecordId + 1;
            foreach ($expectedData as $id => $subject) 
            {
            	$createdEmailRecord = $emailRecord->findByHmsEmailId($recordToCheck);
            	EmailRecordTest::validateRecord($this, $createdEmailRecord, $id, $subject, $beforeTime, $afterTime);
            	$recordToCheck++;
            }
		}

		public function testAcceptDetailsWithInvalidMembers()
		{
			$data = array(
                'Account' => array(
                    'account_id' => '2',
                )
            );

			$memberList = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 13, 14 );

            foreach ($memberList as $memberId)
            {
            	$this->controller = $this->generate('Members', array(
	            	'components' => array(
	            		'Auth' => array(
	            			'user',
	            		)
	            	)
	            ));

				$mockEmail = $this->getMock('CakeEmail');
				$this->controller->email = $mockEmail;
	            $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));

				$mockEmail->expects($this->never())->method('config');
				$mockEmail->expects($this->never())->method('from');
				$mockEmail->expects($this->never())->method('sender');
				$mockEmail->expects($this->never())->method('emailFormat');
				$mockEmail->expects($this->never())->method('to');
				$mockEmail->expects($this->never())->method('subject');
				$mockEmail->expects($this->never())->method('template');
				$mockEmail->expects($this->never())->method('viewVars');
				$mockEmail->expects($this->never())->method('send');

            	$this->testAction('/members/acceptDetails/' . $memberId, array('data' => $data, 'method' => 'post'));
				
				$this->assertTrue( isset($this->headers), 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				$this->assertInternalType( 'array', $this->headers, 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				$this->assertContains('/pages/home', $this->headers['Location'], 'Redirect to login page did not occur for member: ' . $memberId . '.' );

				$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
				$this->assertArrayHasKey( 'accounts', $this->vars, 'No view value called \'accounts\'.' );
				$this->assertEqual( $this->vars['accounts'], array( '-1' => 'Create new', '1' => 'Mathew Pryce', '2' => 'Annabelle Santini', '3' => 'Jessie Easterwood, Kelly Savala and Guy Viles', '6' => 'Guy Garrette', '7' => 'Ryan Miles', '8' => 'Evan Atkinson' ), 'Accounts view var not set correctly.' );

				$this->assertArrayHasKey('name', $this->vars, 'No view value called \'name\' for member: ' . $memberId . '.' );
            }
		}

		public function testAcceptDetails()
		{
			$data = array(
                'Account' => array(
                    'account_id' => '5',
                )
            );

			$this->controller = $this->generate('Members', array(
				'models' => array(
					'Member' => array(
						'getSoDetails', 
						'__construct'
					)
				),
				'components' => array(
            		'Auth' => array(
            			'user',
            		)
            	)
			));

			$mockEmail = $this->getMock('CakeEmail');
			$this->controller->email = $mockEmail;

			$fakePaymentRef = 'HSNOTTSTYX339RW4';
			$this->controller->Member->expects($this->exactly(2))->method('getSoDetails')->will($this->returnValue(array('firstname' => 'Roy', 'surname' => 'Forsman', 'email' => 'RoyJForsman@teleworm.us', 'paymentRef' => $fakePaymentRef)));
			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));

            $mockEmail->expects($this->exactly(2))->method('config');
			$mockEmail->expects($this->exactly(2))->method('from');
			$mockEmail->expects($this->exactly(2))->method('sender');
			$mockEmail->expects($this->exactly(2))->method('emailFormat');
			$mockEmail->expects($this->exactly(2))->method('to');
			$mockEmail->expects($this->exactly(2))->method('subject');
			$mockEmail->expects($this->exactly(2))->method('template');
			$mockEmail->expects($this->exactly(2))->method('viewVars');
			$mockEmail->expects($this->exactly(2))->method('send')->will($this->returnValue(true));

			$mockEmail->expects($this->at(0))->method('config')->with('smtp');
			$mockEmail->expects($this->at(1))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(2))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(3))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(4))->method('to')->with('RoyJForsman@teleworm.us');
			$mockEmail->expects($this->at(5))->method('subject')->with('Bank Details');
			$mockEmail->expects($this->at(6))->method('template')->with('to_member_so_details');
			$mockEmail->expects($this->at(7))->method('viewVars')->with(array('name' => 'Roy Forsman', 'paymentRef' => $fakePaymentRef, 'accountNum' => Configure::read('hms_so_accountNumber'), 'sortCode' => Configure::read('hms_so_sortCode'), 'accountName' => Configure::read('hms_so_accountName') ));
			$mockEmail->expects($this->at(8))->method('send')->will($this->returnValue(true));

			$mockEmail->expects($this->at(9))->method('config')->with('smtp');
			$mockEmail->expects($this->at(10))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(11))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(12))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(13))->method('to')->with(array('j.easterwood@googlemail.com'));
			$mockEmail->expects($this->at(14))->method('subject')->with('Impending Payment');
			$mockEmail->expects($this->at(15))->method('template')->with('notify_admins_payment_incoming');
			$mockEmail->expects($this->at(16))->method('viewVars')->with(array('memberId' => '12', 'memberName' => 'Roy Forsman', 'memberEmail' => 'RoyJForsman@teleworm.us', 'memberPayRef' => $fakePaymentRef));
			$mockEmail->expects($this->at(17))->method('send')->will($this->returnValue(true));

			$expectedIdsAndSubjects = array(
				12 => 'Bank Details',
				5 => 'Impending Payment',
			);
			$this->_testRecordedEmailAction('/members/acceptDetails/12', $data, $expectedIdsAndSubjects);

			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect did not occurred.' );
			$this->assertContains('/members/view/12', $this->headers['Location'], 'Redirect to member view did not occur.' );

			$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
			$this->assertArrayHasKey( 'accounts', $this->vars, 'No view value called \'accounts\'.' );
			$this->assertEqual( $this->vars['accounts'], array( '-1' => 'Create new', '1' => 'Mathew Pryce', '2' => 'Annabelle Santini', '3' => 'Jessie Easterwood, Kelly Savala and Guy Viles', '6' => 'Guy Garrette', '7' => 'Ryan Miles', '8' => 'Evan Atkinson' ), 'Accounts view var not set correctly.' );
		}

		public function testApproveMemberWithInvalidMembers()
		{

			$memberList = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 );

            foreach ($memberList as $memberId)
            {
            	$mockEmail = $this->_mockMemberEmail();

				$mockEmail->expects($this->never())->method('config');
				$mockEmail->expects($this->never())->method('from');
				$mockEmail->expects($this->never())->method('sender');
				$mockEmail->expects($this->never())->method('emailFormat');
				$mockEmail->expects($this->never())->method('to');
				$mockEmail->expects($this->never())->method('subject');
				$mockEmail->expects($this->never())->method('template');
				$mockEmail->expects($this->never())->method('viewVars');
				$mockEmail->expects($this->never())->method('send');

            	$this->testAction('/members/approveMember/' . $memberId);
				
				$this->assertTrue( isset($this->headers), 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				$this->assertInternalType( 'array', $this->headers, 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect to login page did not occur for member: ' . $memberId . '.' );
				//$this->assertContains('/pages/home', $this->headers['Location'], 'Redirect to login page did not occur for member: ' . $memberId . '.' );
            }
		}

		public function testApproveMember()
		{
            $this->controller = $this->generate('Members', array(
            	'models' => array(
            		'Member' => array(
            			'getApproveDetails', 
            			'__construct',
            		)
            	),
            	'components' => array(
            		'Auth' => array(
            			'user',
            		)
            	)
            ));

			$mockEmail = $this->getMock('CakeEmail');
			$this->controller->email = $mockEmail;
            $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));
            $this->controller->Member->expects($this->exactly(1))->method('getApproveDetails')->will($this->returnValue(array('firstname' => 'Ryan', 'surname' => 'Miles', 'id' => 13, 'email' => 'RyanMiles@dayrep.com', 'pin' => '2234')));

            $mockEmail->expects($this->exactly(2))->method('config');
			$mockEmail->expects($this->exactly(2))->method('from');
			$mockEmail->expects($this->exactly(2))->method('sender');
			$mockEmail->expects($this->exactly(2))->method('emailFormat');
			$mockEmail->expects($this->exactly(2))->method('to');
			$mockEmail->expects($this->exactly(2))->method('subject');
			$mockEmail->expects($this->exactly(2))->method('template');
			$mockEmail->expects($this->exactly(2))->method('viewVars');
			$mockEmail->expects($this->exactly(2))->method('send')->will($this->returnValue(true));

			$mockEmail->expects($this->at(0))->method('config')->with('smtp');
			$mockEmail->expects($this->at(1))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(2))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(3))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(4))->method('to')->with(array('j.easterwood@googlemail.com'));
			$mockEmail->expects($this->at(5))->method('subject')->with('Member Approved');
			$mockEmail->expects($this->at(6))->method('template')->with('notify_admins_member_approved');
			$mockEmail->expects($this->at(7))->method('viewVars')->with(array('memberName' => 'Ryan Miles', 'memberId' => 13, 'memberEmail' => 'RyanMiles@dayrep.com', 'memberPin' => '2234'));
			$mockEmail->expects($this->at(8))->method('send')->will($this->returnValue(true));

			$mockEmail->expects($this->at(9))->method('config')->with('smtp');
			$mockEmail->expects($this->at(10))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(11))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(12))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(13))->method('to')->with('RyanMiles@dayrep.com');
			$mockEmail->expects($this->at(14))->method('subject')->with('Membership Complete');
			$mockEmail->expects($this->at(15))->method('template')->with('to_member_access_details');
			$mockEmail->expects($this->at(16))->method('viewVars')->with(array('manLink' => Configure::read('hms_help_manual_url'), 'outerDoorCode' => Configure::read('hms_access_street_door'), 'innerDoorCode' => Configure::read('hms_access_inner_door'), 'wifiSsid' => Configure::read('hms_access_wifi_ssid'), 'wifiPass' => Configure::read('hms_access_wifi_password')));
			$mockEmail->expects($this->at(17))->method('send')->will($this->returnValue(true));

			$expectedIdsAndSubjects = array(
				5 => 'Member Approved',
				13 => 'Membership Complete',
			);
			$this->_testRecordedEmailAction('/members/approveMember/13', null, $expectedIdsAndSubjects);

			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect did not occurr.' );
		}

		public function testChangePasswordNoInputOwnAccount()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		)
            	)
            ));

            $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(3));

			$this->testAction('/members/changePassword/3');

			$this->assertIdentical( count($this->vars), 3, 'Unexpected number of view vars.' );
			$this->assertArrayHasKey( 'id', $this->vars, 'No id value in view vars.' );
			$this->assertArrayHasKey( 'name', $this->vars, 'No name value in view vars.' );
			$this->assertArrayHasKey( 'ownAccount', $this->vars, 'No ownAccount value in view vars.' );
			$this->assertIdentical( $this->vars['id'], '3', 'Incorrect id value.' );
			$this->assertIdentical( $this->vars['name'], 'buntweyr', 'Incorrect name value.' );
			$this->assertIdentical( $this->vars['ownAccount'], true, 'Incorrect ownAccount value.' );
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect occurred.' );
		}

		public function testChangePasswordNoInputMemberAdmin()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		)
            	)
            ));

            $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));

            $this->testAction('/members/changePassword/5');

			$this->assertIdentical( count($this->vars), 3, 'Unexpected number of view vars.' );
			$this->assertArrayHasKey( 'id', $this->vars, 'No id value in view vars.' );
			$this->assertArrayHasKey( 'name', $this->vars, 'No name value in view vars.' );
			$this->assertArrayHasKey( 'ownAccount', $this->vars, 'No ownAccount value in view vars.' );
			$this->assertIdentical( $this->vars['id'], '5', 'Incorrect id value.' );
			$this->assertIdentical( $this->vars['name'], 'chollertonbanker', 'Incorrect name value.' );
			$this->assertIdentical( $this->vars['ownAccount'], true, 'Incorrect ownAccount value.' );
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect occurred.' );
		}

		public function testChangePasswordNoInputMemberAdminOwnAccount()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		)
            	)
            ));

            $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));

            $this->testAction('/members/changePassword/2');

			$this->assertIdentical( count($this->vars), 3, 'Unexpected number of view vars.' );
			$this->assertArrayHasKey( 'id', $this->vars, 'No id value in view vars.' );
			$this->assertArrayHasKey( 'name', $this->vars, 'No name value in view vars.' );
			$this->assertArrayHasKey( 'ownAccount', $this->vars, 'No ownAccount value in view vars.' );
			$this->assertIdentical( $this->vars['id'], '2', 'Incorrect id value.' );
			$this->assertIdentical( $this->vars['name'], 'pecanpaella', 'Incorrect name value.' );
			$this->assertIdentical( $this->vars['ownAccount'], false, 'Incorrect ownAccount value.' );
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect occurred.' );
		}

		public function testChangePasswordRedirects()
		{
			// A non logged in user can't do this
			$this->testAction('/members/changePassword/3');
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );

			// A logged in user that is a non member-admin cannot change the password of another member.
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		)
            	)
            ));

            $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(3));
			$this->testAction('/members/changePassword/2');
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
		}

		public function testChangePasswordMemberCanChangeOwnPassword()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		)
            	)
            ));

            $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(2));

            $data = array(
            	'ChangePassword' => array(
            		'current_password' => 'hunter2',
            		'new_password' => 'c*6vUc88i1"C=3$',
            		'new_password_confirm' => 'c*6vUc88i1"C=3$',
            	)
            );
			$this->testAction('/members/changePassword/2', array('data' => $data, 'method' => 'post'));
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
			$this->assertContains('/members/view/2', $this->headers['Location'], 'Redirect to member view did not occur.' );
		}

		public function testChangePasswordMemberAdminCanChangeOtherPassword()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		)
            	)
            ));

            $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));

            $data = array(
            	'ChangePassword' => array(
            		'current_password' => 'hunter2',
            		'new_password' => 'c*6vUc88i1"C=3$',
            		'new_password_confirm' => 'c*6vUc88i1"C=3$',
            	)
            );
			$this->testAction('/members/changePassword/3', array('data' => $data, 'method' => 'post'));
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
			$this->assertContains('/members/view/3', $this->headers['Location'], 'Redirect to member view did not occur.' );
		}

		public function testChangePasswordMemberAdminCanChangeOwnPassword()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		)
            	)
            ));

            $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));

            $data = array(
            	'ChangePassword' => array(
            		'current_password' => 'hunter2',
            		'new_password' => 'c*6vUc88i1"C=3$',
            		'new_password_confirm' => 'c*6vUc88i1"C=3$',
            	)
            );
			$this->testAction('/members/changePassword/5', array('data' => $data, 'method' => 'post'));
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
			$this->assertContains('/members/view/5', $this->headers['Location'], 'Redirect to member view did not occur.' );
		}

		public function testForgotPasswordNoInput()
		{
			$mockEmail = $this->_mockMemberEmail();
			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$this->testAction('/members/forgotPassword/');

			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view vars.' );
			$this->assertArrayHasKey( 'createRequest', $this->vars, 'No createRequest value in view vars.' );
			$this->assertIdentical( $this->vars['createRequest'], true, 'Incorrect createRequest value.' );
		}

		public function testForgotPasswordInvalidGuid()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$this->testAction('/members/forgotPassword/awfefwfargaez');

			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view vars.' );
			$this->assertArrayHasKey( 'createRequest', $this->vars, 'No createRequest value in view vars.' );
			$this->assertIdentical( $this->vars['createRequest'], true, 'Incorrect createRequest value.' );
		}

		public function testForgotPasswordCreate()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->exactly(1))->method('config');
			$mockEmail->expects($this->exactly(1))->method('from');
			$mockEmail->expects($this->exactly(1))->method('sender');
			$mockEmail->expects($this->exactly(1))->method('emailFormat');
			$mockEmail->expects($this->exactly(1))->method('to');
			$mockEmail->expects($this->exactly(1))->method('subject');
			$mockEmail->expects($this->exactly(1))->method('template');
			$mockEmail->expects($this->exactly(1))->method('viewVars');
			$mockEmail->expects($this->exactly(1))->method('send');

			$mockEmail->expects($this->at(0))->method('config')->with('smtp');
			$mockEmail->expects($this->at(1))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(2))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(3))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(4))->method('to')->with('a.santini@hotmail.com');
			$mockEmail->expects($this->at(5))->method('subject')->with('Password Reset Request');
			$mockEmail->expects($this->at(6))->method('template')->with('forgot_password');
			$mockEmail->expects($this->at(8))->method('send')->will($this->returnValue(true));

			$data = array(
				'ForgotPassword' => array(
					'email' => 'a.santini@hotmail.com',
				),
			);

			$expectedIdsAndSubjects = array(
				2 => 'Password Reset Request',
			);
			$this->_testRecordedEmailAction('/members/forgotPassword', $data, $expectedIdsAndSubjects);

			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view vars.' );
			$this->assertArrayHasKey( 'createRequest', $this->vars, 'No createRequest value in view vars.' );
			$this->assertIdentical( $this->vars['createRequest'], true, 'Incorrect createRequest value.' );
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
			$this->assertContains('/pages/forgot_password_sent', $this->headers['Location'], 'Redirect to forgot password sent view did not occur.' );
		}

		public function testForgotPasswordCreateInvalidMember()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$data = array(
				'ForgotPassword' => array(
					'email' => 'CherylLCarignan@teleworm.us',
				),
			);

			$this->testAction('/members/forgotPassword', array('data' => $data, 'method' => 'post'));

			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view vars.' );
			$this->assertArrayHasKey( 'createRequest', $this->vars, 'No createRequest value in view vars.' );
			$this->assertIdentical( $this->vars['createRequest'], true, 'Incorrect createRequest value.' );
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
			$this->assertContains('/pages/home', $this->headers['Location'], 'Redirect to home sent view did not occur.' );
		}

		public function testForgotPasswordCreateInvalidEmail()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$data = array(
				'ForgotPassword' => array(
					'email' => 'totallyfake@gmail.com',
				),
			);

			$this->testAction('/members/forgotPassword', array('data' => $data, 'method' => 'post'));

			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view vars.' );
			$this->assertArrayHasKey( 'createRequest', $this->vars, 'No createRequest value in view vars.' );
			$this->assertIdentical( $this->vars['createRequest'], true, 'Incorrect createRequest value.' );
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
			$this->assertContains('/pages/home', $this->headers['Location'], 'Redirect to home view did not occur.' );
		}

		public function testForgotPasswordComplete()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$data = array(
				'ForgotPassword' => array(
					'email' => 'a.santini@hotmail.com',
					'new_password' => 'totally1337password',
					'new_password_confirm' => 'totally1337password',
				),
			);

			$this->testAction('/members/forgotPassword/50b104e4-33f8-4821-b756-5e100a000005', array('data' => $data, 'method' => 'post'));

			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view vars.' );
			$this->assertArrayHasKey( 'createRequest', $this->vars, 'No createRequest value in view vars.' );
			$this->assertIdentical( $this->vars['createRequest'], false, 'Incorrect createRequest value.' );
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
			$this->assertContains('/members/login', $this->headers['Location'], 'Redirect to login view did not occur.' );
		}

		public function testForgotPasswordCompleteNonMatchingPasswords()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$data = array(
				'ForgotPassword' => array(
					'email' => 'a.santini@hotmail.com',
					'new_password' => 'totally1337password',
					'new_password_confirm' => 'non1337password',
				),
			);

			$this->testAction('/members/forgotPassword/50b104e4-33f8-4821-b756-5e100a000005', array('data' => $data, 'method' => 'post'));

			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view vars.' );
			$this->assertArrayHasKey( 'createRequest', $this->vars, 'No createRequest value in view vars.' );
			$this->assertIdentical( $this->vars['createRequest'], false, 'Incorrect createRequest value.' );
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
			$this->assertContains('/pages/forgot_password_error', $this->headers['Location'], 'Redirect to forgot password error view did not occur.' );
		}

		public function testForgotPasswordCompleteInvalidGuid()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$data = array(
				'ForgotPassword' => array(
					'email' => 'a.santini@hotmail.com',
					'new_password' => 'totally1337password',
					'new_password_confirm' => 'totally1337password',
				),
			);

			$this->testAction('/members/forgotPassword/50b104e4-33f8-4821-b756', array('data' => $data, 'method' => 'post'));

			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view vars.' );
			$this->assertArrayHasKey( 'createRequest', $this->vars, 'No createRequest value in view vars.' );
			$this->assertIdentical( $this->vars['createRequest'], true, 'Incorrect createRequest value.' );
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
			$this->assertContains('/pages/home', $this->headers['Location'], 'Redirect to forgot password error view did not occur.' );
		}

		public function testForgotPasswordCompleteExpiredGuid()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$data = array(
				'ForgotPassword' => array(
					'email' => 'm.pryce@example.org',
					'new_password' => 'totally1337password',
					'new_password_confirm' => 'totally1337password',
				),
			);

			$this->testAction('/members/forgotPassword/50b0ec45-8984-48b8-ac8a-5db90a000005', array('data' => $data, 'method' => 'post'));

			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view vars.' );
			$this->assertArrayHasKey( 'createRequest', $this->vars, 'No createRequest value in view vars.' );
			$this->assertIdentical( $this->vars['createRequest'], false, 'Incorrect createRequest value.' );
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
			$this->assertContains('/pages/forgot_password_error', $this->headers['Location'], 'Redirect to forgot password error view did not occur.' );
		}

		public function testForgotPasswordCompleteTimedoutRequest()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$data = array(
				'ForgotPassword' => array(
					'email' => 'g.viles@gmail.com',
					'new_password' => 'totally1337password',
					'new_password_confirm' => 'totally1337password',
				),
			);

			$this->testAction('/members/forgotPassword/50be19c8-0968-43ba-be1b-0990bcda665d', array('data' => $data, 'method' => 'post'));

			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view vars.' );
			$this->assertArrayHasKey( 'createRequest', $this->vars, 'No createRequest value in view vars.' );
			$this->assertIdentical( $this->vars['createRequest'], false, 'Incorrect createRequest value.' );
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
			$this->assertContains('/pages/forgot_password_error', $this->headers['Location'], 'Redirect to forgot password error view did not occur.' );
		}

		public function testSendMembershipReminderInvalidData()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$this->testAction('/members/sendMembershipReminder/sdfsfgresr');
		}

		public function testSendMembershipReminder()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->once())->method('config');
			$mockEmail->expects($this->once())->method('from');
			$mockEmail->expects($this->once())->method('sender');
			$mockEmail->expects($this->once())->method('emailFormat');
			$mockEmail->expects($this->once())->method('to');
			$mockEmail->expects($this->once())->method('subject');
			$mockEmail->expects($this->once())->method('template');
			$mockEmail->expects($this->once())->method('viewVars');
			$mockEmail->expects($this->once())->method('send');

			$mockEmail->expects($this->at(0))->method('config')->with('smtp');
			$mockEmail->expects($this->at(1))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(2))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(3))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(4))->method('to')->with('CherylLCarignan@teleworm.us');
			$mockEmail->expects($this->at(5))->method('subject')->with('Welcome to Nottingham Hackspace');
			$mockEmail->expects($this->at(6))->method('template')->with('to_prospective_member');
			$mockEmail->expects($this->at(7))->method('viewVars')->with(array('memberId' => 7));
			$mockEmail->expects($this->at(8))->method('send')->will($this->returnValue(true));

			$expectedIdsAndSubjects = array(
				7 => 'Welcome to Nottingham Hackspace',
			);
			$this->_testRecordedEmailAction('/members/sendMembershipReminder/7', null, $expectedIdsAndSubjects);
		}

		public function testSendSoDetailsReminderInvalidData()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$this->testAction('/members/sendSoDetailsReminder/sdfsfgresr');
		}

		public function testSendSoDetailsReminder()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->once())->method('config');
			$mockEmail->expects($this->once())->method('from');
			$mockEmail->expects($this->once())->method('sender');
			$mockEmail->expects($this->once())->method('emailFormat');
			$mockEmail->expects($this->once())->method('to');
			$mockEmail->expects($this->once())->method('subject');
			$mockEmail->expects($this->once())->method('template');
			$mockEmail->expects($this->once())->method('viewVars');
			$mockEmail->expects($this->once())->method('send');

			$mockEmail->expects($this->at(0))->method('config')->with('smtp');
			$mockEmail->expects($this->at(1))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(2))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(3))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(4))->method('to')->with('RyanMiles@dayrep.com');
			$mockEmail->expects($this->at(5))->method('subject')->with('Bank Details');
			$mockEmail->expects($this->at(6))->method('template')->with('to_member_so_details');
			$mockEmail->expects($this->at(7))->method('viewVars')->with(array('name' => 'Ryan Miles', 'paymentRef' => 'HSNOTTSFGXWGKF48', 'accountNum' => Configure::read('hms_so_accountNumber'), 'sortCode' => Configure::read('hms_so_sortCode'), 'accountName' => Configure::read('hms_so_accountName') ));
			$mockEmail->expects($this->at(8))->method('send')->will($this->returnValue(true));

			$expectedIdsAndSubjects = array(
				13 => 'Bank Details',
			);
			$this->_testRecordedEmailAction('/members/sendSoDetailsReminder/13', null, $expectedIdsAndSubjects);
		}

		public function testSendContactDetailsReminderInvalidData()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$this->testAction('/members/sendContactDetailsReminder/sdfsfgresr');
		}

		public function testSendContactDetailsReminder()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->once())->method('config');
			$mockEmail->expects($this->once())->method('from');
			$mockEmail->expects($this->once())->method('sender');
			$mockEmail->expects($this->once())->method('emailFormat');
			$mockEmail->expects($this->once())->method('to');
			$mockEmail->expects($this->once())->method('subject');
			$mockEmail->expects($this->once())->method('template');
			$mockEmail->expects($this->once())->method('viewVars');
			$mockEmail->expects($this->once())->method('send');

			$mockEmail->expects($this->at(0))->method('config')->with('smtp');
			$mockEmail->expects($this->at(1))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(2))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(3))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(4))->method('to')->with('HugoJLorenz@dayrep.com');
			$mockEmail->expects($this->at(5))->method('subject')->with('Membership Info');
			$mockEmail->expects($this->at(6))->method('template')->with('to_member_contact_details_reminder');
			$mockEmail->expects($this->at(7))->method('viewVars')->with(array('memberId' => 10));
			$mockEmail->expects($this->at(8))->method('send')->will($this->returnValue(true));

			$expectedIdsAndSubjects = array(
				10 => 'Membership Info',
			);
			$this->_testRecordedEmailAction('/members/sendContactDetailsReminder/10', null, $expectedIdsAndSubjects);
		}

		public function testEmailMembersWithStatusNoData()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$this->testAction('/members/emailMembersWithStatus/1');

			$this->_testEmailMembersWithStatusVewVars();
		}

		public function testEmailMembersWithStatusInvalidStatus()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$this->testAction('/members/emailMembersWithStatus/0');

			$this->_testEmailMembersWithStatusVewVars();
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
		}

		public function testEmailMemberWithStatusInvalidData()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->never())->method('config');
			$mockEmail->expects($this->never())->method('from');
			$mockEmail->expects($this->never())->method('sender');
			$mockEmail->expects($this->never())->method('emailFormat');
			$mockEmail->expects($this->never())->method('to');
			$mockEmail->expects($this->never())->method('subject');
			$mockEmail->expects($this->never())->method('template');
			$mockEmail->expects($this->never())->method('viewVars');
			$mockEmail->expects($this->never())->method('send');

			$data = array(
				'MemberEmail' => array(
					'subject' => '',
					'message' => '',
				),
			);
			$this->testAction('/members/emailMembersWithStatus/2', array('data' => $data, 'method' => 'post'));

			$this->_testEmailMembersWithStatusVewVars();
			//$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
		}

		public function testEmailMemberWithStatusValidData()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->exactly(5))->method('config');
			$mockEmail->expects($this->exactly(5))->method('from');
			$mockEmail->expects($this->exactly(5))->method('sender');
			$mockEmail->expects($this->exactly(5))->method('emailFormat');
			$mockEmail->expects($this->exactly(5))->method('to');
			$mockEmail->expects($this->exactly(5))->method('subject');
			$mockEmail->expects($this->exactly(5))->method('template');
			$mockEmail->expects($this->exactly(5))->method('viewVars');
			$mockEmail->expects($this->exactly(5))->method('send');

			$emails = array(
				'm.pryce@example.org',
				'a.santini@hotmail.com',
				'g.viles@gmail.com',
				'k.savala@yahoo.co.uk',
				'j.easterwood@googlemail.com',
			);

			$data = array(
				'MemberEmail' => array(
					'subject' => 'Hello!',
					'message' => 'This is a test message :)',
				),
			);

			$index = 0;
			foreach ($emails as $email) 
			{
				$mockEmail->expects($this->at($index++))->method('config')->with('smtp');
				$mockEmail->expects($this->at($index++))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
				$mockEmail->expects($this->at($index++))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
				$mockEmail->expects($this->at($index++))->method('emailFormat')->with('html');
				$mockEmail->expects($this->at($index++))->method('to')->with($email);
				$mockEmail->expects($this->at($index++))->method('subject')->with($data['MemberEmail']['subject']);
				$mockEmail->expects($this->at($index++))->method('template')->with('default');
				$mockEmail->expects($this->at($index++))->method('viewVars')->with(array('content' => $data['MemberEmail']['message']));
				$mockEmail->expects($this->at($index++))->method('send')->will($this->returnValue(true));
			}

			$this->testAction('/members/emailMembersWithStatus/5', array('data' => $data, 'method' => 'post'));

			$this->_testEmailMembersWithStatusVewVars();
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
		}

		public function testEmailMemberWithStatusValidDataDodgyEmail()
		{
			$mockEmail = $this->_mockMemberEmail();

			$mockEmail->expects($this->exactly(5))->method('config');
			$mockEmail->expects($this->exactly(5))->method('from');
			$mockEmail->expects($this->exactly(5))->method('sender');
			$mockEmail->expects($this->exactly(5))->method('emailFormat');
			$mockEmail->expects($this->exactly(5))->method('to');
			$mockEmail->expects($this->exactly(5))->method('subject');
			$mockEmail->expects($this->exactly(5))->method('template');
			$mockEmail->expects($this->exactly(5))->method('viewVars');
			$mockEmail->expects($this->exactly(5))->method('send');

			$emails = array(
				'm.pryce@example.org',
				'a.santini@hotmail.com',
				'g.viles@gmail.com',
				'k.savala@yahoo.co.uk',
				'j.easterwood@googlemail.com',
			);

			$data = array(
				'MemberEmail' => array(
					'subject' => 'Hello!',
					'message' => 'This is a test message :)',
				),
			);

			$index = 0;
			$count = 0;
			foreach ($emails as $email) 
			{
				$mockEmail->expects($this->at($index++))->method('config')->with('smtp');
				$mockEmail->expects($this->at($index++))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
				$mockEmail->expects($this->at($index++))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
				$mockEmail->expects($this->at($index++))->method('emailFormat')->with('html');
				$mockEmail->expects($this->at($index++))->method('to')->with($email);
				$mockEmail->expects($this->at($index++))->method('subject')->with($data['MemberEmail']['subject']);
				$mockEmail->expects($this->at($index++))->method('template')->with('default');
				$mockEmail->expects($this->at($index++))->method('viewVars')->with(array('content' => $data['MemberEmail']['message']));
				$mockEmail->expects($this->at($index++))->method('send')->will($this->returnValue(($count % 2) == 0));
				$count++;
			}

			$this->testAction('/members/emailMembersWithStatus/5', array('data' => $data, 'method' => 'post'));

			$this->_testEmailMembersWithStatusVewVars();
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
		}

		public function testViewInvalidData()
		{
			// Should redirect
			$this->testAction('members/view/0');
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
		}

		public function testViewMemberAsAnotherMember()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		)
            	)
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(2));

			// Should redirect
			$this->testAction('members/view/4');
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
		}

		public function testViewMemberAsMemberAdmin()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		),
            		'Nav' => array(
            			'add',
            		)
            	),
            	'methods' => array(
            		'getMailingList',
            	),
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));

			$this->_constructMailingList();

			$this->controller->Nav->expects($this->exactly(4))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('View Email History', 'emailRecords', 'view', array(4));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Edit', 'members', 'edit', array(4));
			$this->controller->Nav->expects($this->at(2))->method('add')->with('Change Password', 'members', 'changePassword', array(4));
			$this->controller->Nav->expects($this->at(3))->method('add')->with('Revoke Membership', 'members', 'revokeMembership', array(4));

			// Should not redirect, and should populate 
			$this->testAction('members/view/4');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
			$this->_testMailingListView(array('us8gz1v8rq' => true, '455de2ac56' => false));

			$expectedMemberInfo = array(
				'id' => '4',
				'firstname' => 'Kelly',
				'surname' => 'Savala',
				'username' => 'huskycolossus',
				'email' => 'k.savala@yahoo.co.uk',
				'groups' => array(
					0 => array(
						'id' => '2',
						'description' => 'Current Members',
					),
					1 => array(
						'id' => '4',
						'description' => 'Gatekeeper Admin',
					),
				),
				'status' => array(
					'id' => '5',
					'name' => 'Current Member',
				),
				'joinDate' => '2010-09-22',
				'unlockText' => 'Hey Kelly',
				'balance' => '-5649',
				'creditLimit' => '5000',
				'pin' => array(
					0 => array( 
						'id' => 4,
						'pin' => '5436',
						'state' => 30,
					)
				),
				'paymentRef' => 'HSNOTTSYT7H4CW3G',
				'address' => array(
					'part1' => '8 Elm Close',
					'part2' => 'Tetsworth',
					'city' => 'Thame',
					'postcode' => 'OX9 7AP',
				),
				'contactNumber' => '079 0644 8720',
				'lastStatusUpdate' => array(
					'id' => '4',
					'by' => '5',
					'from' => '4',
					'to' => '5',
					'at' => '2012-12-17 19:19:59',
					'by_username' => 'chollertonbanker',
				),
				'lastEmail' => array(
					'id' => '4',
					'member_id' => '4',
					'subject' => 'Test email 2',
					'timestamp' => '2013-06-05 13:51:04'
				)
			);

			$this->assertEqual( $this->vars['member'], $expectedMemberInfo, 'Member info was not correct.' );
		}

		public function testViewMemberAsMembershipTeam()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		),
            		'Nav' => array(
            			'add',
            		)
            	),
            	'methods' => array(
            		'getMailingList',
            	),
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(4));

			$this->_constructMailingList();

			$this->controller->Nav->expects($this->exactly(4))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('View Email History', 'emailRecords', 'view', array(3));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Edit', 'members', 'edit', array(3));
			$this->controller->Nav->expects($this->at(2))->method('add')->with('Change Password', 'members', 'changePassword', array(3));
			$this->controller->Nav->expects($this->at(3))->method('add')->with('Revoke Membership', 'members', 'revokeMembership', array(3));

			// Should not redirect, and should populate 
			$this->testAction('members/view/3');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
			$this->_testMailingListView(array('us8gz1v8rq' => true, '455de2ac56' => false));

			$expectedMemberInfo = array(
				'id' => '3',
				'firstname' => 'Guy',
				'surname' => 'Viles',
				'username' => 'buntweyr',
				'email' => 'g.viles@gmail.com',
				'groups' => array(
					0 => array(
						'id' => '2',
						'description' => 'Current Members',
					),
				),
				'status' => array(
					'id' => '5',
					'name' => 'Current Member',
				),
				'joinDate' => '2010-08-18',
				'unlockText' => 'Sup Guy',
				'balance' => '-985',
				'creditLimit' => '5000',
				'pin' => array(
					0 => array( 
						'id' => 3,
						'pin' => '5142',
						'state' => 30,
					)
				),
				'paymentRef' => 'HSNOTTSYT7H4CW3G',
				'lastStatusUpdate' => array(
					'id' => '3',
					'by' => '5',
					'from' => '4',
					'to' => '5',
					'at' => '2013-04-02 09:32:42',
					'by_username' => 'chollertonbanker',
				),
				'lastEmail' => array(
					'id' => '3',
					'member_id' => '3',
					'subject' => 'Test email 2',
					'timestamp' => '2013-03-23 05:42:21'
				)
			);

			$this->assertEqual( $this->vars['member'], $expectedMemberInfo, 'Member info was not correct.' );
		}

		public function testViewMemberAsFullAccess()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		),
            		'Nav' => array(
            			'add',
            		)
            	),
            	'methods' => array(
            		'getMailingList',
            	),
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(1));

			$this->_constructMailingList();

			$this->controller->Nav->expects($this->exactly(4))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('View Email History', 'emailRecords', 'view', array(4));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Edit', 'members', 'edit', array(4));
			$this->controller->Nav->expects($this->at(2))->method('add')->with('Change Password', 'members', 'changePassword', array(4));
			$this->controller->Nav->expects($this->at(3))->method('add')->with('Revoke Membership', 'members', 'revokeMembership', array(4));

			// Should not redirect, and should populate 
			$this->testAction('members/view/4');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
			$this->_testMailingListView(array('us8gz1v8rq' => true, '455de2ac56' => false));

			$expectedMemberInfo = array(
				'id' => '4',
				'firstname' => 'Kelly',
				'surname' => 'Savala',
				'username' => 'huskycolossus',
				'email' => 'k.savala@yahoo.co.uk',
				'groups' => array(
					0 => array(
						'id' => '2',
						'description' => 'Current Members',
					),
					1 => array(
						'id' => '4',
						'description' => 'Gatekeeper Admin',
					),
				),
				'status' => array(
					'id' => '5',
					'name' => 'Current Member',
				),
				'joinDate' => '2010-09-22',
				'unlockText' => 'Hey Kelly',
				'balance' => '-5649',
				'creditLimit' => '5000',
				'pin' => array(
					0 => array( 
						'id' => 4,
						'pin' => '5436',
						'state' => 30,
					)
				),
				'paymentRef' => 'HSNOTTSYT7H4CW3G',
				'address' => array(
					'part1' => '8 Elm Close',
					'part2' => 'Tetsworth',
					'city' => 'Thame',
					'postcode' => 'OX9 7AP',
				),
				'contactNumber' => '079 0644 8720',
				'lastStatusUpdate' => array(
					'id' => '4',
					'by' => '5',
					'from' => '4',
					'to' => '5',
					'at' => '2012-12-17 19:19:59',
					'by_username' => 'chollertonbanker'
				),
				'lastEmail' => array(
					'id' => '4',
					'member_id' => '4',
					'subject' => 'Test email 2',
					'timestamp' => '2013-06-05 13:51:04'
				)
			);

			$this->assertEqual( $this->vars['member'], $expectedMemberInfo, 'Member info was not correct.' );
		}

		public function testViewMemberAsSameMember()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		),
            		'Nav' => array(
            			'add',
            		)
            	),
            	'methods' => array(
            		'getMailingList',
            	),
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(3));

			$this->_constructMailingList();

			$this->controller->Nav->expects($this->exactly(3))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('Edit', 'members', 'edit', array(3));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Change Password', 'members', 'changePassword', array(3));
			$this->controller->Nav->expects($this->at(2))->method('add')->with('Revoke Membership', 'members', 'revokeMembership', array(3));

			// Should not redirect, and should populate 
			$this->testAction('members/view/3');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
			$this->_testMailingListView(array('us8gz1v8rq' => true, '455de2ac56' => false));

			$expectedMemberInfo = array(
				'id' => '3',
				'firstname' => 'Guy',
				'surname' => 'Viles',
				'username' => 'buntweyr',
				'email' => 'g.viles@gmail.com',
				'joinDate' => '2010-08-18',
				'unlockText' => 'Sup Guy',
				'balance' => '-985',
				'creditLimit' => '5000',
				'paymentRef' => 'HSNOTTSYT7H4CW3G',
				'address' => array(
					'part1' => '4 Fraser Crescent',
					'part2' => '',
					'city' => 'Portree',
					'postcode' => 'IV51 9DR',
				),
				'contactNumber' => '077 7181 0959',
			);

			$this->assertEqual( $this->vars['member'], $expectedMemberInfo, 'Member info was not correct.' );
		}

		public function testViewMemberAsMemberAdminThatIsProspectiveMember()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		),
            		'Nav' => array(
            			'add',
            		)
            	),
            	'methods' => array(
            		'getMailingList',
            	),
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));

			$this->_constructMailingList();

			$this->controller->Nav->expects($this->exactly(3))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('Edit', 'members', 'edit', array(7));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Change Password', 'members', 'changePassword', array(7));
			$this->controller->Nav->expects($this->at(2))->method('add')->with('Send Membership Reminder', 'members', 'sendMembershipReminder', array(7));

			// Should not redirect, and should populate 
			$this->testAction('members/view/7');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
			$this->_testMailingListView(array('us8gz1v8rq' => true, '455de2ac56' => true));

			$expectedMemberInfo = array(
				'id' => '7',
				'email' => 'CherylLCarignan@teleworm.us',
				'status' => array(
					'id' => '1',
					'name' => 'Prospective Member',
				),
			);

			$this->assertEqual( $this->vars['member'], $expectedMemberInfo, 'Member info was not correct.' );
		}

		public function testViewMemberAsSameMemberThatIsPreMember1()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		),
            		'Nav' => array(
            			'add',
            		)
            	),
            	'methods' => array(
            		'getMailingList',
            	),
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(9));

			$this->_constructMailingList();

			$this->controller->Nav->expects($this->exactly(3))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('Edit', 'members', 'edit', array(9));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Change Password', 'members', 'changePassword', array(9));
			$this->controller->Nav->expects($this->at(2))->method('add')->with('Send Contact Details Reminder', 'members', 'sendContactDetailsReminder', array(9));

			// Should not redirect, and should populate 
			$this->testAction('members/view/9');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
			$this->_testMailingListView(array('us8gz1v8rq' => true, '455de2ac56' => true));

			$expectedMemberInfo = array(
				'id' => '9',
				'firstname' => 'Dorothy',
				'surname' => 'Russell',
				'username' => 'Warang29',
				'email' => 'DorothyDRussell@dayrep.com',
			);

			$this->assertEqual( $this->vars['member'], $expectedMemberInfo, 'Member info was not correct.' );
		}

		public function testViewMemberAsSameMemberThatIsPreMember2()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		),
            		'Nav' => array(
            			'add',
            		)
            	),
            	'methods' => array(
            		'getMailingList',
            	),
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(11));

			$this->_constructMailingList();

			$this->controller->Nav->expects($this->exactly(4))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('Edit', 'members', 'edit', array(11));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Change Password', 'members', 'changePassword', array(11));
			$this->controller->Nav->expects($this->at(2))->method('add')->with('Accept Details', 'members', 'acceptDetails', array(11));
			$this->controller->Nav->expects($this->at(3))->method('add')->with('Reject Details', 'members', 'rejectDetails', array(11));

			// Should not redirect, and should populate 
			$this->testAction('members/view/11');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
			$this->_testMailingListView(array('us8gz1v8rq' => false, '455de2ac56' => true));

			$expectedMemberInfo = array(
				'id' => '11',
				'firstname' => 'Betty',
				'surname' => 'Paris',
				'username' => 'Beltonstlend51',
				'email' => 'BettyCParis@teleworm.us',
				'address' => array(
					'part1' => '10 Hampton Court Rd',
					'part2' => '',
					'city' => 'Spelsbury',
					'postcode' => 'OX7 2US',
				),
				'contactNumber' => '079 0572 8737',
			);

			$this->assertEqual( $this->vars['member'], $expectedMemberInfo, 'Member info was not correct.' );
		}

		public function testViewMemberAsSameMemberThatIsPreMember3()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		),
            		'Nav' => array(
            			'add',
            		)
            	),
            	'methods' => array(
            		'getMailingList',
            	),
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(4));

			$this->_constructMailingList();

			$this->controller->Nav->expects($this->exactly(4))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('View Email History', 'emailRecords', 'view', array(4));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Edit', 'members', 'edit', array(4));
			$this->controller->Nav->expects($this->at(2))->method('add')->with('Change Password', 'members', 'changePassword', array(4));
			$this->controller->Nav->expects($this->at(3))->method('add')->with('Revoke Membership', 'members', 'revokeMembership', array(4));

			// Should not redirect, and should populate 
			$this->testAction('members/view/4');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
			$this->_testMailingListView(array('us8gz1v8rq' => true, '455de2ac56' => false));

			$expectedMemberInfo = array(
				'id' => '4',
				'firstname' => 'Kelly',
				'surname' => 'Savala',
				'username' => 'huskycolossus',
				'email' => 'k.savala@yahoo.co.uk',
				'joinDate' => '2010-09-22',
				'unlockText' => 'Hey Kelly',
				'balance' => '-5649',
				'creditLimit' => '5000',
				'paymentRef' => 'HSNOTTSYT7H4CW3G',
				'address' => array(
					'part1' => '8 Elm Close',
					'part2' => 'Tetsworth',
					'city' => 'Thame',
					'postcode' => 'OX9 7AP',
				),
				'contactNumber' => '079 0644 8720',
				'groups' => array(
					0 => array(
				        'id' => '2',
				        'description' => 'Current Members',
				    ),
				    1 => array(
				        'id' => '4',
				        'description' => 'Gatekeeper Admin',
				    ),
				),
				'status' => array(
					'id' => '5',
				    'name' => 'Current Member',
				),
				'pin' => array(
					0 => array( 
						'id' => 4,
						'pin' => '5436',
						'state' => 30,
					)
				),
				'lastStatusUpdate' => array(
					'id' => '4',
					'by' => '5',
					'from' => '4',
					'to' => '5',
					'at' => '2012-12-17 19:19:59',
					'by_username' => 'chollertonbanker',
				),
				'lastEmail' => array(
					'id' => '4',
					'member_id' => '4',
					'subject' => 'Test email 2',
					'timestamp' => '2013-06-05 13:51:04'
				)
			);

			$this->assertEqual( $this->vars['member'], $expectedMemberInfo, 'Member info was not correct.' );
		}

		public function testEditMemberInvalidData()
		{
			// Should redirect
			$this->testAction('members/edit/0');
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
		}

		public function testEditMemberGetOwn()
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		),
            	),
            	'methods' => array(
            		'getMailingList',
            	),
            ));

            $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(2));

            $this->_constructMailingList();

			$this->testAction('members/edit/2');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );

			$expectedMemberVal = array(
				'id' => '2',
				'firstname' => 'Annabelle',
				'surname' => 'Santini',
				'username' => 'pecanpaella',
				'email' => 'a.santini@hotmail.com',
				'joinDate' => '2011-02-24',
				'unlockText' => 'Welcome Annabelle',
				'balance' => '0',
				'creditLimit' => '5000',
				'address' => array(
					'part1' => '1 Saint Paul\'s Church Yard',
					'part2' => 'The City',
					'city' => 'London',
					'postcode' => 'EC4M 8SH',
				),
				'contactNumber' => '077 1755 4342',
			);
			$this->_testEditMemberViewVars($expectedMemberVal, array('us8gz1v8rq' => true, '455de2ac56' => false));
		}

		public function testEditMemberEditOwn()
		{
			$inputData = array(
            	'Member' => array(
					'firstname' => 'Nat',
					'surname' => 'Gillian',
					'username' => 'foo',
					'email' => 'a.santini@hotmail.com',
					'unlock_text' => 'Would you kindly?',
					'address_1' => '5 Henry Way',
					'address_2' => '',
					'address_city' => 'Bobbington',
					'address_postcode' => 'FU453JD',
					'contact_number' => '079716523804',
				),
				'MailingLists' => array(
					'MailingLists' => array(
						'455de2ac56'
					)
				),
			);

			$expectedViewVal = array(
				'id' => '2',
				'firstname' => 'Annabelle',
				'surname' => 'Santini',
				'username' => 'pecanpaella',
				'email' => 'a.santini@hotmail.com',
				'joinDate' => '2011-02-24',
				'unlockText' => 'Welcome Annabelle',
				'balance' => '0',
				'creditLimit' => '5000',
				'address' => array(
					'part1' => '1 Saint Paul\'s Church Yard',
					'part2' => 'The City',
					'city' => 'London',
					'postcode' => 'EC4M 8SH',
				),
				'contactNumber' => '077 1755 4342',
			);

			$expectedRecordData = array(
				'Member' => array(
				    'member_id' => '2',
            		'account_id' => '2',
            		'member_status' => '5',
            		'join_date' => '2011-02-24',
            		'balance' => '0',
            		'credit_limit' => '5000',
					'firstname' => 'Nat',
					'surname' => 'Gillian',
					'username' => 'foo',
					'email' => 'a.santini@hotmail.com',
					'unlock_text' => 'Would you kindly?',
					'address_1' => '5 Henry Way',
					'address_2' => '',
					'address_city' => 'Bobbington',
					'address_postcode' => 'FU453JD',
					'contact_number' => '079716523804',
				),
				'Status' => array(
				    'status_id' => '5',
				    'title' => 'Current Member',
				),
				'Account' => array(
				    'account_id' => '2',
				    'payment_ref' => 'HSNOTTSK2R62GQW6',
				),
				'Pin' => array(
					0 => array(
					    'pin_id' => '2',
					    'pin' => '7422',
					    'date_added' => '2012-12-03 23:56:43',
					    'expiry' => null,
					    'state' => '30',
					    'member_id' => '2',
				    ),
				),
				'StatusUpdate' => array(
				),
				'Group' => array(
				    0 => array(
				        'grp_id' => '2',
				        'grp_description' => 'Current Members',
				    ),
				    1 => array(
				        'grp_id' => '3',
				        'grp_description' => 'Snackspace Admin',
				    ),
				),
			);

			$this->_testEditMember(
				2, 
				2, 
				$inputData, 
				$expectedViewVal, 
				$expectedRecordData, 
				array(
					'us8gz1v8rq' => true, 
					'455de2ac56' => false
				),
				'Details updated.\nSuccessfully subscribed to Nottingham Hackspace The Other List\nSuccessfully unsubscribed from Nottingham Hackspace Announcements\n'
				);
		}

		public function testEditMemberEditOwnAllValues()
		{
			$inputData = array(
            	'Member' => array(
            		'member_id' => '243',
            		'firstname' => 'Nat',
            		'surname' => 'Gillian',
            		'account_id' => '325',
            		'member_status' => '1',
            		'join_date' => '2013-12-30',
            		'balance' => '100000',
            		'credit_limit' => '20000',
					'username' => 'foo',
					'email' => 'totallydifferent@hotmail.com',
					'unlock_text' => 'Would you kindly?',
					'address_1' => '5 Henry Way',
					'address_2' => '',
					'address_city' => 'Bobbington',
					'address_postcode' => 'FU453JD',
					'contact_number' => '079716523804',
				),
				'Status' => array(
				    'status_id' => '3',
				    'title' => 'Special Status',
				),
				'Account' => array(
				    'account_id' => '56',
				    'payment_ref' => 'INEEDNOPAYMENTREF',
				),
				'Pin' => array(
					0 => array(
					    'pin_id' => '4',
					    'pin' => '5555',
					    'unlock_text' => 'MAYBE USED',
					    'date_added' => '2010-01-01 00:00:00',
					    'expiry' => 'NEVER!',
					    'state' => '22',
					    'member_id' => '15',
				    ),
				),
				'StatusUpdate' => array(
					0 => array(
						'id' => '1',
						'member_id' => '4',
						'admin_id' => '5',
						'old_status' => '0',
						'new_status' => '3',
						'timestamp' => '2012-12-17 19:19:59',
					),
				),
				'Group' => array(
				    0 => array(
				        'grp_id' => '1',
				        'grp_description' => 'Full Access',
				    ),
				),
				'MailingLists' => array(
					'MailingLists' => array(
						'us8gz1v8rq'
					)
				),
			);

			$expectedViewVal = array(
				'id' => '2',
				'firstname' => 'Annabelle',
				'surname' => 'Santini',
				'username' => 'pecanpaella',
				'email' => 'a.santini@hotmail.com',
				'joinDate' => '2011-02-24',
				'unlockText' => 'Welcome Annabelle',
				'balance' => '0',
				'creditLimit' => '5000',
				'address' => array(
					'part1' => '1 Saint Paul\'s Church Yard',
					'part2' => 'The City',
					'city' => 'London',
					'postcode' => 'EC4M 8SH',
				),
				'contactNumber' => '077 1755 4342',
			);

			$expectedRecordData = array(
				'Member' => array(
				    'member_id' => '2',
            		'account_id' => '2',
            		'member_status' => '5',
            		'join_date' => '2011-02-24',
            		'balance' => '0',
            		'credit_limit' => '5000',
					'firstname' => 'Nat',
            		'surname' => 'Gillian',
					'username' => 'foo',
					'email' => 'totallydifferent@hotmail.com',
					'unlock_text' => 'Would you kindly?',
					'address_1' => '5 Henry Way',
					'address_2' => '',
					'address_city' => 'Bobbington',
					'address_postcode' => 'FU453JD',
					'contact_number' => '079716523804',
				),
				'Status' => array(
				    'status_id' => '5',
				    'title' => 'Current Member',
				),
				'Account' => array(
				    'account_id' => '2',
				    'payment_ref' => 'HSNOTTSK2R62GQW6',
				),
				'Pin' => array(
					0 => array( 
					    'pin_id' => '2',
					    'pin' => '7422',
					    'date_added' => '2012-12-03 23:56:43',
					    'expiry' => null,
					    'state' => '30',
					    'member_id' => '2',
					),
				),
				'StatusUpdate' => array(
				),
				'Group' => array(
				    0 => array(
				        'grp_id' => '2',
				        'grp_description' => 'Current Members',
				    ),
				    1 => array(
				        'grp_id' => '3',
				        'grp_description' => 'Snackspace Admin',
				    ),
				),
			);
			$this->_testEditMember(
				2, 
				2, 
				$inputData, 
				$expectedViewVal, 
				$expectedRecordData, 
				array(
					'us8gz1v8rq' => true, 
					'455de2ac56' => false
				),
				'Details updated.\nSuccessfully subscribed to Nottingham Hackspace Announcements\n'
			);
		}

		public function testEditMemberEditMemberAdmin()
		{
			$this->_testEditMemberSetAdminFields(5);
		}

		public function testEditMemberEditFullAccess()
		{
			$this->_testEditMemberSetAdminFields(1);
		}

		private function _testEditMemberSetAdminFields($adminId)
		{
			$inputData = array(
            	'Member' => array(
					'firstname' => 'Ser',
					'surname' => 'Dantus',
					'username' => 'loremipsum',
					'email' => 'k.savala@yahoo.co.uk',
					'unlock_text' => 'Open the damn door',
					'address_1' => '34fewarg',
					'address_2' => '',
					'address_city' => '5468452456',
					'address_postcode' => 'weqfwrgetshb',
					'contact_number' => '01321564895',
					'account_id' => '4',
					'member_status' => 1,
				),
				'Group' => array(
					'Group' => array(
						0 => 2,
						1 => 3,
						2 => 5,
					),
				),
				'MailingLists' => array(
					'MailingLists' => array(
						'us8gz1v8rq',
						'455de2ac56',
					)
				),
			);

			$expectedViewVal = array(
				'id' => '4',
				'firstname' => 'Kelly',
				'surname' => 'Savala',
				'username' => 'huskycolossus',
				'email' => 'k.savala@yahoo.co.uk',
				'joinDate' => '2010-09-22',
				'unlockText' => 'Hey Kelly',
				'balance' => '-5649',
				'creditLimit' => '5000',
				'paymentRef' => 'HSNOTTSYT7H4CW3G',
				'address' => array(
					'part1' => '8 Elm Close',
					'part2' => 'Tetsworth',
					'city' => 'Thame',
					'postcode' => 'OX9 7AP',
				),
				'contactNumber' => '079 0644 8720',
				'pin' => array(
					0 => array( 
						'id' => 4,
						'pin' => '5436',
						'state' => 30,
					)
				),
				'groups' => array(
					0 => array(
				        'id' => '2',
				        'description' => 'Current Members',
				    ),
				    1 => array(
				        'id' => '4',
				        'description' => 'Gatekeeper Admin',
				    ),
				),
				'status' => array(
					'id' => '5',
				    'name' => 'Current Member',
				),
				'lastStatusUpdate' => array(
					'id' => '4',
					'by' => '5',
					'from' => '4',
					'to' => '5',
					'at' => '2012-12-17 19:19:59',
					'by_username' => 'chollertonbanker',
				),
			);

			$expectedRecordData = array(
				'Member' => array(
				    'member_id' => '4',
            		'account_id' => '4',
            		'member_status' => '5',
            		'join_date' => '2010-09-22',
            		'balance' => '-5649',
            		'credit_limit' => '5000',
					'firstname' => 'Ser',
					'surname' => 'Dantus',
					'username' => 'loremipsum',
					'email' => 'k.savala@yahoo.co.uk',
					'unlock_text' => 'Open the damn door',
					'address_1' => '34fewarg',
					'address_2' => '',
					'address_city' => '5468452456',
					'address_postcode' => 'weqfwrgetshb',
					'contact_number' => '01321564895',
				),
				'Status' => array(
				    'status_id' => '5',
				    'title' => 'Current Member',
				),
				'Account' => array(
				    'account_id' => '4',
				    'payment_ref' => 'HSNOTTSCV3TFFDGX',
				),
				'Pin' => array(
					0 => array(
					    'pin_id' => '4',
					    'pin' => '5436',
					    'date_added' => '2012-12-18 21:01:05',
					    'expiry' => null,
					    'state' => '30',
					    'member_id' => '4',
					),
				),
				'StatusUpdate' => array(
					0 => array(
						'id' => '2',
						'member_id' => '4',
						'admin_id' => '5',
						'old_status' => '4',
						'new_status' => '5',
						'timestamp' => '2012-12-17 19:19:59',
					),
				),
				'Group' => array(
					0 => array(
						'grp_id' => '2',
						'grp_description' => 'Current Members'
					),
					1 => array(
						'grp_id' => '3',
						'grp_description' => 'Snackspace Admin',
					),
					2 => array(
						'grp_id' => '5',
						'grp_description' => 'Member Admin'
					),
				),
			);

			$this->_testEditMember(
				4, 
				$adminId, 
				$inputData, 
				$expectedViewVal, 
				$expectedRecordData, 
				array(
					'us8gz1v8rq' => true, 
					'455de2ac56' => false
				),
				'Details updated.\nSuccessfully subscribed to Nottingham Hackspace The Other List\n'
			);
		}

		public function testEditMemberEditRemoveGroupsMemberAdmin()
		{
			$this->_testEditMemberRemoveGroups(5);
		}

		public function testEditMemberEditRemoveGroupsFullAccess()
		{
			$this->_testEditMemberRemoveGroups(1);
		}

		private function _testEditMemberRemoveGroups($adminId)
		{
			$inputData = array(
            	'Member' => array(
					'firstname' => 'Ser',
					'surname' => 'Dantus',
					'username' => 'loremipsum',
					'email' => 'a.santini@hotmail.com',
					'unlock_text' => 'Open the damn door',
					'address_1' => '34fewarg',
					'address_2' => '',
					'address_city' => '5468452456',
					'address_postcode' => 'weqfwrgetshb',
					'contact_number' => '01321564895',
					'account_id' => '4',
					'member_status' => 1,
				),
				'Group' => array(
					'Group' => array(
						0 => 2,
					),
				),
				'MailingLists' => array(
					'MailingLists' => array(
						'455de2ac56',
					)
				),
			);

			$expectedViewVal = array(
				'id' => '2',
				'firstname' => 'Annabelle',
				'surname' => 'Santini',
				'username' => 'pecanpaella',
				'email' => 'a.santini@hotmail.com',
				'joinDate' => '2011-02-24',
				'unlockText' => 'Welcome Annabelle',
				'balance' => '0',
				'creditLimit' => '5000',
				'paymentRef' => 'HSNOTTSK2R62GQW6',
				'address' => array(
					'part1' => '1 Saint Paul\'s Church Yard',
					'part2' => 'The City',
					'city' => 'London',
					'postcode' => 'EC4M 8SH',
				),
				'contactNumber' => '077 1755 4342',
				'pin' => array(
					0 => array( 
						'id' => 2,
						'pin' => '7422',
						'state' => '30',
					)
				),
				'groups' => array(
					0 => array(
				        'id' => '2',
				        'description' => 'Current Members',
				    ),
				    1 => array(
				        'id' => '3',
				        'description' => 'Snackspace Admin',
				    ),
				),
				'status' => array(
					'id' => '5',
				    'name' => 'Current Member',
				),
			);

			$expectedRecordData = array(
				'Member' => array(
				    'member_id' => '2',
            		'account_id' => '4',
            		'member_status' => '5',
            		'join_date' => '2011-02-24',
            		'balance' => '0',
            		'credit_limit' => '5000',
					'firstname' => 'Ser',
					'surname' => 'Dantus',
					'username' => 'loremipsum',
					'email' => 'a.santini@hotmail.com',
					'unlock_text' => 'Open the damn door',
					'address_1' => '34fewarg',
					'address_2' => '',
					'address_city' => '5468452456',
					'address_postcode' => 'weqfwrgetshb',
					'contact_number' => '01321564895',
				),
				'Status' => array(
				    'status_id' => '5',
				    'title' => 'Current Member',
				),
				'Account' => array(
				    'account_id' => '4',
				    'payment_ref' => 'HSNOTTSCV3TFFDGX',
				),
				'Pin' => array(
					0 => array( 
					    'pin_id' => '2',
					    'pin' => '7422',
					    'date_added' => '2012-12-03 23:56:43',
					    'expiry' => null,
					    'state' => '30',
					    'member_id' => '2',
					),
				),
				'StatusUpdate' => array(
				),
				'Group' => array(
					0 => array(
						'grp_id' => '2',
						'grp_description' => 'Current Members'
					),
				),
			);

			$this->_testEditMember(
				2, 
				$adminId, 
				$inputData, 
				$expectedViewVal, 
				$expectedRecordData, 
				array(
					'us8gz1v8rq' => true, 
					'455de2ac56' => false
				),
				'Details updated.\nSuccessfully subscribed to Nottingham Hackspace The Other List\nSuccessfully unsubscribed from Nottingham Hackspace Announcements\n'
			);
		}

		public function testEditMemberEditRemoveAllGroupsMemberAdmin()
		{
			$this->_testEditMemberRemoveAllGroups(5);
		}

		public function testEditMemberEditRemoveAllGroupsFullAccess()
		{
			$this->_testEditMemberRemoveAllGroups(1);
		}

		private function _testEditMemberRemoveAllGroups($adminId)
		{
			$inputData = array(
            	'Member' => array(
					'firstname' => 'Ser',
					'surname' => 'Dantus',
					'username' => 'loremipsum',
					'email' => 'not_the_same@gmail.com',
					'unlock_text' => 'Open the damn door',
					'address_1' => '34fewarg',
					'address_2' => '', 
					'address_city' => '5468452456',
					'address_postcode' => 'weqfwrgetshb',
					'contact_number' => '01321564895',
					'account_id' => '4',
					'member_status' => 1,
				),
				'Group' => array(
					'Group' => array(
					),
				),
				'MailingLists' => array(
					'MailingLists' => array(
						'us8gz1v8rq',
					)
				),
			);

			$expectedViewVal = array(
				'id' => '5',
				'firstname' => 'Jessie',
				'surname' => 'Easterwood',
				'username' => 'chollertonbanker',
				'email' => 'j.easterwood@googlemail.com',
				'joinDate' => '2010-09-22',
				'unlockText' => 'Oh dear...',
				'balance' => '-3465',
				'creditLimit' => '5000',
				'paymentRef' => 'HSNOTTSYT7H4CW3G',
				'address' => array(
					'part1' => '9 Langton Avenue',
					'part2' => 'East Calder',
					'city' => 'Livingston',
					'postcode' => 'EH53 0DR',
				),
				'contactNumber' => '070 0036 0548',
				'pin' => array(
					0 => array( 
						'id' => 5,
						'pin' => '3014',
						'state' => '30',
					)
				),
				'groups' => array(
					0 => array(
				        'id' => '2',
				        'description' => 'Current Members',
				    ),
				    1 => array(
				        'id' => '5',
				        'description' => 'Member Admin',
				    ),
				),
				'status' => array(
					'id' => '5',
				    'name' => 'Current Member',
				),
			);

			$expectedRecordData = array(
				'Member' => array(
				    'member_id' => '5',
            		'account_id' => '4',
            		'member_status' => '5',
            		'join_date' => '2010-09-22',
            		'balance' => '-3465',
            		'credit_limit' => '5000',
					'firstname' => 'Ser',
					'surname' => 'Dantus',
					'username' => 'loremipsum',
					'email' => 'not_the_same@gmail.com',
					'unlock_text' => 'Open the damn door',
					'address_1' => '34fewarg',
					'address_2' => '',
					'address_city' => '5468452456',
					'address_postcode' => 'weqfwrgetshb',
					'contact_number' => '01321564895',
				),
				'Status' => array(
				    'status_id' => '5',
				    'title' => 'Current Member',
				),
				'Account' => array(
				    'account_id' => '4',
				    'payment_ref' => 'HSNOTTSCV3TFFDGX',
				),
				'Pin' => array(
					0 => array(
					    'pin_id' => '5',
					    'pin' => '3014',
					    'date_added' => '2012-12-19 19:54:12',
					    'expiry' => null,
					    'state' => '30',
					    'member_id' => '5',
					),
				),
				'StatusUpdate' => array(
				),
				'Group' => array(
					0 => array(
						'grp_id' => '2',
						'grp_description' => 'Current Members'
					),
				),
			);

			$this->_testEditMember(
				5, 
				$adminId, 
				$inputData, 
				$expectedViewVal, 
				$expectedRecordData, 
				array(
					'us8gz1v8rq' => true, 
					'455de2ac56' => false
				),
				'Details updated.\nSuccessfully subscribed to Nottingham Hackspace Announcements\n'
				);
		}

		public function testEditMemberEditJoinAccountMemberAdmin()
		{
			$this->_testEditMemberJoinAccount(5);
		}

		public function testEditMemberEditJoinAccountFullAccess()
		{
			$this->_testEditMemberJoinAccount(1);
		}

		private function _testEditMemberJoinAccount($adminId)
		{
			$inputData = array(
            	'Member' => array(
					'firstname' => 'Ser',
					'surname' => 'Dantus',
					'username' => 'loremipsum',
					'email' => 'not_the_same@gmail.com',
					'unlock_text' => 'Open the damn door',
					'address_1' => '34fewarg',
					'address_2' => '', 
					'address_city' => '5468452456',
					'address_postcode' => 'weqfwrgetshb',
					'contact_number' => '01321564895',
					'account_id' => '3',
					'member_status' => 1,
				),
				'Group' => array(
					'Group' => array(
					),
				),
				'MailingLists' => array(
					'MailingLists' => array(
						'455de2ac56',
					)
				),
			);

			$expectedViewVal = array(
				'id' => '3',
				'firstname' => 'Guy',
				'surname' => 'Viles',
				'username' => 'buntweyr',
				'email' => 'g.viles@gmail.com',
				'joinDate' => '2010-08-18',
				'unlockText' => 'Sup Guy',
				'balance' => '-985',
				'creditLimit' => '5000',
				'paymentRef' => 'HSNOTTSYT7H4CW3G',
				'address' => array(
					'part1' => '4 Fraser Crescent',
					'part2' => '',
					'city' => 'Portree',
					'postcode' => 'IV51 9DR',
				),
				'contactNumber' => '077 7181 0959',
				'pin' => array(
					0 => array( 
						'id' => 3,
						'pin' => '5142',
						'state' => '30',
					)
				),
				'groups' => array(
					0 => array(
				        'id' => '2',
				        'description' => 'Current Members',
				    ),
				),
				'status' => array(
					'id' => '5',
				    'name' => 'Current Member',
				),
				'lastStatusUpdate' => array(
					'id' => '3',
					'by' => '5',
					'from' => '4',
					'to' => '5',
					'at' => '2013-04-02 09:32:42',
					'by_username' => 'chollertonbanker',
				),
			);

			$expectedRecordData = array(
				'Member' => array(
				    'member_id' => '3',
            		'account_id' => '3',
            		'member_status' => '5',
            		'join_date' => '2010-08-18',
            		'balance' => '-985',
            		'credit_limit' => '5000',
					'firstname' => 'Ser',
					'surname' => 'Dantus',
					'username' => 'loremipsum',
					'email' => 'not_the_same@gmail.com',
					'unlock_text' => 'Open the damn door',
					'address_1' => '34fewarg',
					'address_2' => '',
					'address_city' => '5468452456',
					'address_postcode' => 'weqfwrgetshb',
					'contact_number' => '01321564895',
				),
				'Status' => array(
				    'status_id' => '5',
				    'title' => 'Current Member',
				),
				'Account' => array(
				    'account_id' => '3',
				    'payment_ref' => 'HSNOTTSYT7H4CW3G',
				),
				'Pin' => array(
					0 => array( 
					    'pin_id' => '3',
					    'pin' => '5142',
					    'date_added' => '2012-12-18 20:15:00',
					    'expiry' => null,
					    'state' => '30',
					    'member_id' => '3',
					),
				),
				'StatusUpdate' => array(
					0 => array(
						'id' => '3',
						'member_id' => '3',
						'admin_id' => '5',
						'old_status' => '4',
						'new_status' => '5',
						'timestamp' => '2013-04-02 09:32:42',
					),
				),
				'Group' => array(
					0 => array(
						'grp_id' => '2',
						'grp_description' => 'Current Members'
					),
				),
			);

			$this->_testEditMember(
				3, 
				$adminId, 
				$inputData, 
				$expectedViewVal, 
				$expectedRecordData, 
				array(
					'us8gz1v8rq' => true, 
					'455de2ac56' => false
				),
				'Details updated.\nSuccessfully subscribed to Nottingham Hackspace The Other List\n'
			);
		}

		public function testEditMemberEditLeaveJointAccountMemberAdmin()
		{
			$this->_testEditMemberLeaveJointAccount(5);
		}

		public function testEditMemberEditLeaveJointAccountFullAccess()
		{
			$this->_testEditMemberLeaveJointAccount(1);
		}

		private function _testEditMemberLeaveJointAccount($adminId)
		{
			$inputData = array(
            	'Member' => array(
					'firstname' => 'Ser',
					'surname' => 'Dantus',
					'username' => 'loremipsum',
					'email' => 'not_the_same@gmail.com',
					'unlock_text' => 'Open the damn door',
					'address_1' => '34fewarg',
					'address_2' => '', 
					'address_city' => '5468452456',
					'address_postcode' => 'weqfwrgetshb',
					'contact_number' => '01321564895',
					'account_id' => '-1',
					'member_status' => 1,
				),
				'Group' => array(
					'Group' => array(
					),
				),
				'MailingLists' => array(
					'MailingLists' => array(
						'us8gz1v8rq',
					)
				),
			);

			$expectedViewVal = array(
				'id' => '3',
				'firstname' => 'Guy',
				'surname' => 'Viles',
				'username' => 'buntweyr',
				'email' => 'g.viles@gmail.com',
				'joinDate' => '2010-08-18',
				'unlockText' => 'Sup Guy',
				'balance' => '-985',
				'creditLimit' => '5000',
				'paymentRef' => 'HSNOTTSYT7H4CW3G',
				'address' => array(
					'part1' => '4 Fraser Crescent',
					'part2' => '',
					'city' => 'Portree',
					'postcode' => 'IV51 9DR',
				),
				'contactNumber' => '077 7181 0959',
				'pin' => array(
					0 => array( 
						'id' => 3,
						'pin' => '5142',
						'state' => '30',
					)
				),
				'groups' => array(
					0 => array(
				        'id' => '2',
				        'description' => 'Current Members',
				    ),
				),
				'status' => array(
					'id' => '5',
				    'name' => 'Current Member',
				),
				'lastStatusUpdate' => array(
					'id' => '3',
					'by' => '5',
					'from' => '4',
					'to' => '5',
					'at' => '2013-04-02 09:32:42',
					'by_username' => 'chollertonbanker',
				),
			);

			$expectedRecordData = array(
				'Member' => array(
				    'member_id' => '3',
            		'account_id' => '9',
            		'member_status' => '5',
            		'join_date' => '2010-08-18',
            		'balance' => '-985',
            		'credit_limit' => '5000',
					'firstname' => 'Ser',
					'surname' => 'Dantus',
					'username' => 'loremipsum',
					'email' => 'not_the_same@gmail.com',
					'unlock_text' => 'Open the damn door',
					'address_1' => '34fewarg',
					'address_2' => '',
					'address_city' => '5468452456',
					'address_postcode' => 'weqfwrgetshb',
					'contact_number' => '01321564895',
				),
				'Status' => array(
				    'status_id' => '5',
				    'title' => 'Current Member',
				),
				'Account' => array(
				    'account_id' => '9',
				),
				'Pin' => array(
					0 => array(
					    'pin_id' => '3',
					    'pin' => '5142',
					    'date_added' => '2012-12-18 20:15:00',
					    'expiry' => null,
					    'state' => '30',
					    'member_id' => '3',
					),
				),
				'StatusUpdate' => array(
					0 => array(
						'id' => '3',
						'member_id' => '3',
						'admin_id' => '5',
						'old_status' => '4',
						'new_status' => '5',
						'timestamp' => '2013-04-02 09:32:42',
					),
				),
				'Group' => array(
					0 => array(
						'grp_id' => '2',
						'grp_description' => 'Current Members'
					),
				),
			);

			$controllerMock = $this->generate('Members', array(
	        	'components' => array(
	        		'Auth' => array(
	        			'user',
	        		),
	        		'Session' => array(
	        			'setFlash',
	        		),
	        	),
	        	'methods' => array(
            		'getMailingList',
            	),
	        ));

			$this->_testEditMember(
				3, 
				$adminId, 
				$inputData, 
				$expectedViewVal, 
				$expectedRecordData, 
				array(
					'us8gz1v8rq' => true, 
					'455de2ac56' => false
				), 
				'Details updated.\nSuccessfully subscribed to Nottingham Hackspace Announcements\n',
				$controllerMock,
				function ($recordData) {
					unset($recordData['Account']['payment_ref']);
					return $recordData;
				});
		}

		public function testEditMemberEditSetEverythingMemberAdmin()
		{
			$this->_testEditMemberSetEverything(5);
		}

		public function testEditMemberEditSetEverythingFullAccess()
		{
			$this->_testEditMemberSetEverything(1);
		}

		private function _testEditMemberSetEverything($adminId)
		{
			$inputData = array(
            	'Member' => array(
				    'member_id' => '6',
            		'account_id' => '1',
            		'member_status' => '8',
            		'join_date' => '2012-10-11',
            		'balance' => '-6575',
            		'credit_limit' => '8976',
					'firstname' => 'Ser',
					'surname' => 'Dantus',
					'username' => 'loremipsum',
					'email' => 'not_the_same@gmail.com',
					'unlock_text' => 'Open the damn door',
					'address_1' => '34fewarg',
					'address_2' => 'efwshtydrt',
					'address_city' => '5468452456',
					'address_postcode' => 'weqfwrgetshb',
					'contact_number' => '01321564895',
				),
				'Status' => array(
				    'status_id' => '1',
				    'title' => 'Full Access',
				),
				'Account' => array(
				    'account_id' => '1',
				    'payment_ref' => 'HSNOTTSYT7H4CW3G',
				),
				'Pin' => array(
					0 => array(
					    'pin_id' => '4',
					    'pin' => '9995',
					    'unlock_text' => 'Might be used',
					    'date_added' => '2011-04-06 04:23:59',
					    'expiry' => '2011-04-06',
					    'state' => '20',
					    'member_id' => '1',
					),
				),
				'StatusUpdate' => array(
					0 => array(
						'id' => '2',
						'member_id' => '4',
						'admin_id' => '5',
						'old_status' => '4',
						'new_status' => '5',
						'timestamp' => '2012-12-17 19:19:59',
					),
				),
				'Group' => array(
					'Group' => array(
						0 => 1,
						1 => 2,
						2 => 3,
						3 => 4,
						4 => 5,
					)
				),
				'MailingLists' => array(
					'MailingLists' => array(
						'us8gz1v8rq',
						'455de2ac56',
					)
				),

			);

			$expectedViewVal = array(
				'id' => '3',
				'firstname' => 'Guy',
				'surname' => 'Viles',
				'username' => 'buntweyr',
				'email' => 'g.viles@gmail.com',
				'joinDate' => '2010-08-18',
				'unlockText' => 'Sup Guy',
				'balance' => '-985',
				'creditLimit' => '5000',
				'paymentRef' => 'HSNOTTSYT7H4CW3G',
				'address' => array(
					'part1' => '4 Fraser Crescent',
					'part2' => '',
					'city' => 'Portree',
					'postcode' => 'IV51 9DR',
				),
				'contactNumber' => '077 7181 0959',
				'pin' => array(
					0 => array( 
						'id' => 3,
						'pin' => '5142',
						'state' => '30',
					)
				),
				'groups' => array(
					0 => array(
				        'id' => '2',
				        'description' => 'Current Members',
				    ),
				),
				'status' => array(
					'id' => '5',
				    'name' => 'Current Member',
				),
				'lastStatusUpdate' => array(
					'id' => '3',
					'by' => '5',
					'from' => '4',
					'to' => '5',
					'at' => '2013-04-02 09:32:42',
					'by_username' => 'chollertonbanker',
				),
			);

			$expectedRecordData = array(
				'Member' => array(
				    'member_id' => '3',
            		'account_id' => '1',
            		'member_status' => '5',
            		'join_date' => '2010-08-18',
            		'balance' => '-985',
            		'credit_limit' => '5000',
					'firstname' => 'Ser',
					'surname' => 'Dantus',
					'username' => 'loremipsum',
					'email' => 'not_the_same@gmail.com',
					'unlock_text' => 'Open the damn door',
					'address_1' => '34fewarg',
					'address_2' => 'efwshtydrt',
					'address_city' => '5468452456',
					'address_postcode' => 'weqfwrgetshb',
					'contact_number' => '01321564895',
				),
				'Status' => array(
				    'status_id' => '5',
				    'title' => 'Current Member',
				),
				'Account' => array(
				    'account_id' => '1',
				    'payment_ref' => 'HSNOTTS6762KC8JD',
				),
				'Pin' => array(
					0 => array(
					    'pin_id' => '3',
					    'pin' => '5142',
					    'date_added' => '2012-12-18 20:15:00',
					    'expiry' => null,
					    'state' => '30',
					    'member_id' => '3',
					),
				),
				'StatusUpdate' => array(
					0 => array(
						'id' => '3',
						'member_id' => '3',
						'admin_id' => '5',
						'old_status' => '4',
						'new_status' => '5',
						'timestamp' => '2013-04-02 09:32:42',
					),
				),
				'Group' => array(
					0 => array(
						'grp_id' => '1',
						'grp_description' => 'Full Access'
					),
					1 => array(
						'grp_id' => '2',
						'grp_description' => 'Current Members'
					),
					2 => array(
						'grp_id' => '3',
						'grp_description' => 'Snackspace Admin'
					),
					3 => array(
						'grp_id' => '4',
						'grp_description' => 'Gatekeeper Admin'
					),
					4 => array(
						'grp_id' => '5',
						'grp_description' => 'Member Admin'
					),
				),
			);

			$this->_testEditMember(
				3, 
				$adminId, 
				$inputData, 
				$expectedViewVal, 
				$expectedRecordData, 
				array(
					'us8gz1v8rq' => true, 
					'455de2ac56' => false
				)
				,'Details updated.\nSuccessfully subscribed to Nottingham Hackspace The Other List\nSuccessfully subscribed to Nottingham Hackspace Announcements\n'
			);
		}

		private function _testEditMember($memberId, $adminId, $inputData, $expectedViewVal, $expectedRecordData, $expectedMailingLists, $expectedFlash, $controllerMock = null, $recordCallback = null)
		{
			if($controllerMock == null)
			{
				$this->controller = $this->generate('Members', array(
		        	'components' => array(
		        		'Auth' => array(
		        			'user',
		        		),
		        		'Session' => array(
		        			'setFlash',
		        		),
		        	),
		        	'methods' => array(
	            		'getMailingList',
	            	),
		        ));
			}
			else
			{
				$this->controller = $controllerMock;
			}

            $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue($adminId));

            $this->_constructMailingList();

           	$this->controller->Session->expects($this->once())->method('setFlash')->with($expectedFlash);

			$this->testAction('members/edit/' . $memberId, array('data' => $inputData, 'method' => 'post'));
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
			$this->assertContains('/members/view/' . $memberId, $this->headers['Location']);

			$this->_testEditMemberViewVars($expectedViewVal, $expectedMailingLists);

			$record = $this->controller->Member->find('first', array('conditions' => array('Member.member_id' => $memberId)));

			if($recordCallback != null)
			{
				$record = $recordCallback($record);
			}

			$this->assertEqual( $record, $expectedRecordData, 'Member record was not updated correctly.' );
		}

		public function _testEditMemberViewVars($expectedMemberVal, $expectedMailingLists)
		{
			$this->assertIdentical( count($this->vars), 4, 'Unexpected number of view values.' );
			$this->assertArrayHasKey( 'member', $this->vars, 'No view value called \'members\'.' );
			$this->assertArrayHasKey( 'accounts', $this->vars, 'No view value called \'accounts\'.' );
			$this->assertArrayHasKey( 'groups', $this->vars, 'No view value called \'groups\'.' );
			$this->assertArrayHasKey( 'mailingLists', $this->vars, 'No view value called \'mailingLists\'.' );

			$this->assertEqual( $this->vars['member'], $expectedMemberVal, 'Member array is incorrect.' );

			$expectedAccountsVal = array(
				'-1' => 'Create new', 
				'1' => 'Mathew Pryce', 
				'2' => 'Annabelle Santini', 
				'3' => 'Jessie Easterwood, Kelly Savala and Guy Viles', 
				'6' => 'Guy Garrette', 
				'7' => 'Ryan Miles', 
				'8' => 'Evan Atkinson' 
			);

			$this->assertEqual( $this->vars['accounts'], $expectedAccountsVal, 'Accounts array is incorrect.' );

			$expectedGroupsVal = array(
				1 => 'Full Access',
				2 => 'Current Members',
				3 => 'Snackspace Admin',
				4 => 'Gatekeeper Admin',
				5 => 'Member Admin',
			);

			$this->assertEqual( $this->vars['groups'], $expectedGroupsVal, 'Groups array is incorrect.' );

			$this->_testMailingListView($expectedMailingLists);

		}

		private function _testEmailMembersWithStatusVewVars()
		{
			$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
			$this->assertArrayHasKey( 'members', $this->vars, 'No view value called \'members\'.' );
			$this->assertArrayHasKey( 'status', $this->vars, 'No view value called \'status\'.' );
		}

		public function testRevokeMembershipAsNonAdmin()
		{
			$this->_testRevokeMembership(2, 5, 'You are not authorized to do that.', false);
		}

		public function testRevokeMembershipInvalidMember()
		{
			$this->_testRevokeMembership(5, 6, 'Only current members can have their membership revoked.', false);
		}

		public function testRevokeMembershipAsMemberAdmin()
		{
			$this->_testRevokeMembership(5, 2, 'Membership revoked.', true);
		}

		public function testRevokeMembershipAsFullAccess()
		{
			$this->_testRevokeMembership(1, 2, 'Membership revoked.', true);
		}

		private function _testRevokeMembership($adminId, $memberId, $expectedFlash, $expectSuccess)
		{
			$this->_testChangeMembershipStatus($adminId, $memberId, $expectedFlash, $expectSuccess, 'revokeMembership', Status::EX_MEMBER, Status::CURRENT_MEMBER);
		}

		public function testReinstateMembershipAsNonAdmin()
		{
			$this->_testReinstateMembership(2, 6, 'You are not authorized to do that.', false);
		}

		public function testReinstateMembershipInvalidMember()
		{
			$this->_testReinstateMembership(5, 3, 'Only ex members can have their membership reinstated.', false);
		}

		public function testReinstateMembershipAsMemberAdmin()
		{
			$this->_testReinstateMembership(5, 6, 'Membership reinstated.', true);
		}

		public function testReinstateMembershipAsFullAccess()
		{
			$this->_testReinstateMembership(1, 6, 'Membership reinstated.', true);
		}

		private function _testReinstateMembership($adminId, $memberId, $expectedFlash, $expectSuccess)
		{
			$this->_testChangeMembershipStatus($adminId, $memberId, $expectedFlash, $expectSuccess, 'reinstateMembership', Status::CURRENT_MEMBER, Status::EX_MEMBER);
		}

		private function _testChangeMembershipStatus($adminId, $memberId, $expectedFlash, $expectSuccess, $function, $newStatus, $oldStatus)
		{
			$this->controller = $this->generate('Members', array(
				'components' => array(
					'Auth' => array(
						'user',
					),
					'Session' => array(
						'setFlash',
					),
				),
			));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue($adminId));
			$this->controller->Session->expects($this->once())->method('setFlash')->with($expectedFlash);

			$this->testAction('members/' . $function . '/' . $memberId);

			if($expectSuccess)
			{
				// Get the record and test it
				$record = $this->controller->Member->findByMemberId($memberId);

				$this->assertInternalType('array', $record, 'Record was not found.');

				$this->assertEqual($record['Member']['member_status'], $newStatus, 'Status was not set correctly.');

				$this->assertEqual($record['StatusUpdate'][0]['admin_id'], $adminId, 'StatusUpdate has incorrect adminId');
				$this->assertEqual($record['StatusUpdate'][0]['old_status'], $oldStatus, 'StatusUpdate has incorrect oldStatus');
				$this->assertEqual($record['StatusUpdate'][0]['new_status'], $newStatus, 'StatusUpdate has incorrect oldStatus');
			}
		}

		public function testUploadCsvInvalidFile()
		{
			$contents = 'iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAACXBIWXMAAAsTAAALEwEAmpwYAAAKT2lDQ1BQaG90b3Nob3AgSUNDIHByb2ZpbGUAAHjanVNnVFPpFj333vRCS4iAlEtvUhUIIFJCi4AUkSYqIQkQSoghodkVUcERRUUEG8igiAOOjoCM';
			$guid = null;

			$this->_setupTestUploadCsv();

			$this->controller->Session->expects($this->once())->method('setFlash')->with('That did not seem to be a valid bank .csv file');
			$this->controller->Nav->expects($this->never())->method('add');

			$this->_runTestUploadCsv($contents, $guid);

			$this->assertArrayHasKey('Location', $this->headers);
			$this->assertContains('/members/uploadCsv', $this->headers['Location']);
		}

		public function testUploadCsvDudFile()
		{
			$contents = 'This, is not a valid .csv file, even though, it has, the correct, number, of commas';
			$guid = null;
			$this->_setupTestUploadCsv();
			$this->controller->Session->expects($this->once())->method('setFlash')->with('That did not seem to be a valid bank .csv file');
			$this->controller->Nav->expects($this->never())->method('add');
			$this->_runTestUploadCsv($contents, $guid);

			$this->assertArrayHasKey('Location', $this->headers);
			$this->assertContains('/members/uploadCsv', $this->headers['Location']);
		}

		public function testUploadCsvValidFileNoMembers()
		{
			$contents =
			'Date, Type, Description, Value, Balance, Account Name, Account Number
			,,,,,,
			,,,,,,
			,,,,,,
			06/02/2013,BAC,"\'A NAME , HSNOTTSVD74BY3C8 , FP 06/02/13 0138 , 300000000062834772",15,1664.08,\'NOTTINGHACK,\'558899-45687951
			,,,,,,
			06/02/2013,BAC,"\'DOROTHY D D/2011 , DOROTHY DEVAL",15,1679.08,\'NOTTINGHACK,\'558899-45687951
			,,,,,,
			06/02/2013,BAC,"\'SIMPMSON T , HSNOTTSTYX339RW3",10,1689.08,\'NOTTINGHACK,\'558899-45687951
			,,,,,,
			07/02/2013,BAC,"\'C DAVIES , CHRIS , FP 07/02/13 0034 , 00156265632BBBVSCR",5,1694.08,\'NOTTINGHACK,\'558899-45687951';
			$guid = null;
			$this->_setupTestUploadCsv();

			$this->controller->Session->expects($this->once())->method('setFlash')->with('No new member payments in .csv.');
			$this->controller->Nav->expects($this->never())->method('add');

			$this->_runTestUploadCsv($contents, $guid);

			$this->assertArrayHasKey('Location', $this->headers);
			$this->assertContains('/members', $this->headers['Location']);
		}

		public function testUploadCsvDudGuid()
		{
			$contents = null;
			$guid = '123456789';
			$this->_setupTestUploadCsv();
			$this->controller->Session->expects($this->never())->method('setFlash');
			$this->controller->Nav->expects($this->never())->method('add');
			$this->_runTestUploadCsv($contents, $guid);
		}

		public function testUploadValidFile()
		{
			$contents = 
			'Date, Type, Description, Value, Balance, Account Name, Account Number
			,,,,,,
			,,,,,,
			,,,,,,
			06/02/2013,BAC,"\'A NAME , HSNOTTSFGXWGKF48 , FP 06/02/13 0138 , 300000000062834772",15,1664.08,\'NOTTINGHACK,\'558899-45687951
			,,,,,,
			06/02/2013,BAC,"\'DOROTHY D D/2011 , DOROTHY DEVAL",15,1679.08,\'NOTTINGHACK,\'558899-45687951
			,,,,,,
			24/02/2013,BAC,"\'SIMPMSON T , HSNOTTSHVQGT3XF2",10,1689.08,\'NOTTINGHACK,\'558899-45687951
			,,,,,,
			07/02/2013,BAC,"\'C DAVIES , CHRIS , FP 07/02/13 0034 , 00156265632BBBVSCR",5,1694.08,\'NOTTINGHACK,\'558899-45687951';
			$guid = null;

			$generatedGuid = String::uuid();
			$this->_setupTestUploadCsv();
			$this->controller->Session->expects($this->never())->method('setFlash');
			$this->controller->Nav->expects($this->once())->method('add')->with('Approve All', 'members', 'uploadCsv', array($generatedGuid), 'positive');
			$this->controller->expects($this->once())->method('getMemberIdSessionKey')->will($this->returnValue($generatedGuid));

			$this->_runTestUploadCsv($contents, $guid);

			$this->_setupTestUploadCsv();
			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));
			$this->controller->Session->expects($this->once())->method('setFlash')->with('Successfully approved member Ryan Miles\nSuccessfully approved member Evan Atkinson\n');
			$this->controller->Nav->expects($this->never())->method('add');

			// Email stuff
			$this->controller->email->expects($this->exactly(4))->method('config');
			$this->controller->email->expects($this->exactly(4))->method('from');
			$this->controller->email->expects($this->exactly(4))->method('sender');
			$this->controller->email->expects($this->exactly(4))->method('emailFormat');
			$this->controller->email->expects($this->exactly(4))->method('to');
			$this->controller->email->expects($this->exactly(4))->method('subject');
			$this->controller->email->expects($this->exactly(4))->method('template');
			$this->controller->email->expects($this->exactly(4))->method('viewVars');
			$this->controller->email->expects($this->exactly(4))->method('send')->will($this->returnValue(true));
			
			$this->_runTestUploadCsv($contents, $generatedGuid);

			$this->assertEqual($this->controller->Member->getStatusForMember(13), Status::CURRENT_MEMBER);
			$this->assertEqual($this->controller->Member->getStatusForMember(14), Status::CURRENT_MEMBER);
		}

		private function _setupTestUploadCsv()
		{
			$this->controller = $this->generate('Members', array(
				'components' => array(
					'Auth' => array(
						'user',
					),
					'Session' => array(
						'setFlash',
					),
					'Nav' => array(
						'add',
					),
				),
				'methods' => array(
					'getMemberIdSessionKey',
				),
			));

			$mockEmail = $this->getMock('CakeEmail');
			$this->controller->email = $mockEmail;

			$this->controller->Member->setDataSource('test');
			$this->controller->Member->Account->setDataSource('test');
		}

		private function _runTestUploadCsv($fileContents, $guid)
		{
			$action = 'members/uploadCsv';
			if($guid != null)
			{
				$action .= '/' . $guid;
			}

			if($fileContents == null)
			{
				$this->testAction($action);
			}
			else
			{
				$data = $this->_makeFileUploadData($fileContents);
				$this->testAction($action, array('data' => $data, 'method' => 'post'));
			}
		}

		private function _makeFileUploadData($contents)
		{
			$filePath = $this->_makeTmpFile($contents);
			if($filePath != false)
			{
				return array(
					'FileUpload' => array(
						'filename' => array(
							'name' => 'uploaded.tmp',
							'type' => $this->_getFileType($filePath),
							'tmp_name' => $filePath,
							'error' => 0,
							'size' => filesize($filePath),
						),
					),
				);
			}

			return false;
		}

		private function _getFileType($filename)
		{
			// None of the proper ways to do this seem to work, but it doesn't matter for our tests
			return 'text';
		}

		private function _makeTmpFile($contents)
		{
			$tmpFile = tempnam(sys_get_temp_dir(), 'tst');
			if($tmpFile != false)
			{
				$filehandle = fopen($tmpFile, 'w');
				if($filehandle != false)
				{
					$success = fwrite($filehandle, $contents);
					fclose($filehandle);
					if($success)
					{
						return $tmpFile;
					}
				}
			}

			return false;
		}
	}

?>