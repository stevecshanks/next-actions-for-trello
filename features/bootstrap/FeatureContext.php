<?php

use App\Tests\Trello\CardBuilder;
use App\Tests\Trello\DataSource;
use App\Tests\Trello\FakeJsonApi;
use App\Trello\Board;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context
{
    /** @var DataSource */
    protected $fakeApiData;

    /**
     * @BeforeScenario
     */
    public function prepare(BeforeScenarioScope $scope)
    {
        // Make sure no cards are hanging around from a previous test
        FakeJsonApi::reset();

        $this->fakeApiData = new DataSource();
        FakeJsonApi::setDataSource($this->fakeApiData);
    }

    /**
     * @Given I am a member of the card :cardName
     * @Given I am a member of the card :cardName on the board :boardName
     */
    public function iAmAMemberOfTheCardOnTheBoard($cardName, $boardName = 'some board')
    {
        $board = new Board(md5($boardName), $boardName);
        $card = (new CardBuilder($cardName))
            ->withBoardId($board->getId())
            ->buildCard();
        $this->fakeApiData->addBoard($board);
        $this->fakeApiData->joinCard($card);
    }

    /**
     * @Given I have a card :name on my Next Actions list
     */
    public function iHaveACardOnMyNextActionsList($name)
    {
        $card = (new CardBuilder($name))
            ->withUrl($this->generateFakeUrlForCard($name))
            ->buildCard();
        $this->fakeApiData->addNextActionCard($card);
    }

    /**
     * @Given I have a project :name
     */
    public function iHaveAProject($name)
    {
        FakeJsonApi::addProject($name);
        // FIXME Do nothing, then refactor steps
    }

    /**
     * @Given :projectName has a Todo card :cardName
     */
    public function hasATodoCard($projectName, $cardName)
    {
        // FIXME RENAME ME ^^^^

        FakeJsonApi::addTodoCardToProject($projectName, $cardName);

        $board = new Board(md5($projectName), $projectName);
        $projectCard = (new CardBuilder($projectName))
            ->linkedToProject($board->getId())
            ->buildCard();
        $card = (new CardBuilder($cardName))
            ->withBoardId($board->getId())
            ->buildCard();

        $this->fakeApiData->addBoard($board);
        $this->fakeApiData->addProjectCard($projectCard);
        $this->fakeApiData->addTodoCard($card);
    }

    /**
     * @When I view my Next Actions list
     */
    public function iViewMyNextActionsList()
    {
        $this->visit("/actions");
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
     * @Then I should be taken to :cardName on Trello
     */
    public function iShouldBeTakenToOnTrello($cardName)
    {
        $currentUrl = $this->getSession()->getCurrentUrl();
        $expectedUrl = $this->generateFakeUrlForCard($cardName);

        assert(strpos($currentUrl, $expectedUrl) !== false);
    }

    /**
     * @Then I should see the Project :name
     */
    public function iShouldSeeTheProject($name)
    {
        $this->assertPageContainsText($name);
    }

    protected function generateFakeUrlForCard(string $cardName): string
    {
        return '/actions?testcard=' . urlencode($cardName);
    }
}
