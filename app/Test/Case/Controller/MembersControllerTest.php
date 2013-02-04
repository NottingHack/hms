<?php

	App::uses('MembersController', 'Controller');
	App::uses('Member', 'Model');
	App::uses('PhpReader', 'Configure');
	Configure::config('default', new PhpReader());
	Configure::load('hms', 'default');

	class MembersControllerTest extends ControllerTestCase
	{
		public $fixtures = array( 'app.Member', 'app.Status', 'app.Group', 'app.GroupsMember', 'app.Account', 'app.Pin', 'app.StatusUpdate' );

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