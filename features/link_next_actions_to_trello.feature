Feature: Link Next Actions to Trello
  In order to decide on my next action
  As a registered user
  I need to be able to quickly get more information on the Next Actions in my list

  Scenario: Click to view Trello card
    Given I have a card "Improve usability" on my Next Actions list
    When I click on "Improve usability"
    Then I should be taken to "Improve usability" on Trello

  Scenario: See project name for project Next Actions
    Given I have a project "Improve usability" with a Todo card "Show project name"
    When I view my Next Actions list
    Then I should see the Project "Improve usability"
    And I should see a Next Action "Show project name"

  Scenario: See board name for Next Actions I am a member of
    Given I am a member of the card "See board name" on the board "Improve usability"
    When I view my Next Actions list
    Then I should see the Project "Improve usability"
    And I should see a Next Action "See board name"
