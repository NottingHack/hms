Feature: Registration
	People should be able to register through the
	HMS website

	Background:
		Given I am in the hackspace
		And I go to the homepage
		And I click the register link on the home page
		And I am on the member register page

	Scenario: Register with new e-mail
		When I fill in the register form
		And I submit the register form
		Then I am on the homepage
		And I should see the registration successful message
		And the following e-mails are sent
			| emailType                     |
			| new_member_welcome_email      |
			| new_member_member_admin_email |

	Scenario: Register with invalid e-mail
		When I fill in the register form with an invalid e-mail
		And I submit the register form
		Then I am on the member register page
		And no e-mails are sent

	Scenario: Register when already a prospective member
		When I fill in the register form with an e-mail that belongs to a prospective member
		And I submit the register form
		Then I am on the homepage
		And I should see the registration successful message
		And the following e-mails are sent
			| emailType                     |
			| new_member_welcome_email      |

	Scenario Outline: Register with e-mail HMS knows about
		When I fill in the register form with an e-mail that belongs to <status>
		And I submit the register form
		Then I am on the member login page
		And no e-mails are sent
		And I should see the user already registered message

		Examples:
			| status                                       |
			| a waiting for contact details member         |
			| a waiting for contact detail approval member |
			| a waiting for payment member                 |
			| a current member                             |
			| an ex member                                 |

