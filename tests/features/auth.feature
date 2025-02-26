Feature: User Authentication
  As a Zenvalue employee
  I want to log in to the application
  So that I can access my dashboards

  Background:
    Given the following user exists:
      | email                  | password    | name          |
      | jdupont@zenvalue.fr   | Pass@word1  | Jean Dupont   |

  Scenario: Successful login with Zenvalue email
    Given I am on the login page
    When I fill in the login form with the following details:
      | email                | password   |
      | jdupont@zenvalue.fr | Pass@word1 |
    And I click on the "Se connecter" button
    Then I should be redirected to the dashboard page
    And I should see " Tableau de bord " title
    And I should see the following menu items:
      | Tableau de bord |
      | Auteurs    |
      | Ouvrages     |
      | Mots-clés     |
      | Mes prêts     |

  Scenario: Login attempt with non-Zenvalue email
    Given I am on the login page
    When I fill in the login form with the following details:
      | email               | password   |
      | user@example.com   | Pass@word1 |
    And I click on the "Se connecter" button
    Then I should see an error message "Seules les adresses email @zenvalue.fr sont autorisées"
    And I should remain on the login page
