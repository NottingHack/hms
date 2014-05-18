Feature: SetupLogin
	After registering, prospective members
	should be able to set-up a HMS login

	Scenario: Setup login for prospective member
		Given I am on the member setup login page for a prospective member
		When I fill in the setup login form
		And I submit the setup login form
		Then I am on the member login page
		And I should see the login details set message

	Scenario: Attempt to setup login but enter incorrect e-mail
		Given I am on the member setup login page for a prospective member
		When I fill in the setup login form with the incorrect e-mail
		And I submit the setup login form
		Then I am on the member setup login page
		And I should see the failed to set login details message

	Scenario: Attempt to setup login but enter invalid details
		Given I am on the member setup login page for a prospective member
		When I fill in the setup login form with invalid details
		And I submit the setup login form
		Then I am on the member setup login page

	Scenario Outline:
		Given I am on the member setup login page for <status>
		Then I am on the homepage

		Examples:
			| status                                       |
			| a waiting for contact details member         |
			| a waiting for contact detail approval member |
			| a waiting for payment member                 |
			| a current member                             |
			| an ex member                                 |