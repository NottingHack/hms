<?php

require_once ('HmsContext.php');

use Behat\Behat\Context\Step\Given;
use Behat\Behat\Context\Step\Then;
use Behat\Behat\Context\Step\When;

class DriverContext extends HmsContext {

/**
 * @When /^I register with a new e-mail address$/
 */
	public function iRegisterWithANewEmailAddress() {
		$email = $this->_memberContext()->getNewEmail();
		$this->_memberContext()->setTestMemberData('email', $email);
		return array(
			new Given("I follow \"Register Now!\""),
			new Given("I fill in \"MemberEmail\" with \"$email\""),
			new When("I press \"Register\""),
			new Then("I should be on \"/pages/home\""),
			new Then("I should see \"Registration successful, please check your inbox.\""),
		);
	}
}