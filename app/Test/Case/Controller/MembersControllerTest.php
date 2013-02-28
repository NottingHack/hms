<?php

	App::uses('MembersController', 'Controller');
	App::uses('Member', 'Model');
	App::uses('PhpReader', 'Configure');
	Configure::config('default', new PhpReader());
	Configure::load('hms', 'default');

	class MembersControllerTest extends ControllerTestCase
	{
		public $fixtures = array( 'app.Member', 'app.Status', 'app.Group', 'app.GroupsMember', 'app.Account', 'app.Pin', 'app.StatusUpdate', 'app.ForgotPassword' );

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
				array( 'name' => 'listMembers', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'listMembersWithStatus', 			'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'emailMembersWithStatus', 			'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'search', 							'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'setMemberStatus', 				'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'acceptDetails', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'rejectDetails', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'approveMember', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'sendMembershipReminder', 			'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'sendContactDetailsReminder', 		'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'sendSoDetailsReminder', 			'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),
				array( 'name' => 'addExistingMember', 				'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),

				array( 'name' => 'changePassword', 					'params' => array('ourId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
				array( 'name' => 'changePassword', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),

				array( 'name' => 'view', 							'params' => array('ourId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
				array( 'name' => 'view', 							'params' => array('otherId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),

				array( 'name' => 'edit', 							'params' => array('ourId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
				array( 'name' => 'edit', 							'params' => array('otherId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),

				array( 'name' => 'setupDetails', 					'params' => array('ourId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
				array( 'name' => 'setupDetails', 					'params' => array('otherId'), 	'access' => array( 'fullAccessMember', 'memberAdminMember' ) ),

				array( 'name' => 'login', 							'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
				array( 'name' => 'logout', 							'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
				array( 'name' => 'setupLogin', 						'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
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
						'name' => 'Tony',
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
						'name' => 'Cheryl',
						'username' => 'dayrep',
						'email' => 'CherylLCarignan@teleworm.us',
						'password' => 'hunter2',
						'password_confirm' => 'hunter2'
					),
				),
				8 => array(
					'Member' => array(
						'name' => 'Melvin',
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

			$this->testAction('/members/setupDetails/9', array('data' => $data, 'method' => 'post'));
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
			$mockEmail->expects($this->at(7))->method('viewVars')->with(array('message' => 'barrrrrrrrrrrr'));
			$mockEmail->expects($this->at(8))->method('send')->will($this->returnValue(true));

            $this->testAction('/members/rejectDetails/11', array('data' => $data, 'method' => 'post'));
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect did not occurred.' );
			$this->assertContains('/members/view/11', $this->headers['Location'], 'Redirect to member view did not occur.' );
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

				$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );
				$this->assertArrayHasKey( 'accounts', $this->vars, 'No view value called \'accounts\'.' );
				$this->assertEqual( $this->vars['accounts'], array( '-1' => 'Create new', '1' => 'Mathew Pryce', '2' => 'Annabelle Santini', '3' => 'Guy Viles, Kelly Savala and Jessie Easterwood', '6' => 'Guy Garrette', '7' => 'Ryan Miles', '8' => 'Evan Atkinson' ), 'Accounts view var not set correctly.' );
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

			$fakePaymentRef = 'HSNOTTSTYX339RW444';
			$this->controller->Member->expects($this->exactly(2))->method('getSoDetails')->will($this->returnValue(array('name' => 'Roy J. Forsman', 'email' => 'RoyJForsman@teleworm.us', 'paymentRef' => $fakePaymentRef)));
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
			$mockEmail->expects($this->at(7))->method('viewVars')->with(array('name' => 'Roy J. Forsman', 'paymentRef' => $fakePaymentRef, 'accountNum' => Configure::read('hms_so_accountNumber'), 'sortCode' => Configure::read('hms_so_sortCode'), 'accountName' => Configure::read('hms_so_accountName') ));
			$mockEmail->expects($this->at(8))->method('send')->will($this->returnValue(true));

			$mockEmail->expects($this->at(9))->method('config')->with('smtp');
			$mockEmail->expects($this->at(10))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(11))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(12))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(13))->method('to')->with(array('j.easterwood@googlemail.com'));
			$mockEmail->expects($this->at(14))->method('subject')->with('Impending Payment');
			$mockEmail->expects($this->at(15))->method('template')->with('notify_admins_payment_incoming');
			$mockEmail->expects($this->at(16))->method('viewVars')->with(array('name' => 'Roy J. Forsman', 'email' => 'RoyJForsman@teleworm.us', 'paymentRef' => $fakePaymentRef));
			$mockEmail->expects($this->at(17))->method('send')->will($this->returnValue(true));

            $this->testAction('/members/acceptDetails/12', array('data' => $data, 'method' => 'post'));
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect did not occurred.' );
			$this->assertContains('/members/view/12', $this->headers['Location'], 'Redirect to member view did not occur.' );

			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );
			$this->assertArrayHasKey( 'accounts', $this->vars, 'No view value called \'accounts\'.' );
			$this->assertEqual( $this->vars['accounts'], array( '-1' => 'Create new', '1' => 'Mathew Pryce', '2' => 'Annabelle Santini', '3' => 'Guy Viles, Kelly Savala and Jessie Easterwood', '6' => 'Guy Garrette', '7' => 'Ryan Miles', '8' => 'Evan Atkinson' ), 'Accounts view var not set correctly.' );
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
            $this->controller->Member->expects($this->exactly(1))->method('getApproveDetails')->will($this->returnValue(array('name' => 'Ryan Miles', 'id' => 13, 'email' => 'RyanMiles@dayrep.com', 'pin' => '2234')));

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
			$mockEmail->expects($this->at(4))->method('to')->with('j.easterwood@googlemail.com');
			$mockEmail->expects($this->at(5))->method('subject')->with('Member Approved');
			$mockEmail->expects($this->at(6))->method('template')->with('notify_admins_member_approved');
			$mockEmail->expects($this->at(7))->method('viewVars')->with(array('name' => 'Ryan Miles', 'id' => 13, 'email' => 'RyanMiles@dayrep.com', 'pin' => '2234'));
			$mockEmail->expects($this->at(8))->method('send')->will($this->returnValue(true));

			$mockEmail->expects($this->at(9))->method('config')->with('smtp');
			$mockEmail->expects($this->at(10))->method('from')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(11))->method('sender')->with(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$mockEmail->expects($this->at(12))->method('emailFormat')->with('html');
			$mockEmail->expects($this->at(13))->method('to')->with('RyanMiles@dayrep.com');
			$mockEmail->expects($this->at(14))->method('subject')->with('Membership Complete');
			$mockEmail->expects($this->at(15))->method('template')->with('to_member_access_details');
			$mockEmail->expects($this->at(16))->method('viewVars')->with(array('adminName' => 'Jessie Easterwood', 'adminEmail' => 'j.easterwood@googlemail.com', 'manLink' => Configure::read('hms_help_manual_url'), 'outerDoorCode' => Configure::read('hms_access_street_door'), 'innerDoorCode' => Configure::read('hms_access_inner_door'), 'wifiSsid' => Configure::read('hms_access_wifi_ssid'), 'wifiPass' => Configure::read('hms_access_wifi_password')));
			$mockEmail->expects($this->at(17))->method('send')->will($this->returnValue(true));

            $this->testAction('/members/approveMember/13');
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect did not occurr.' );
			//$this->assertContains('/members/view/11', $this->headers['Location'], 'Redirect to member view did not occur.' );
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
			$this->assertIdentical( $this->vars['name'], 'Guy Viles', 'Incorrect name value.' );
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
			$this->assertIdentical( $this->vars['name'], 'Jessie Easterwood', 'Incorrect name value.' );
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
			$this->assertIdentical( $this->vars['name'], 'Annabelle Santini', 'Incorrect name value.' );
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

			$this->testAction('/members/forgotPassword', array('data' => $data, 'method' => 'post'));

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

			$this->testAction('/members/sendMembershipReminder/7');
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
			$mockEmail->expects($this->at(7))->method('viewVars')->with(array('name' => 'Ryan Miles', 'paymentRef' => 'HSNOTTSFGXWGKF48QB', 'accountNum' => Configure::read('hms_so_accountNumber'), 'sortCode' => Configure::read('hms_so_sortCode'), 'accountName' => Configure::read('hms_so_accountName') ));
			$mockEmail->expects($this->at(8))->method('send')->will($this->returnValue(true));

			$this->testAction('/members/sendSoDetailsReminder/13');
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

			$this->testAction('/members/sendContactDetailsReminder/10');
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
            	)
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));

			$this->controller->Nav->expects($this->exactly(3))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('Edit', 'members', 'edit', array(4));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Change Password', 'members', 'changePassword', array(4));
			$this->controller->Nav->expects($this->at(2))->method('add')->with('Revoke Membership', 'members', 'setMemberStatus', array(4, Status::EX_MEMBER));

			// Should not redirect, and should populate 
			$this->testAction('members/view/4');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );

			$expectedMemberInfo = array(
				'id' => '4',
				'name' => 'Kelly Savala',
				'username' => 'huskycolossus',
				'handle' => 'bildestonelectrician',
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
				'pin' => '5436',
				'paymentRef' => 'HSNOTTSYT7H4CW3GP9',
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
				),
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
            	)
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(1));

			$this->controller->Nav->expects($this->exactly(3))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('Edit', 'members', 'edit', array(4));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Change Password', 'members', 'changePassword', array(4));
			$this->controller->Nav->expects($this->at(2))->method('add')->with('Revoke Membership', 'members', 'setMemberStatus', array(4, Status::EX_MEMBER));

			// Should not redirect, and should populate 
			$this->testAction('members/view/4');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );

			$expectedMemberInfo = array(
				'id' => '4',
				'name' => 'Kelly Savala',
				'username' => 'huskycolossus',
				'handle' => 'bildestonelectrician',
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
				'pin' => '5436',
				'paymentRef' => 'HSNOTTSYT7H4CW3GP9',
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
				),
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
            	)
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(4));

			$this->controller->Nav->expects($this->exactly(3))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('Edit', 'members', 'edit', array(4));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Change Password', 'members', 'changePassword', array(4));
			$this->controller->Nav->expects($this->at(2))->method('add')->with('Revoke Membership', 'members', 'setMemberStatus', array(4, Status::EX_MEMBER));

			// Should not redirect, and should populate 
			$this->testAction('members/view/4');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );

			$expectedMemberInfo = array(
				'id' => '4',
				'name' => 'Kelly Savala',
				'username' => 'huskycolossus',
				'handle' => 'bildestonelectrician',
				'email' => 'k.savala@yahoo.co.uk',
				'joinDate' => '2010-09-22',
				'unlockText' => 'Hey Kelly',
				'balance' => '-5649',
				'creditLimit' => '5000',
				'paymentRef' => 'HSNOTTSYT7H4CW3GP9',
				'address' => array(
					'part1' => '8 Elm Close',
					'part2' => 'Tetsworth',
					'city' => 'Thame',
					'postcode' => 'OX9 7AP',
				),
				'contactNumber' => '079 0644 8720',
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
            	)
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));

			$this->controller->Nav->expects($this->exactly(3))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('Edit', 'members', 'edit', array(7));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Change Password', 'members', 'changePassword', array(7));
			$this->controller->Nav->expects($this->at(2))->method('add')->with('Send Membership Reminder', 'members', 'sendMembershipReminder', array(7));

			// Should not redirect, and should populate 
			$this->testAction('members/view/7');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );

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
            	)
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(9));

			$this->controller->Nav->expects($this->exactly(2))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('Edit', 'members', 'edit', array(9));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Change Password', 'members', 'changePassword', array(9));

			// Should not redirect, and should populate 
			$this->testAction('members/view/9');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );

			$expectedMemberInfo = array(
				'id' => '9',
				'name' => 'Dorothy D. Russell',
				'username' => 'Warang29',
				'handle' => 'Warang29',
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
            	)
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(11));

			$this->controller->Nav->expects($this->exactly(3))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('Edit', 'members', 'edit', array(11));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Change Password', 'members', 'changePassword', array(11));
			$this->controller->Nav->expects($this->at(2))->method('add')->with('Send Contact Details Reminder', 'members', 'sendContactDetailsReminder', array(11));

			// Should not redirect, and should populate 
			$this->testAction('members/view/11');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );

			$expectedMemberInfo = array(
				'id' => '11',
				'name' => 'Betty C. Paris',
				'username' => 'Beltonstlend51',
				'handle' => 'Beltonstlend51',
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
            	)
            ));

			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(4));

			$this->controller->Nav->expects($this->exactly(3))->method('add');
			$this->controller->Nav->expects($this->at(0))->method('add')->with('Edit', 'members', 'edit', array(4));
			$this->controller->Nav->expects($this->at(1))->method('add')->with('Change Password', 'members', 'changePassword', array(4));
			$this->controller->Nav->expects($this->at(2))->method('add')->with('Revoke Membership', 'members', 'setMemberStatus', array(4, Status::EX_MEMBER));

			// Should not redirect, and should populate 
			$this->testAction('members/view/4');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );
			$this->assertIdentical( count($this->vars), 1, 'Unexpected number of view values.' );

			$expectedMemberInfo = array(
				'id' => '4',
				'name' => 'Kelly Savala',
				'username' => 'huskycolossus',
				'handle' => 'bildestonelectrician',
				'email' => 'k.savala@yahoo.co.uk',
				'joinDate' => '2010-09-22',
				'unlockText' => 'Hey Kelly',
				'balance' => '-5649',
				'creditLimit' => '5000',
				'paymentRef' => 'HSNOTTSYT7H4CW3GP9',
				'address' => array(
					'part1' => '8 Elm Close',
					'part2' => 'Tetsworth',
					'city' => 'Thame',
					'postcode' => 'OX9 7AP',
				),
				'contactNumber' => '079 0644 8720',
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
            	)
            ));

            $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(2));

			$this->testAction('members/edit/2');
			$this->assertArrayNotHasKey( 'Location', $this->headers, 'Redirect has occurred.' );

			$expectedMemberVal = array(
				'id' => '2',
				'name' => 'Annabelle Santini',
				'username' => 'pecanpaella',
				'handle' => 'mammetwarpsgrove',
				'email' => 'a.santini@hotmail.com',
				'joinDate' => '2011-02-24',
				'unlockText' => 'Welcome Annabelle',
				'balance' => '0',
				'creditLimit' => '5000',
				'paymentRef' => 'HSNOTTSK2R62GQW684',
				'address' => array(
					'part1' => '1 Saint Paul\'s Church Yard',
					'part2' => 'The City',
					'city' => 'London',
					'postcode' => 'EC4M 8SH',
				),
				'contactNumber' => '077 1755 4342',
			);
			$this->_testEditMemberViewVars($expectedMemberVal);
		}

		public function testEditMemberEditOwn()
		{
			$inputData = array(
            	'Member' => array(
					'name' => 'Nat',
					'username' => 'foo',
					'handle' => 'thisisahandle',
					'email' => 'totallydifferent@hotmail.com',
					'unlock_text' => 'Would you kindly?',
					'address_1' => '5 Henry Way',
					'address_2' => '',
					'address_city' => 'Bobbington',
					'address_postcode' => 'FU453JD',
					'contact_number' => '079716523804',
				),
			);

			$expectedViewVal = array(
				'id' => '2',
				'name' => 'Annabelle Santini',
				'username' => 'pecanpaella',
				'handle' => 'mammetwarpsgrove',
				'email' => 'a.santini@hotmail.com',
				'joinDate' => '2011-02-24',
				'unlockText' => 'Welcome Annabelle',
				'balance' => '0',
				'creditLimit' => '5000',
				'paymentRef' => 'HSNOTTSK2R62GQW684',
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
            		'member_number' => null,
					'name' => 'Nat',
					'username' => 'foo',
					'handle' => 'thisisahandle',
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
				    'description' => 'Active member',
				),
				'Account' => array(
				    'account_id' => '2',
				    'payment_ref' => 'HSNOTTSK2R62GQW684',
				),
				'Pin' => array(
				    'pin_id' => '2',
				    'pin' => '7422',
				    'unlock_text' => 'NOT USED',
				    'date_added' => '2012-12-03 23:56:43',
				    'expiry' => null,
				    'state' => '30',
				    'member_id' => '2',
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

			$this->_testEditMember($inputData, $expectedViewVal, $expectedRecordData);
		}

		public function testEditMemberEditOwnAllValues()
		{
			$inputData = array(
            	'Member' => array(
            		'member_id' => '243',
            		'account_id' => '325',
            		'member_status' => '1',
            		'join_date' => '2013-12-30',
            		'balance' => '100000',
            		'credit_limit' => '20000',
            		'member_number' => '4954365',
					'name' => 'Nat',
					'username' => 'foo',
					'handle' => 'thisisahandle',
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
				    'description' => 'All the awesome',
				),
				'Account' => array(
				    'account_id' => '56',
				    'payment_ref' => 'INEEDNOPAYMENTREF',
				),
				'Pin' => array(
				    'pin_id' => '4',
				    'pin' => '5555',
				    'unlock_text' => 'MAYBE USED',
				    'date_added' => '2010-01-01 00:00:00',
				    'expiry' => 'NEVER!',
				    'state' => '22',
				    'member_id' => '15',
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
			);

			$expectedViewVal = array(
				'id' => '2',
				'name' => 'Annabelle Santini',
				'username' => 'pecanpaella',
				'handle' => 'mammetwarpsgrove',
				'email' => 'a.santini@hotmail.com',
				'joinDate' => '2011-02-24',
				'unlockText' => 'Welcome Annabelle',
				'balance' => '0',
				'creditLimit' => '5000',
				'paymentRef' => 'HSNOTTSK2R62GQW684',
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
            		'member_number' => null,
					'name' => 'Nat',
					'username' => 'foo',
					'handle' => 'thisisahandle',
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
				    'description' => 'Active member',
				),
				'Account' => array(
				    'account_id' => '2',
				    'payment_ref' => 'HSNOTTSK2R62GQW684',
				),
				'Pin' => array(
				    'pin_id' => '2',
				    'pin' => '7422',
				    'unlock_text' => 'NOT USED',
				    'date_added' => '2012-12-03 23:56:43',
				    'expiry' => null,
				    'state' => '30',
				    'member_id' => '2',
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
			$this->_testEditMember($inputData, $expectedViewVal, $expectedRecordData);
		}

		private function _testEditMember($inputData, $expectedViewVal, $expectedRecordData)
		{
			$this->controller = $this->generate('Members', array(
            	'components' => array(
            		'Auth' => array(
            			'user',
            		),
            	)
            ));

            $this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(2));

			$this->testAction('members/edit/2', array('data' => $inputData, 'method' => 'post'));
			$this->assertArrayHasKey( 'Location', $this->headers, 'Redirect has not occurred.' );
			$this->assertContains('/members/view/2', $this->headers['Location']);

			
			$this->_testEditMemberViewVars($expectedViewVal);

			$record = $this->controller->Member->find('first', array('conditions' => array('Member.member_id' => 2)));

			$this->assertEqual( $record, $expectedRecordData, 'Member record was not updated correctly.' );
		}

		public function _testEditMemberViewVars($expectedMemberVal)
		{
			$this->assertIdentical( count($this->vars), 4, 'Unexpected number of view values.' );
			$this->assertArrayHasKey( 'member', $this->vars, 'No view value called \'members\'.' );
			$this->assertArrayHasKey( 'accounts', $this->vars, 'No view value called \'accounts\'.' );
			$this->assertArrayHasKey( 'statuses', $this->vars, 'No view value called \'statuses\'.' );
			$this->assertArrayHasKey( 'groups', $this->vars, 'No view value called \'groups\'.' );

			$this->assertEqual( $this->vars['member'], $expectedMemberVal, 'Member array is incorrect.' );

			$expectedAccountsVal = array(
				'-1' => 'Create new', 
				'1' => 'Mathew Pryce', 
				'2' => 'Annabelle Santini', 
				'3' => 'Guy Viles, Kelly Savala and Jessie Easterwood', 
				'6' => 'Guy Garrette', 
				'7' => 'Ryan Miles', 
				'8' => 'Evan Atkinson' 
			);

			$this->assertEqual( $this->vars['accounts'], $expectedAccountsVal, 'Accounts array is incorrect.' );

			$expectedStatusesVal = array(
				0 => array('id' => '1', 'name' => 'Prospective Member', 'description' => 'Interested in the hackspace, we have their e-mail. May be receiving the newsletter', 'count' => 2),
				1 => array('id' => '2', 'name' => 'Pre-Member (stage 1)', 'description' => 'Member has HMS login details, waiting for them to enter contact details', 'count' => 2),
				2 => array('id' => '3', 'name' => 'Pre-Member (stage 2)', 'description' => 'Waiting for member-admin to approve contact details', 'count' => 2),
				3 => array('id' => '4', 'name' => 'Pre-Member (stage 3)', 'description' => 'Waiting for standing order', 'count' => 2),
				4 => array('id' => '5', 'name' => 'Current Member', 'description' => 'Active member', 'count' => 5),
				5 => array('id' => '6', 'name' => 'Ex Member', 'description' => 'Former member, details only kept for a while', 'count' => 1),
			);

			$this->assertEqual( $this->vars['statuses'], $expectedStatusesVal, 'Status array is incorrect.' );

			$expectedGroupsVal = array(
				0 => array(
					'id' => '1',
					'description' => 'Full Access',
					'count' => '1',
				),
				1 => array(
					'id' => '2',
					'description' => 'Current Members',
					'count' => '5',
				),
				2 => array(
					'id' => '3',
					'description' => 'Snackspace Admin',
					'count' => '1',
				),
				3 => array(
					'id' => '4',
					'description' => 'Gatekeeper Admin',
					'count' => '1',
				),
				4 => array(
					'id' => '5',
					'description' => 'Member Admin',
					'count' => '1',
				),
			);

			$this->assertEqual( $this->vars['groups'], $expectedGroupsVal, 'Groups array is incorrect.' );

		}

		private function _testEmailMembersWithStatusVewVars()
		{
			$this->assertIdentical( count($this->vars), 2, 'Unexpected number of view values.' );
			$this->assertArrayHasKey( 'members', $this->vars, 'No view value called \'members\'.' );
			$this->assertArrayHasKey( 'status', $this->vars, 'No view value called \'status\'.' );
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