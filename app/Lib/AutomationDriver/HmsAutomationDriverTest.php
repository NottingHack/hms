<?php
	
	require_once('AutomationDriverTest.php');

	class HmsAutomationDriverTest extends AutomationDriverTest
	{
		public function reigsterNewMember($email)
		{
			// Go to the member registration page
			$this->assertTrue( $this->automationDriver->navigateToMemberRegister(), 'Unable to navigate to member register page.' );
			$this->assertTrue( $this->automationDriver->pageHasNoErrors(), 'Member register page has errors.' );

			// Grab the e-mail field
			$memberEmail = $this->automationDriver->getElementById('MemberEmail');
			// Type out the e-mail
			$memberEmail->sendKeys($email);

			$memberEmail->submit();
        }
	}
	
?>