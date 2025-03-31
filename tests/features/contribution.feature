Feature: Book Contribution Management

  Rule: Users can contribute books that need to be qualified by admins

    Background:
      Given I am logged in as a user

    Scenario: Contributing a new book to the library
      When I go to the catalogue page
      And I click on the "Ajouter un de mes livres au catalogue" button
      And I fill in the contribution form with ISBN "9781942788812"
      And I submit the contribution form
      Then I should see a success message "Livre en cours d'ajout au catalogue"
      And a new book record should be created with:
        | Field        | Value                 |
        | status       | contribution_to_qualify|
        | owner_id     | authenticated_user     |
        | support_id   | 1                     |
        | ISBN         | 9781942788812         |

      Given I am logged in as a super_admin
      When I go to the admin catalogue page "admin/book-admins"
      And I click on the "Livres à qualifier" tab button
      Then I should see a record in the catalogue list with the field isbn "9782070423528"

    Scenario: Attempting to contribute an existing book
      When I go to the catalogue page
      And I click on the "Ajouter un de mes livres au catalogue" button
      And I fill in the contribution form with ISBN "9781942788812"
      And the ISBN already exists in the catalogue
      And I submit the contribution form
      Then I should see an error message "Ce livre existe déjà dans notre catalogue"

    Scenario: Dropping off a book at the office
      Given I a user
      And I have registered a book with ISBN "9781942788812"
      When I drop off the book at the office
      Then I can click on dropped button on /admin/catalogue
      And I should receive a confirmation notification
      And the admin should be notified of a new book drop-off



Vérifier que le livre en cours d'ajout : (ISBN search)

- N'existe pas déjà sur étagère ou dans un autre status
- N'est pas dans une proposition d'achat
- Gérer une sorte d'appel à la decision


Utilisateur dépose le livre au bureau


Admin valide le libre :
- le livre est présent
- un qr code est présent dans le livre
- le livre est déplacé dans les étagères

Utilisateur reçoit un message indiquant que son livre est disponible à l'emprunt

Utilisateur peut voir le status d'avancement du dépot (étapes X sur Y)
