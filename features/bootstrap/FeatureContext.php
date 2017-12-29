<?php

use App\Tests\Trello\FakeJsonApi;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
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
     * @Given I am a member of the card :name
     */
    public function iAmAMemberOfTheCard($name)
    {
        FakeJsonApi::joinCard($name);
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

}
