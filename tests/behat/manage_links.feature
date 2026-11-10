@block @block_quicklinks
Feature: Manage quick links
  As an admin I want to manage quick links

  Background:
    Given I log in as "admin"

  Scenario: Add a new quick link
    Given I navigate to "Plugins > Blocks > Quick Links" in site administration
    When I click on "Add link" "button"
    And I set the following fields to these values:
      | Label      | Moodle                |
	  | Source type  | Manual URL          |
      | URL | https://moodle.org/   |
	  | Logo |  |
      | Sort order | 0                     |
    And I press "Save changes"
    Then I should see "Moodle"

  Scenario: Add a new quick link
    Given I navigate to "Plugins > Blocks > Quick Links" in site administration
    When I click on "Add link" "button"
    And I set the following fields to these values:
      | Label      | Moodle                  |
  	  | Source type  | Manual URL          |
      | URL | https://moodle.org/         |
	  | Logo |  |
      | Sort order | 1                                  |
    And I press "Save changes"
    Then I should see "Moodle"

   Scenario: Edit an existing quick link
    Given the following "block_quicklinks" exist:
      | Logo | label          | Source type | URL | Sort order | Visible | Actions |
      |  | Moodle | Manual URL | https://moodle.org | 1  | Visible | Edit |
    When I navigate to "Plugins > Blocks > Quick Links" in site administration
    And I click on "Edit" "link" in the "Moodle" "table_row"
    And I set the field "Label" to "Moodle Website"
    And I press "Save changes"
    Then I should see "Moodle Website"
    And I should not see "Moodle"

  Scenario: Delete a quick link
    Given the following "block_quicklinks" exist:
     | Logo | label          | Source type | URL | Sort order | Visible | Actions |
      |  | Moodle | Manual URL | https://moodle.org | 1  | Visible | Edit | 
    When I navigate to "Plugins > Blocks > Quick Links" in site administration
    And I click on "Delete" "link" in the "Moodle" "table_row"
    Then I should not see "Moodle"
    