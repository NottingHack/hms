<?php

require_once (__DIR__ . '/../HmsContext.php');

use Behat\Behat\Context\Step\Given;
use Behat\Behat\Context\Step\Then;
use Behat\Behat\Context\Step\When;

class MemberSetupLoginPageContext extends HmsContext {

	private function __fillInTheSetupLoginForm($username, $email, $password, $passwordConfim) {
		$formData = array(
			'MemberFirstname' => 'Bob',
			'MemberSurname' => 'Bobson',
			'MemberUsername' => $username,
			'MemberEmail' => $email,
			'MemberPassword' => $password,
			'MemberPasswordConfirm' => $passwordConfim,
		);

		$stepList = array();
		foreach ($formData as $inputName => $value) {
			array_push($stepList, new When('I fill in "' . $inputName . '" with "' . $value . '"'));
		}

		return $stepList;
	}

	private function __getSetupLoginPageUrlForTestMember() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		$id = $memberContext->getTestMemberData('id');
		return "/members/setupLogin/$id";
	}

	private function __iAmOnTheMemberSetupLoginPageForAMemberWithStatus($status) {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		$databaseContext = $this->getMainContext()->getSubcontext('database');
		$idAndEmail = $databaseContext->getIdAndEmailForMemberWithStatus($status);

		$memberContext->setTestMemberData('id', $idAndEmail['id']);
		$memberContext->setTestMemberData('email', $idAndEmail['email']);
		return new Then('I go to "' . $this->__getSetupLoginPageUrlForTestMember() . '"');
	}

/**
 * 
 * @Given I am on the member setup login page
 */
	public function iAmOnTheMemberSetupLoginPage() {
		return new Then('I should be on "' . $this->__getSetupLoginPageUrlForTestMember() . '"');
	}

/**
 *
 * @When I am on the member setup login page for a prospective member
 */
	public function IAmOnTheMemberSetupLoginPageForAProspectiveMember() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		return $this->__iAmOnTheMemberSetupLoginPageForAMemberWithStatus($memberContext::STATUS_PROSPECTIVE_MEMBER);
	}

/**
 *
 * @When I am on the member setup login page for a waiting for contact details member
 */
	public function IAmOnTheMemberSetupLoginPageForAWaitingForContactDetailsMember() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		return $this->__iAmOnTheMemberSetupLoginPageForAMemberWithStatus($memberContext::STATUS_PRE_MEMBER_1);
	}

/**
 *
 * @When I am on the member setup login page for a waiting for contact detail approval member
 */
	public function IAmOnTheMemberSetupLoginPageForAWaitingForContactDetailApprovalMember() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		return $this->__iAmOnTheMemberSetupLoginPageForAMemberWithStatus($memberContext::STATUS_PRE_MEMBER_2);
	}

/**
 *
 * @When I am on the member setup login page for a waiting for payment member
 */
	public function IAmOnTheMemberSetupLoginPageForAWaitingForPaymentMember() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		return $this->__iAmOnTheMemberSetupLoginPageForAMemberWithStatus($memberContext::STATUS_PRE_MEMBER_3);
	}

/**
 *
 * @When I am on the member setup login page for a current member
 */
	public function IAmOnTheMemberSetupLoginPageForACurrentMember() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		return $this->__iAmOnTheMemberSetupLoginPageForAMemberWithStatus($memberContext::STATUS_CURRENT_MEMBER);
	}

/**
 *
 * @When I am on the member setup login page for an ex member
 */
	public function IAmOnTheMemberSetupLoginPageForAnExMember() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		return $this->__iAmOnTheMemberSetupLoginPageForAMemberWithStatus($memberContext::STATUS_EX_MEMBER);
	}

/**
 * 
 * @when I fill in the setup login form
 */
	public function IFillInTheSetupLoginForm() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		$username = $memberContext->getNewUsername();
		$email = $memberContext->getTestMemberData('email');
		$password = 'hunter2';
		return $this->__fillInTheSetupLoginForm($username, $email, $password, $password);
	}

/**
 * 
 * @when I fill in the setup login form with the incorrect e-mail
 */
	public function IFillInTheSetupLoginFormWithTheIncorrectEmail() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		$username = $memberContext->getNewUsername();
		$email = $memberContext->getNewEmail();
		$password = 'hunter2';
		return $this->__fillInTheSetupLoginForm($username, $email, $password, $password);
	}

/**
 * 
 * @when I fill in the setup login form with invalid details
 */
	public function IFillInTheSetupLoginFormWithInvalidDetails() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		$username = $memberContext->getNewUsername();
		$email = $memberContext->getNewEmail();
		$password = 'hunter2';
		$passwordConfirm = 'notTheSame';
		return $this->__fillInTheSetupLoginForm($username, $email, $password, $passwordConfirm);
	}

/**
 * 
 * @when I submit the setup login form
 */
	public function iSubmitTheSetupLoginForm() {
		return new When('I press "Create"');
	}

/**
 * 
 * @Then I should see the login details set message
 */
	public function iShouldSeeTheLoginDetailsSetMessage() {
		return new Then('I should see "Username and Password set, please login."');
	}

/**
 * 
 * @Then I should see the failed to set login details message
 */
	public function iShouldSeeTheFailedToSetLoginDetailsMessage() {
		return new Then('I should see "Unable to set username and password."');
	}
}