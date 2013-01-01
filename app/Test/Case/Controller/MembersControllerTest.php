<?php

	class MembersControllerTest extends ControllerTestCase
	{
		public $fixtures = array( 'app.Member', 'app.Status', 'app.Group', 'app.GroupsMember', 'app.Account', 'app.Pin' );

		public function testIsAuthorized()
		{
			$result = $this->testAction('/members/index');
        	debug($result);
		}
	}

?>