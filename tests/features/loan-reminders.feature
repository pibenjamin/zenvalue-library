Feature: Loan Reminder Notifications
  As a library system
  I want to send automatic reminders for loans
  So that books are returned on time

  Background:
    Given the system has configured reminder settings
    And the loan duration is set to 30 days
    And there are active loans in the system

  Scenario: First reminder before due date
    Given a loan is due in 3 days
    And the loan was created 27 days ago
    When the reminder system runs
    Then a first reminder should be sent to the borrower
    And the reminder should contain the number of days until due
    And the system should log "Sent first reminder for loan #"

  Scenario: Recurring reminders for overdue loans
    Given a loan is 5 days overdue
    And the loan was created 35 days ago
    And no reminder has been sent in the last 7 days
    When the reminder system runs
    Then a recurring reminder should be sent to the borrower
    And the reminder should contain the number of days overdue
    And the system should log "Sent recurring reminder for loan #"

  Scenario: Urgent notifications for severely overdue loans
    Given a loan is overdue by more than 30 days
    And the loan was created 60 days ago
    When the reminder system runs
    Then an urgent notification should be sent to all librarians
    And each librarian should receive the notification
    And the system should log "Sent urgent notification for loan # to librarians"

  Scenario: No reminders for returned books
    Given a loan was returned yesterday
    And the loan duration was respected
    When the reminder system runs
    Then no reminders should be sent for this loan

  Scenario: Multiple reminders for different loans
    Given there are 3 loans due in 3 days
    And there are 2 loans overdue by 5 days
    And there is 1 loan overdue by 31 days
    And all loans were created within their respective timeframes
    When the reminder system runs
    Then 3 first reminders should be sent
    And 2 recurring reminders should be sent
    And 1 urgent notification should be sent to librarians 