<?php

require_once (__DIR__ . '/../HmsContext.php');

use Behat\Behat\Context\Step\Given;
use Behat\Behat\Context\Step\Then;
use Behat\Behat\Context\Step\When;

class MemberLoginPageContext extends HmsContext {

/**
 *
 * @Then I am on the member login page
 */
	public function iAmOnTheMemberLoginPage() {
		return new Then('I should be on "/members/login"');
	}
}