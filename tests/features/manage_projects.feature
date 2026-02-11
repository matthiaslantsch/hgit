Feature: Managing projects
	In order work with the hgit app
	As an authorised user
	I need to be able create, edit and view projects

	Background:
		Given I am logged with the default user

	Scenario: Creating a new project
		Given the following project exists:
			| name         | slug         |
			| Test Project | test-project |
		When I am on the homepage
		Then I should see "Quick Links"
		And I should see "Create a new project"
		When I follow "Create a new project"
		Then I should see "Create a new project"
		When I fill in "Name" with "Test Project"
		And I fill in "Description" with "This is a test project"
		And I select "internal" from "project_create[permissionPreset]"
		And I select "other" from "project_create[type]"
		And I press "Submit"
		Then I should be on "/projects/new"
		And I should see a form error "Choose a different name" on "Name"
		And the "Name" field should contain "Test Project"
		And the "Description" field should contain "This is a test project"
		When I fill in "Name" with "Test Project 2"
		And I press "Submit"
		Then I should be on "/projects/test-project-2"
		And I should see "Project created successfully"
		Given I am on the homepage
		When I follow "Test Project 2"
		Then I should be on "/projects/test-project-2"
		And the response status code should be 200
		And I should see "This is a test project"
		And I should see "Project type: other"
		And there should be a main git repo for the project "Test Project 2"

	Scenario: Editing a project
		Given the following project exists:
			| name           | slug         | description |
			| Test Project   | test-project | test        |
		When I am on the homepage
		And I follow "Test Project"
		Then I should be on "/projects/test-project"
		And I should see "Project type: other"
		When I follow "Edit"
		Then I should be on "/projects/test-project/edit"
		And the "Description" field should contain "test"
		When I fill in "Description" with "New Description"
		And I select "php app" from "project_update[type]"
		And I press "Submit"
		Then I should be on "/projects/test-project"
		And I should see "Project updated successfully"
		And I should see "New Description"
		And I should see "Project type: php app"
		But I should not see "Project type: other"
