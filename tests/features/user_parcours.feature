Feature: User Management of parcours
  The actions the user can do to manage their parcours

  Background:
    Given I am a user with the role "user"

  Scenario: Parcours listing
    When I visit the parcours page
    Then I see the list of different parcours

  Scenario: Parcours subscription description
    When I visit the parcours page
    And I click on a parcours row
    Then I see the parcours details

  Scenario: User subscribe to a parcours
    When I visit the parcours details page
    And I click on the "Subscribe" button
    Then I see the "Subscribe" button is disabled
    And I see the "Unsubscribe" button is enabled
    And I see the a notification "You are now subscribed to this parcours"

  Scenario: User unsubscribe to a parcours
    When I visit the parcours details page
    And I click on the "Unsubscribe" button
    Then I see the "Subscribe" button is enabled
    And I see the "Unsubscribe" button is disabled
    And I see the a notification "You are now unsubscribed to this parcours"

  Scenario: My Parcours dashboard
    When I connect to the app
    And I subscribed to a parcours
    Then I see the "My Parcours" dashboard


  Scenario: User can see the other employees subscribed to the same parcours
    When I visit the parcours details page
    Then I see the list of other employees subscribed to the same parcours  

  Scenario: Reading suggestions

