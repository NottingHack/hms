Feature: Registration
	To cope with a growing userbase
	It should be possible to automate most of the sign-up proceedure
	Through HMS

Scenario: Register from homepage
	Given I am in the hackspace
	And I am on the homepage
	When I click the register link on the home page
	And I should be on "/members/register"
	And I fill in the register form
	And I submit the register form
	Then I should be on the homepage
	And I should see the registration successful message
	And the new member should receive the welcome email
	And the member admins should receive the new member e-mail