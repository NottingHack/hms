<?php

require_once (__DIR__ . '/../HmsContext.php');

use Behat\Behat\Context\Step\Given;
use Behat\Behat\Context\Step\Then;
use Behat\Behat\Context\Step\When;

class MemberRegisterPageContext extends HmsContext {

	private function __registerWithEmail($email) {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		$memberContext->setTestMemberData('email', $email);
		return new When('I fill in "MemberEmail" with "' . $email . '"');
	}

	private function __registerWithEmailThatBelongsToMemberWithStatus($status) {
		$databaseContext = $this->getMainContext()->getSubcontext('database');
		$email = $databaseContext->getEmailForMemberWithStatus($status);
		return $this->__registerWithEmail($email);
	}

/**
 * 
 * @Given I am on the member register page
 */
	public function iAmOnTheMemberRegisterPage() {
		return new Then('I should be on "/members/register"');
	}

/**
 * 
 * @when I fill in the register form
 */
	public function iFillInTheRegisterForm() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		$email = $memberContext->getNewEmail();
		return $this->__registerWithEmail($email);
	}

/**
 * 
 * @when I fill in the register form with an invalid e-mail
 */
	public function iFillInTheRegisterFormWithAnInvalidEmail() {
		return $this->__registerWithEmail('invalid');
	}

/**
 * 
 * @when I fill in the register form with an e-mail that belongs to a prospective member
 */
	public function iFillInTheRegisterFormWithAnEmailThatBelongsToAProspectiveMember() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		return $this->__registerWithEmailThatBelongsToMemberWithStatus($memberContext::STATUS_PROSPECTIVE_MEMBER);
	}

/**
 * 
 * @when I fill in the register form with an e-mail that belongs to a waiting for contact details member
 */
	public function iFillInTheRegisterFormWithAnEmailThatBelongsToAWaitingForContactDetailsMember() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		return $this->__registerWithEmailThatBelongsToMemberWithStatus($memberContext::STATUS_PRE_MEMBER_1);
	}

/**
 * 
 * @when I fill in the register form with an e-mail that belongs to a waiting for contact detail approval member
 */
	public function iFillInTheRegisterFormWithAnEmailThatBelongsToAWaitingForContactDetailApprovalMember() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		return $this->__registerWithEmailThatBelongsToMemberWithStatus($memberContext::STATUS_PRE_MEMBER_2);
	}

/**
 * 
 * @when I fill in the register form with an e-mail that belongs to a waiting for payment member
 */
	public function iFillInTheRegisterFormWithAnEmailBelongsToAWaitingForPaymentMember() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		return $this->__registerWithEmailThatBelongsToMemberWithStatus($memberContext::STATUS_PRE_MEMBER_3);
	}

/**
 * 
 * @when I fill in the register form with an e-mail that belongs to a current member
 */
	public function iFillInTheRegisterFormWithAnEmailBelongsToACurrentMember() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		return $this->__registerWithEmailThatBelongsToMemberWithStatus($memberContext::STATUS_CURRENT_MEMBER);
	}

/**
 * 
 * @when I fill in the register form with an e-mail that belongs to an ex member
 */
	public function iFillInTheRegisterFormWithAnEmailBelongsToAnExMember() {
		$memberContext = $this->getMainContext()->getSubcontext('member');
		return $this->__registerWithEmailThatBelongsToMemberWithStatus($memberContext::STATUS_EX_MEMBER);
	}

/**
 * 
 * @when I submit the register form
 */
	public function iSubmitTheRegisterForm() {
		return new When('I press "Register"');
	}

/**
 * 
 * @Then I should see the registration successful message
 */
	public function iShouldSeeTheRegistrationSuccessfulMessage() {
		return new Then('I should see "Registration successful, please check your inbox."');
	}

/**
 * 
 * @Then I should see the user already registered message
 */
	public function iShouldSeeTheUserAlreadyRegisteredMessage() {
		return new Then('I should see "User with that e-mail already exists."');
	}
}