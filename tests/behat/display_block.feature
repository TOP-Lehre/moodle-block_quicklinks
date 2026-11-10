@block @block_quicklinks
Feature: Add and remove quick links block in courses
  As a teacher I want to add and remove the quick links block in my course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  Scenario: Teacher adds the quick links block to a course
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Quick Links" block
    Then I should see "Quick Links" in the "Quick Links" "block"

  Scenario: Quick links are displayed in the block
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Quick Links" in site administration
    And I click on "Add link" "button"
    And I set the following fields to these values:
      | Label      | Moodle                |
	  | Source type  | Manual URL          |
      | URL | https://moodle.org/   |
	  | Logo |  |
      | Sort order | 0                           |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Quick Links" block
    Then I should see "Moodle" in the "Quick Links" "block"

  Scenario: Student can see the quick links block
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Quick Links" in site administration
    And I click on "Add link" "button"
    And I set the following fields to these values:
      | Label      | Moodle                |
	  | Source type  | Manual URL          |
      | URL | https://moodle.org/   |
	  | Logo |  |
      | Sort order | 0 
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Quick Links" block
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Moodle" in the "Quick Links" "block"

  Scenario: Teacher removes the quick links block from a course
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Quick Links" block
    And I should see "Quick Links" in the "Quick Links" "block"
    When I open the "Quick Links" blocks action menu
    And I click on "Delete Quick Links block" "link"
    And I press "Yes"
    Then I should not see "Quick Links" in the ".block-region" "css_element"

  Scenario: Student cannot add or remove blocks
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Quick Links" block
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Quick Links" in the "Quick Links" "block"
    And I should not see "Actions menu" in the "Quick Links" "block"
    