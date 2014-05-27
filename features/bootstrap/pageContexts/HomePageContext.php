<?php

require_once (__DIR__ . '/../HmsContext.php');

use Behat\Behat\Context\Step\Given;
use Behat\Behat\Context\Step\Then;
use Behat\Behat\Context\Step\When;

class HomePageContext extends HmsContext {

/**
 *
 * @Then I am on the homepage
 */
	public function iAmOnTheHomeage() {
		$this->_navigationContext()->assertHomepage();
	}

/**
 * 
 * @when I click the register link on the home page
 */
	public function iClickTheRegisterLinkOnTheHomePage() {
		$this->_navigationContext()->assertHomepage();
		return new Then('I follow "Register Now"');
	}
}