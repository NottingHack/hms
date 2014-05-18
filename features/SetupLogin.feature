Feature: SetupLogin
	After registering, prospective members
	should be able to set-up a HMS login

	Scenario: Setup login for prospective member
		When I am on the member setup login page for a prospective member
		Given I fill in the setup login form
		And I submit the setup login form
		Then I am on the member login page
		And I should see the login details set message