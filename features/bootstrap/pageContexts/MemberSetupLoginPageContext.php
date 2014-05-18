<?php

require_once (__DIR__ . '/../HmsContext.php');

use Behat\Behat\Context\Step\Given;
use Behat\Behat\Context\Step\Then;
use Behat\Behat\Context\Step\When;

class MemberSetupLoginPageContext extends HmsContext {

/**
 *
 * @When I am on the member setup login page for a prospective member
 */
	public function IAmOnTheMemberSetupLoginPageForAProspectiveMember() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		$databaseContext = $this->getMainContext()->getSubcontext('database');
		$idAndEmail = $databaseContext->getIdAndEmailForMemberWithStatus($memberContext::STATUS_PROSPECTIVE_MEMBER);

		$memberContext->setTestMemberData('id', $idAndEmail['id']);
		$memberContext->setTestMemberData('email', $idAndEmail['email']);
		return new Then('I go to "/members/setupLogin/' . $idAndEmail['id'] . '"');
	}

/**
 * 
 * @when I fill in the setup login form
 */
	public function IFillInTheSetupDetailsForm() {
		$memberContext = $this->getMainContext()->getSubcontext('member');

		$username = $memberContext->getNewUsername();
		$password = 'hunter2';
		$formData = array(
			'MemberFirstname' => 'Bob',
			'MemberSurname' => 'Bobson',
			'MemberUsername' => $username,
			'MemberEmail' => $memberContext->getTestMemberData('email'),
			'MemberPassword' => $password,
			'MemberPasswordConfirm' => $password,
		);

		$stepList = array();
		foreach ($formData as $inputName => $value) {
			array_push($stepList, new When('I fill in "' . $inputName . '" with "' . $value . '"'));
		}

		return $stepList;
	}

/**
 * 
 * @when I submit the setup login form
 */
	public function iSubmitTheSetupDetailsForm() {
		return new When('I press "Create"');
	}

/**
 * 
 * @Then I should see the login details set message
 */
	public function iShouldSeeTheLoginDetailsSetMessage() {
		return new Then('I should see "Username and Password set, please login."');
	}
}