<?php

	// Make this class abstract to stop PHPUnit attempting to run tests directly from it
	abstract class HmsControllerTestBase extends ControllerTestCase
	{
		protected function _testIsAuthorized($controller, $fakeRequestDetails)
		{
			$testUsers = array(
				'fullAccessMember' => array(
					'ourId' => 1,
					'otherId' => 3,
				),

				'memberAdminMember' => array(
					'ourId' => 5,
					'otherId' => 2,
				),

				'membershipTeamMember' => array(
					'ourId' => 4,
					'otherId' => 5,
				),

				'normalMember' => array(
					'ourId' => 3,
					'otherId' => 1,
				),
			);

			foreach($testUsers as $userName => $user)
			{
				$userId = $user['ourId'];
				$member = ClassRegistry::init('Member');
				$userInfo = $member->findByMemberId($userId);
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

					$requestObj = $this->_buildFakeRequest($controller->name, $actionName, $params);
					$expectedResult = in_array($userName, $reqDetails['access']);
					$actualResult = $controller->isAuthorized($userInfo, $requestObj);
					$this->assertIdentical( $actualResult, $expectedResult, sprintf('isAuthorized returned %s for %s (id: %d) when accessing %s.', $actualResult ? 'true' : 'false', $userName, $userId, $requestObj->url));
				}
			}
		}

		protected function _buildFakeRequest($controllerName, $action, $params = array())
		{
			$url = '/' . $controllerName . '/' . $action;

			if(count($params) > 0)
			{
				$url .= '/' . join($params, '/');
			}

			$request = new CakeRequest($url, false);
			$request->addParams(array(
				'plugin' => null,
				'controller' => $controllerName,
				'action' => $action,
				'pass' => $params,
			));

			return $request;
		}
	}

?>