<?php

require_once ('HmsContext.php');

use Behat\Gherkin\Node\TableNode;

class EmailContext extends HmsContext {

	private $__emailsToCheckFunctions = array(
		'new_member_welcome_email' => '__checkNewMemberWelcomeEmail',
		'new_member_member_admin_email' => '__checkNewMemberMemberAdminEmail',
	);

	private function __checkNewMemberWelcomeEmail($emailData) {
		$newMemberEmail = $this->_memberContext()->getTestMemberData('email');
		return $this->__checkEmail(
			$emailData,
			'Welcome to Nottingham Hackspace',
			$newMemberEmail,
			$this->__getFromAddress(),
			function($message) {
				return preg_match('/\/members\/setupLogin\/(\d)/', $message) === 1;
			}
		);
	}

	private function __checkNewMemberMemberAdminEmail($emailData) {
		$newMemberEmail = $this->_memberContext()->getTestMemberData('email');
		$memberAdminEmailAddress = $this->_databaseContext()->getMemberAdminEmailAddresses();
		return $this->__checkEmail(
			$emailData,
			'New Prospective Member Notification',
			join(', ', $memberAdminEmailAddress),
			$this->__getFromAddress(),
			function($message) use($newMemberEmail) {
				$regexFriendlyEmail = preg_quote($newMemberEmail, '/');
				return preg_match("/Someone with the e-mail $regexFriendlyEmail/", $message) === 1;
			}
		);
	}

/**
 * Get the path to the email dir.
 * @return string Path to the email dir.
 */
	private function __getEmailDir() {
		return $this->_testDir() . 'HMS_Emails/';
	}

	private function __getFromAddress() {
		return 'Nottinghack Membership <membership@nottinghack.org.uk>';
	}

	public function beforeScenario() {
		$emailDir = $this->__getEmailDir();
		$this->_configContext()->iSetTheEmailDebugDirectoryTo($emailDir);
		$this->_fileContext()->recursiveDelete($emailDir);
		mkdir($emailDir);
	}

	private function __getAllEmails() {
		$allEmails = array();
		$emailDir = $this->__getEmailDir();
		foreach (glob("$emailDir*.js") as $filename) {
			$email = json_decode(file_get_contents($filename), true);
			array_push($allEmails, $email);
		}
		return $allEmails;
	}

	private function __checkEmailType($emailType, $emailData) {
		if (array_key_exists($emailType, $this->__emailsToCheckFunctions)) {
			// This will attempt to call the function.
			// PHP is 'interesting'
			$checkFunctionName = $this->__emailsToCheckFunctions[$emailType];
			return $this->$checkFunctionName($emailData);
		}

		$this->_fail('Unknown e-mail type: ' . $emailType);
	}

	private function __doesEmailHaveError($emailData) {
		$message = $emailData['message'];
		return strpos($message, 'cake-error') !== false;
	}

	private function __checkEmail($email, $subject, $to, $from, $messageCheck) {
		if ($email['headers']['Subject'] == $subject &&
			$email['headers']['To'] == $to &&
			$email['headers']['From'] == $from ) {
			$message = $email['message'];
			return $messageCheck($message);
		}
		return false;
	}

/**
 * @Then no e-mails are sent
 */
	public function noEmailsAreSent() {
		$allEmails = $this->__getAllEmails();
		if (count($allEmails) > 0) {
			$emailSubjects = $this->__getSubjectsFromEmails($allEmails);
			$this->__fail('The following e-mails were sent unexpectedly: ' . join(', ', $emailSubjects));
		}
	}

/**
 * @Then the following e-mails are sent
 */
	public function theFollowingEmailsAreSent(TableNode $emailTable) {
		$expectedEmailTypes = $this->__getEmailTypesFromTable($emailTable);

		$allEmails = $this->__getAllEmails();
		$notFoundEmailTypes = $expectedEmailTypes;

		// Try to find an email that matches each of the expected types
		foreach ($expectedEmailTypes as $emailType) {
			$emailIdx = $this->__findEmailOfType($emailType, $allEmails);
			if ($emailIdx >= 0) {

				// Remove it from the list of e-mails to check next time
				array_splice($allEmails, $emailIdx, 1);

				// Also want to remove it from the list of 'not found'
				// email types
				$notFoundIdx = array_search($emailType, $notFoundEmailTypes, true);
				if ($notFoundIdx !== false) {
					array_splice($notFoundEmailTypes, $notFoundIdx, 1);
				}
			}
		}

		$failMessage = '';

		// Did we get more e-mails than we were expecting?
		$unexpectedEmails = $allEmails;
		if (count($unexpectedEmails) > 0) {
			$emailSubjects = $this->__getSubjectsFromEmails($unexpectedEmails);
			$failMessage .= 'Found more e-mails than expected: ' . join(', ', $emailSubjects) . PHP_EOL;
		}

		// Did we not get some e-mails we were expecting
		if (count($notFoundEmailTypes) > 0) {
			$failMessage .= 'Failed to find the following e-mails: ' . join(', ', $notFoundEmailTypes);
		}

		if ($failMessage != '') {
			$this->_fail($failMessage);
		}
	}

	private function __getEmailTypesFromTable(TableNode $emailTable) {
		return array_map(function($row) {
			return $row['emailType'];
		}, $emailTable->getHash());
	}

	private function __findEmailOfType($emailType, $allEmails) {
		$numEmails = count($allEmails);
		for ($i = 0; $i < $numEmails; $i++) {
			if ($this->__checkEmailType($emailType, $allEmails[$i])) {

				// We've found the e-mail, take this opportunity to
				// check if the e-mail contains an error
				if ($this->__doesEmailHaveError($allEmails[$i])) {
					$this->_fail('Found matching e-mail for: ' . $emailType . ' but it contained an error.');
					return -1;
				}

				return $i;
			}
		}

		return -1;
	}

	private function __getSubjectsFromEmails($allEmails) {
		return array_map(
			function($email) {
				return $email['headers']['Subject'];
			},
			$allEmails);
	}
}