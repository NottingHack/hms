<?php
/**
 *
 * PHP 5
 *
 * Copyright (C) HMS Team
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     HMS Team
 * @package       app.Lib.AutomationDriver
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

require_once ('AutomationDriverTest.php');

/**
 * Utility functions for acceptance tests.
 */
class HmsAutomationDriverTest extends AutomationDriverTest {

/**
 * Register a new member.
 * @param  string $email E-mail address to register with.
 */
	public function reigsterNewMember($email) {
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