Feature: See the cards at the top of each project todo list
  In order to decide on my next action
  As a registered user
  I need to be able to see the Next Action for each project

  Scenario: Single project with multiple Todo items
    Given I have a project "Next Actions for Trello"
    And "Next Actions for Trello" has a Todo card "Set up Behat"
    And "Next Actions for Trello" has a Todo card "Set up Bootstrap"
    When I am on "/actions"
    Then I should see a Next Action "Next Actions for Trello - Set up Behat"
    And I should not see a Next Action "Next Actions for Trello - Set up Bootstrap"
