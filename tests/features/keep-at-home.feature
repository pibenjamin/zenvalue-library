Feature: Users can contribute books without dropping them off at the office

    Scenario: User can indicate that he wants to keep a book at home
      When I go to the catalogue page
      And I click on the "Ajouter un de mes livres au catalogue" button
      Then I should see a checkbox "Je souhaite conserver le livre chez moi"
      And I should see a success message "Conditions d'utilisation"


    Background: A user has filled the contribution form with ISBN "9781942788812" and accepted the conditions of use

    Scenario: A user can distinguish between a book kept at home and a book dropped off at the office
      Given I go to the catalogue page
      When I look at the row of the book with ISBN "9781942788812"
      Then I should see a column "Statut" with the value "À la maison"

