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
				array( 'name' => 'setup_login', 					'params' => array(), 			'access' => array( 'fullAccessMember', 'memberAdminMember', 'normalMember' ) ),
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
			$this->assertTrue( in_array('setup_login', $afterAllowedActions), 'Allowed actions does not contain \'setup_login\'.' );
			$this->assertTrue( in_array('setup_details', $afterAllowedActions), 'Allowed actions does not contain \'setup_details\'.' );
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