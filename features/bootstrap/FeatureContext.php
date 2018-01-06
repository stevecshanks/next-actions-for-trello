<?php

use App\Tests\Trello\FakeJsonApi;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context
{
    /**
     * @BeforeScenario
     */
    public function prepare(BeforeScenarioScope $scope)
    {
        // Make sure no cards are hanging around from a previous test
        FakeJsonApi::reset();
    }

    /**
     * @When I view my Next Actions list
     */
    public function iViewMyNextActionsList()
    {
        $this->visit("/actions");
    }


    /**
     * @Given I am a member of the card :name
     */
    public function iAmAMemberOfTheCard($name)
    {
        $this->iAmAMemberOfTheCardOnTheBoard($name, 'some board');
    }

    /**
     * @Given I have a card :name on my Next Actions list
     */
    public function iHaveACardOnMyNextActionsList($name)
    {
        FakeJsonApi::addNextActionCard($name);
    }

    /**
     * @Given I have a project :name
     */
    public function iHaveAProject($name)
    {
        FakeJsonApi::addProject($name);
    }

    /**
     * @Given :projectName has a Todo card :cardName
     */
    public function hasATodoCard($projectName, $cardName)
    {
        FakeJsonApi::addTodoCardToProject($projectName, $cardName);
    }

    /**
     * @Then I should see a Next Action :name
     */
    public function iShouldSeeANextAction($name)
    {
        $this->assertPageContainsText($name);
    }

    /**
     * @Then I should not see a Next Action :name
     */
    public function iShouldNotSeeANextAction($name)
    {
        $this->assertPageNotContainsText($name);
    }

    /**
     * @When I click on :nextActionName
     */
    public function iClickOn($nextActionName)
    {
        $this->visit('/actions');
        $this->clickLink($nextActionName);
    }

    /**
     * @Then I should be taken to :cardName on Trello
     */
    public function iShouldBeTakenToOnTrello($cardName)
    {
        $currentUrl = $this->getSession()->getCurrentUrl();
        $expectedUrl = FakeJsonApi::generateFakeUrlForCard($cardName);

        if (strpos($currentUrl, $expectedUrl) === false) {
            throw new ExpectationException(
                "Could not find '$expectedUrl' in '$currentUrl'",
                $this->getSession()
            );
        }
    }

    /**
     * @Then I should see the Project :name
     */
    public function iShouldSeeTheProject($name)
    {
        $this->assertPageContainsText($name);
    }

    /**
     * @Given I am a member of the card :cardName on the board :boardName
     */
    public function iAmAMemberOfTheCardOnTheBoard($cardName, $boardName)
    {
        FakeJsonApi::addBoard($boardName);
        FakeJsonApi::joinCard($cardName);
    }
}
