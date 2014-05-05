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
				return true;//preg_match('/\/members\/setupLogin\/(\d)/', $message) === 1;
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
 * @Then the following e-mails are sent
 */
	public function checkEmailsSent(TableNode $emailTable) {
		$allEmails = $this->__getAllEmails();

		$expectedEmailTypes = array_map(function($row) {
			return $row['emailType'];
		}, $emailTable->getHash());

		$foundEmailTypes = array();

		$numEmailTypes = count($expectedEmailTypes);
		for ($i = 0; $i < $numEmailTypes; $i++) {
			$emailType = $expectedEmailTypes[$i];

			// If we find an e-mail that matches, remove it from the list
			$numEmails = count($allEmails);
			for ($j = 0; $j < $numEmails; $j++) {
				$emailData = $allEmails[$j];

				if ($this->__checkEmailType($emailType, $emailData)) {

					if ($this->__doesEmailHaveError($emailData)) {
						$this->_fail('Found matching e-mail for: ' . $emailType . ' but it contained an error.');
						return;
					}

					array_splice($allEmails, $j, 1);
					array_push($foundEmailTypes, $emailType);
					break;
				}
			}
		}

		$failMessage = '';
		if (count($allEmails) > 0) {
			$emailSubjects = array_map(function($email) {
				return $email['headers']['Subject'];
			}, $allEmails);
			$failMessage .= 'Found more e-mails than expected: ' . join(', ', $emailSubjects) . PHP_EOL;
		}

		if (count($foundEmailTypes) < count($expectedEmailTypes)) {

			$notFoundTypes = array();
			foreach ($expectedEmailTypes as $type) {
				if (array_search($type, $foundEmailTypes) === false) {
					array_push($notFoundTypes, $type);
				}
			}

			$failMessage .= 'Failed to find the following e-mails: ' . join(', ', $notFoundTypes);
		}

		if ($failMessage != '') {
			$this->_fail($failMessage);
		}
	}
}