Feature: Access control tests
	In order work with the hgit app
	As a user (anonymous, logged in or a member of the project team)
	I should be able to see and access projects for which I was granted access to

	Background:
		Given the local auth database contains a user other with password other
		And the local auth database contains a user user with password user

	Scenario: Access control on project listing, detail page and web git (READCODE)
		Given the following projects exist:
			| name     | slug     | publicPermissions | internalPermissions | maintainer |
			| Public   | public   | READCODE          |                     | other      |
			| Internal | internal |                   | READCODE            | other      |
			| Personal | personal |                   |                     | other      |
		When I am on the homepage
		Then there should be a "Public" anchor
		But I should not see "Internal"
		And I should not see "Personal"
		When I follow "Public"
		Then I should be on "/projects/public"
		Then print last response
		Then print the application log
		And there should be a "Git Repository" anchor that links to "/git/public/public.git/"
		When I follow "Git Repository"
		Then I should be on "/git/public/public.git/"
		And I should see "Branches"
		And I should see "develop"
		And I should see "master"
		When I go to "/projects/internal"
		Then the response status code should be 401
		When I go to "/projects/personal"
		Then the response status code should be 401
		Given I am authenticated as user
		And I am on the homepage
		Then there should be a "Public" anchor
		And there should be a "Internal" anchor
		But I should not see "Personal"
		When I follow "Public"
		Then I should be on "/projects/public"
		And there should be a "Git Repository" anchor that links to "/git/public/public.git/"
		When I follow "Git Repository"
		Then I should be on "/git/public/public.git/"
		And I should see "Branches"
		And I should see "develop"
		And I should see "master"
		Given I am on the homepage
		When I follow "Internal"
		Then I should be on "/projects/internal"
		And there should be a "Git Repository" anchor that links to "/git/internal/internal.git/"
		When I follow "Git Repository"
		Then I should be on "/git/internal/internal.git/"
		And I should see "Branches"
		And I should see "develop"
		And I should see "master"
		When I go to "/projects/personal"
		Then the response status code should be 403
		Given I am authenticated as other
		And I am on the homepage
		Then there should be a "Public" anchor
		And there should be a "Internal" anchor
		And there should be a "Personal" anchor
		When I follow "Public"
		Then I should be on "/projects/public"
		And there should be a "Git Repository" anchor that links to "/git/public/public.git/"
		When I follow "Git Repository"
		Then I should be on "/git/public/public.git/"
		And I should see "Branches"
		And I should see "develop"
		And I should see "master"
		Given I am on the homepage
		Then there should be a "Git Repository" anchor that links to "/git/public/public.git/"
		When I follow "Internal"
		Then I should be on "/projects/internal"
		And there should be a "Git Repository" anchor that links to "/git/internal/internal.git/"
		When I follow "Git Repository"
		Then I should be on "/git/internal/internal.git/"
		And I should see "Branches"
		And I should see "develop"
		And I should see "master"
		Given I am on the homepage
		When I follow "Personal"
		Then I should be on "/projects/personal"
		And there should be a "Git Repository" anchor that links to "/git/personal/personal.git/"
		When I follow "Git Repository"
		Then I should be on "/git/personal/personal.git/"
		And I should see "Branches"
		And I should see "develop"
		And I should see "master"

	Scenario: Access control on project edit (ADMIN)
		Given the following projects exist:
			| name     | slug     | publicPermissions | internalPermissions | maintainer |
			| InteRO   | intero   |                   | READCODE            | other      |
			| Internal | internal |                   | ADMIN               | other      |
			| Personal | personal |                   |                     | other      |
		And I am authenticated as user
		And I am on the homepage
		When I follow "InteRO"
		Then I should be on "/projects/intero"
		And I should see "Project type: other"
		But I should not see "Edit project specification"
		When I go to "/projects/intero/edit"
		Then the response status code should be 403
		Given I am on the homepage
		And I follow "Internal"
		Then I should be on "/projects/internal"
		And I should see "Edit project specification"
		When I follow "Edit project specification"
		Then I should be on "/projects/internal/edit"
		And the response status code should be 200
		When I fill in "Description" with "test"
		And I press "Submit"
		Then I should be on "/projects/internal"
		And I should see "Project updated successfully"
		When I go to "/projects/personal/edit"
		Then the response status code should be 403
		Given I am authenticated as other
		And I am on the homepage
		When I follow "InteRO"
		Then I should be on "/projects/intero"
		And I should see "Project type: other"
		And I should see "Edit project specification"
		When I follow "Edit project specification"
		Then I should be on "/projects/intero/edit"
		And the response status code should be 200
		Given I am on the homepage
		And I follow "Internal"
		Then I should be on "/projects/internal"
		When I follow "Edit project specification"
		Then I should be on "/projects/internal/edit"
		And the response status code should be 200
		When I fill in "Description" with "test"
		And I press "Submit"
		Then I should be on "/projects/internal"
		And I should see "Project updated successfully"
		Given I am on the homepage
		When I follow "Personal"
		Then I should be on "/projects/personal"
		And I should see "Project type: other"
		And I should see "Edit project specification"
		When I follow "Edit project specification"
		Then I should be on "/projects/personal/edit"
		And the response status code should be 200
