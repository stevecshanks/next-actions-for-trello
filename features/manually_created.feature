Feature: See the cards I have manually created as Next Actions
  In order to decide on my next action
  As a registered user
  I need to be able to see the cards I have manually added to my Next Actions list

  Scenario: 2 cards on Next Actions list
    Given I have a card "Order cat food" on my Next Actions list
    And I have a card "Buy Christmas presents" on my Next Actions list
    When I am on "/actions"
    Then I should see a Next Action "Order cat food"
    And I should see a Next Action "Buy Christmas presents"
