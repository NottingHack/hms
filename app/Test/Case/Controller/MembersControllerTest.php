<?php

	App::uses('MembersController', 'Controller');

	class MembersControllerTest extends ControllerTestCase
	{
		public $fixtures = array( 'app.Member', 'app.Status', 'app.Group', 'app.GroupsMember', 'app.Account', 'app.Pin' );

		public function setUp() 
        {
        	parent::setUp();

            $this->MembersController = new MembersController();
            $this->MembersController->constructClasses();
        }

		public function testIsAuthorized()
		{
			// Need fake requests for all the functions we need to test
			$fakeRequestDetails = array(
				array( 'name' => 'index', 							'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'list_members', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'list_members_with_status', 		'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'email_members_with_status', 		'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'search', 							'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'set_member_status', 				'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'accept_details', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'reject_details', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'approve_member', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'send_membership_reminder', 		'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'send_contact_details_reminder', 	'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'send_so_details_reminder', 		'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'add_existing_member', 			'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),

				array( 'name' => 'change_password', 				'params' => array('ourId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
				array( 'name' => 'change_password', 				'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),

				array( 'name' => 'view', 							'params' => array('ourId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
				array( 'name' => 'view', 							'params' => array('otherId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),

				array( 'name' => 'edit', 							'params' => array('ourId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
				array( 'name' => 'edit', 							'params' => array('otherId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),

				array( 'name' => 'setup_details', 					'params' => array('ourId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
				array( 'name' => 'setup_details', 					'params' => array('otherId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),

				array( 'name' => 'login', 							'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
				array( 'name' => 'logout', 							'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
				array( 'name' => 'setupLogin', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
			);

			$testUsers = array(
				'fullAccessMember' => array(
					'ourId' => 1,
					'otherId' => 3,
				),

				'memberAdminMember' => array(
					'ourId' => 5,
					'otherId' => 2,
				),

				'normalMember' => array(
					'ourId' => 3,
					'otherId' => 1,
				),
			);

			foreach($testUsers as $userName => $user)
			{
				$userId = $user['ourId'];
				$userInfo = $this->MembersController->Member->findByMemberId($userId);
				foreach($fakeRequestDetails as $reqDetails)
				{
					$actionName = $reqDetails['name'];
					$params = array();

					if(!empty($reqDetails['params']))
					{
						foreach($reqDetails['params'] as $param)
						{
							array_push($params, $user[$param]);
						}
					}

					$requestObj = $this->_buildFakeRequest($actionName, $params);
					$expectedResult = in_array($userName, $reqDetails['access']);
					$actualResult = $this->MembersController->isAuthorized($userInfo, $requestObj);
					$this->assertIdentical( $actualResult, $expectedResult, sprintf('isAuthorized returned %s for %s (id: %d) when accessing %s.', $actualResult ? 'true' : 'false', $userName, $userId, $requestObj->url));
				}
			}
		}

		public function testBeforeFilter()
		{
			$prevAllowedActions = $this->MembersController->Auth->allowedActions;
			$this->assertIdentical( count($prevAllowedActions), 0, 'Prior to calling \'beforeFilter\' the allowed actions array was not empty.' );

			$this->MembersController->beforeFilter();
			$afterAllowedActions = $this->MembersController->Auth->allowedActions;
			$this->assertTrue( in_array('logout', $afterAllowedActions), 'Allowed actions does not contain \'logout\'.' );
			$this->assertTrue( in_array('login', $afterAllowedActions), 'Allowed actions does not contain \'login\'.' );
			$this->assertTrue( in_array('register', $afterAllowedActions), 'Allowed actions does not contain \'register\'.' );
			$this->assertTrue( in_array('forgot_password', $afterAllowedActions), 'Allowed actions does not contain \'forgot_password\'.' );
			$this->assertTrue( in_array('setupLogin', $afterAllowedActions), 'Allowed actions does not contain \'setupLogin\'.' );
			$this->assertTrue( in_array('setup_details', $afterAllowedActions), 'Allowed actions does not contain \'setup_details\'.' );
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

				$this->assertArrayHasKey( 'name', $memberInfo, 'Member has no name.' ); 
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

					$this->assertArrayHasKey( 'name', $memberInfo, 'Member has no name.' ); 
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
		        'query' => 'and',
		    );

		    $this->testAction('/members/search', array('data' => $data, 'method' => 'get'));

			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );
			$this->assertArrayHasKey( 'memberList', $this->vars, 'No view value called \'memberList\'.' ); 

			$this->assertInternalType( 'array', $this->vars['memberList'], 'No array by the name of memberInfo' );

			foreach ($this->vars['memberList'] as $memberInfo)
			{
				$this->assertArrayHasKey( 'id', $memberInfo, 'Member has no id.' ); 
				$this->assertGreaterThan( 0, $memberInfo['id'], 'Member id is invalid.' );

				$this->assertArrayHasKey( 'name', $memberInfo, 'Member has no name.' ); 
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

			$this->testAction('/members/register', array('data' => array('Member' => array('email' => $emailAddress)), 'method' => 'post'));

			$this->_testRegisterMailingListViewVars();

			// Should have created a new member
			$memberInfo = $this->MembersController->Member->findByEmail('foo@bar.org');

			$this->assertInternalType( 'array', $memberInfo, 'Member record is not an array.' );
			$this->assertEqual( Hash::get($memberInfo, 'Member.member_id'), 15, 'Member has incorrect id.' );
			$this->assertEqual( Hash::get($memberInfo, 'Member.email'), $emailAddress, 'Member has incorrect email.' );

			$this->assertContains('/pages/home', $this->headers['Location']);
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

			$this->testAction('/members/register', array('data' => array('Member' => array('email' => $emailAddress)), 'method' => 'post'));

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

			$this->assertContains('/members/login', $this->headers['Location']);
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
			$this->assertContains('/members/login', $this->headers['Location']);
		}

		private function _testRegisterMailingListViewVars()
		{
			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );
			$this->assertArrayHasKey( 'mailingLists', $this->vars, 'No view value called \'memberList\'.' );
		}

		private function _mockMemberEmail()
		{
			$this->controller = $this->generate('Members');
			$mockEmail = $this->getMock('CakeEmail');
			$this->controller->email = $mockEmail;
			return $mockEmail;
		}

		private function _buildFakeRequest($action, $params = array())
		{
			$url = '/' . 'MembersController' . '/' . $action;

			if(count($params) > 0)
			{
				$url .= '/' . join($params, '/');
			}

			$request = new CakeRequest($url, false);
			$request->addParams(array(
				'plugin' => null,
				'controller' => 'MembersController',
				'action' => $action,
				'pass' => $params,
			));

			return $request;
		}
	}

?>