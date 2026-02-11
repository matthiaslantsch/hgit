Feature: Managing GIT API tokens
	In order work with the hgit app
	As an authorised user
	I need to be able to create and remove API tokens (for the git client)

	Background:
		Given I am logged with the default user

	Scenario: Listing and revoking existing tokens
		Given the following token entries exist:
			| name         | hash | expiry     | permissions | idUser |
			| Test Token 1 | 1    | 30.12.2050 | 0		     | 1      |
			| Test Token 2 | 2    | 30.12.2050 | 0		     | 1      |
		And I am on the homepage
		When I follow "Tokens"
		Then I should be on "/tokens"
		And I should see a "tokens" table with 2 elements
		And I should see "Test Token 1"
		And I should see "Test Token 2"
		And I should see "2050-12-29"
		When I click "Delete" in the "tokens" table row "Test Token 1"
		Then I should be on "/tokens"
		And I should see "Token revoked successfully"
		And I should see a "tokens" table with 1 element
		But I should not see "Test Token 1"

	Scenario: Creating a new token
		Given I am on the homepage
		When I follow "Tokens"
		Then I should be on "/tokens"
		When I fill in "Name" with "Test Token 3"
		And I fill in "Expiry" with "30.11.2050"
		And I press "Create"
		Then I should be on "/tokens"
		And I should see "Token created successfully"
		And I should see "Copy it now!"
		When I follow "Back"
		Then I should be on "/tokens"
		And I should see a "tokens" table with 1 element
		And I should see "Test Token 3"
		And I should see "2050-11-29"
