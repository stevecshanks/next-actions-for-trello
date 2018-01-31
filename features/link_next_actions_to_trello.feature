Feature: Link Next Actions to Trello
  In order to decide on my next action
  As a registered user
  I need to be able to quickly get more information on the Next Actions in my list

  Scenario: Click to view Trello card
    Given I have a card "Improve usability" on my Next Actions list
    When I view my Next Actions list
    And I click on "Improve usability"
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

  Scenario: See labels of card
    Given I have a card "Improve usability" on my Next Actions list with the label "Urgent"
    When I view my Next Actions list
    Then I should see a Next Action "Improve usability"
    And I should see "Urgent"

  Scenario: See first checklist item of card
    Given I have a card "See checklists" on my Next Actions list with a checklist containing "Write a failing test, Change API call"
    When I view my Next Actions list
    Then I should see a Next Action "See checklists"
    And I should see "Write a failing test"
    And I should not see "Change API call"
