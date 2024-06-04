Feature: index page
	In order work with the hgit app
	As an authorised user or a guest
	I need to be able to access the index as a guest and login as a user

	Background:
		Given the following local auth user entries exist:
			| username | password | permissions |
			| user     | user     | HGIT_USE    |

	Scenario: Logging into the page as a normal user
		Given I am on the homepage
		Then the response status code should be 200
		And I should see "Login"
		When I am authenticated as "user"
		And follow "Login"
		Then I should be on the homepage
		And the response status code should be 200
		And I should see "Logout"
		And I should not see "Login"
