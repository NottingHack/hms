<?php

require_once ('HmsContext.php');

class EmailContext extends HmsContext {

/**
 * Get the path to the email dir.
 * @return string Path to the email dir.
 */
	private function __getEmailDir() {
		return $this->_testDir() . 'HMS_Emails/';
	}

	public function beforeScenario() {
		$emailDir = $this->__getEmailDir();
		$this->_configContext()->iSetTheEmailDebugDirectoryTo($emailDir);
		$this->_fileContext()->recursiveDelete($emailDir);
		mkdir($emailDir);
	}

	private function __checkForEmail($subject, $to, $from, $messageCheck) {
		$emailDir = $this->__getEmailDir();
		foreach (glob("$emailDir*.js") as $filename) {
			$email = json_decode(file_get_contents($filename), true);
			if ($email['headers']['Subject'] == $subject &&
				$email['headers']['To'] == $to &&
				$email['headers']['From'] == $from ) {

				$message = $email['message'];
				// Always check for errors
				if (strpos($message, 'cake-error') !== false) {
					return false;
				}

				return $messageCheck($message);
			}
		}

		return false;
	}

/**
 * @Then /^the new member should receive the welcome email$/
 */
	public function theNewMemberShouldReceiveTheWelcomeEmail() {
		$newMemberEmail = $this->_memberContext()->getTestMemberData('email');
		$result = $this->__checkForEmail(
			'Welcome to Nottingham Hackspace',
			$newMemberEmail,
			'Nottinghack Membership <membership@nottinghack.org.uk>',
			function($message) {
				return preg_match('/\/members\/setupLogin\/(\d)/', $message) === 1;
			}
		);
		assertTrue($result, 'No e-mail found');
	}

/**
 * @Given /^the member admins should receive the new member e-mail$/
 */
	public function theMemberAdminsShouldReceiveTheNewMemberEMail() {
		$newMemberEmail = $this->_memberContext()->getTestMemberData('email');
		$result = $this->__checkForEmail(
			'New Prospective Member Notification',
			"LorindaRSchroeder@gustr.com, NatashaRichards@armyspy.com, MichaelForbes@dayrep.com, JefferyCRoberts@teleworm.us, CailynMiller@armyspy.com, JamesRKnowlton@cuvox.de, MichelleNHernandez@gustr.com, EllisSmith@teleworm.us, KristenBTrapp@einrot.com, BenStone@cuvox.de, AustinMacleod@armyspy.com, BrianCDurbin@teleworm.us, EllisMoore@teleworm.us, BenjaminMarshall@teleworm.us, JemmaWatson@dayrep.com, FinleyGray@einrot.com, LucyGriffiths@gustr.com, AlexaMcIntyre@einrot.com, IsaacEvans@einrot.com, PaytonMacleod@gustr.com, LawrenceBurns@superrito.com, ConnieTYoung@dayrep.com, WarrenMSours@superrito.com, EllaNBlauvelt@cuvox.de, GeraldineRLewin@dayrep.com, ErinManning@einrot.com, RuairidhDuncan@superrito.com, RobertLDupuis@gustr.com, OlivierMcIntyre@gustr.com, MatildaBaker@cuvox.de, pyroka@gmail.com",
			'Nottinghack Membership <membership@nottinghack.org.uk>',
			function($message) use($newMemberEmail) {
				$regexFriendlyEmail = preg_quote($newMemberEmail, '/');
				return preg_match("/Someone with the e-mail $regexFriendlyEmail/", $message) === 1;
			}
		);
		assertTrue($result, 'No e-mail found');
	}

}