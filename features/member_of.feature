Feature: See the cards I am a member of
  In order to decide on my next action
  As a registered user
  I need to be able to see the cards I am a member of

  Scenario: Member of 2 cards
    Given I am a member of the card "Test a walking skeleton"
    And I am a member of the card "Set up Behat feature"
    When I am on the homepage
    Then I should see a Next Action "Test a walking skeleton"
    And I should see a Next Action "Set up Behat feature"
