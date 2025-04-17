Feature: Super Admin Management of parcours
  The actions the super admin can do to set up and manage the parcours

  Background:
    Given I am a user with the role "super_admin"

  Scenario: Parcours creation
    When I visit the parcours page
    And I click on the "Create" button
    And I fill in the "name" field with "Parcours 1"
    And I fill in the "description" field with "Description du parcours 1"
    And I click on the "Create" button
    Then I see the new parcours in the list

  Scenario: Add books to parcours
    When I visit the parcours page
    And I click on the "Add books" button
    Then I see a model with a filterable multiple select input for books

