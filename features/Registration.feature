Feature: Registration
	To cope with a growing userbase
	It should be possible to automate most of the sign-up proceedure
	Through HMS

Scenario: Register from homepage
	Given I am in the hackspace
	And I am on the homepage
	When I register with a new e-mail address
	Then the new member should receive the welcome email
	And the member admins should receive the new member e-mail