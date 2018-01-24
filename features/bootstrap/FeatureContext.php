<?php

use App\Tests\Trello\CardBuilder;
use App\Tests\Trello\DataSource;
use App\Tests\Trello\FakeJsonApi;
use App\Trello\Board;
use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Cake\Chronos\Chronos;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context
{
    /** @var DataSource */
    protected $fakeApiData;

    /**
     * FeatureContext constructor.
     */
    public function __construct()
    {
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
            ->build();
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
            ->build();
        $this->fakeApiData->addNextActionCard($card);
    }

    /**
     * @Given I have a project :projectName with a Todo card :cardName
     */
    public function iHaveAProjectWithATodoCard($projectName, $cardName)
    {
        $board = new Board(md5($projectName), $projectName);
        $projectCard = (new CardBuilder($projectName))
            ->linkedToProject($board->getId())
            ->build();
        $card = (new CardBuilder($cardName))
            ->withBoardId($board->getId())
            ->build();

        $this->fakeApiData->addBoard($board);
        $this->fakeApiData->addProjectCard($projectCard);
        $this->fakeApiData->addTodoCard($card);
    }

    /**
     * @Transform :date
     */
    public function castStringToDateTime($date)
    {
        return Chronos::createFromFormat("d/m/Y", $date);
    }

    /**
     * @Given I have a card :name on my Next Actions list with a due date of :date
     */
    public function iHaveACardOnMyNextActionsListWithADueDateOf($name, DateTimeInterface $date)
    {
        $card = (new CardBuilder($name))
            ->withDueDate($date)
            ->build();
        $this->fakeApiData->addNextActionCard($card);
    }

    /**
     * @Given I have a card :cardName on my Next Actions list with the label :labelName
     */
    public function iHaveACardOnMyNextActionsListWithTheLabel($cardName, $labelName)
    {
        $card = (new CardBuilder($cardName))
            ->withLabel($labelName)
            ->build();
        $this->fakeApiData->addNextActionCard($card);
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
