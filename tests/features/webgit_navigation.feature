Feature: Webgit navigation
	In order work with the hgit web git interface
	As a user with granted access (anonymous, logged in or a member of the project team)
	I should be able to follow the web git interface structure to inspect the repository

	Background:
		Given I am logged with the default user
		And the following project exists:
			| name         | slug         |
			| Test Project | test-project |

	Scenario: Checking web git pages
		Given I am on the homepage
		When I follow "Git Repository"
		Then I should be on "/git/test-project/test-project.git/"
		And I should see "Clone URL"
		And I should see "Branches"
		When I follow "Commits"
		Then I should be on "/git/test-project/test-project.git/log/master"
		And I should see "Create .gitignore file in master branch"
		When I follow "Tags"
		Then I should be on "/git/test-project/test-project.git/tags"

