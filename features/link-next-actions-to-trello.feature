Feature: Link Next Actions to Trello
  In order to decide on my next action
  As a registered user
  I need to be able to quickly get more information on the Next Actions in my list

  Scenario: Click to view Trello card
    Given I have a card "Improve usability" on my Next Actions list
    When I click on "Improve usability"
    Then I should be taken to "Improve usability" on Trello

  Scenario: See project name
    Given I have a project "Improve usability"
    And "Improve usability" has a Todo card "Show project name"
    When I am on "/actions"
    Then I should see the project "Improve usability"