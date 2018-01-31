Feature: See the due date of cards on Trello
  In order to decide on my next action
  As a registered user
  I need to see the date of any card that has a due date

  Scenario: See due date of card
    Given I have a card "See due date" on my Next Actions list with a due date of "01/02/2018"
    When I view my Next Actions list
    Then I should see a Next Action "See due date"
    And "See due date" should have a date of "01/02/2018"

  Scenario: Highlight overdue cards
    Given I have a card "See due date" on my Next Actions list with a due date of "01/01/2000"
    When I view my Next Actions list
    Then I should see a Next Action "See due date"
    And "See due date" should have a date of "01/01/2000"
    And "See due date" should be overdue
