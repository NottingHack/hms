<?php

	class MembersControllerTest extends ControllerTestCase
	{
		public $fixtures = array( 'app.Member', 'app.Status' );

		public function testIsAuthorized()
		{
			$result = $this->testAction('/members/index');
        	debug($result);
		}
	}

?>