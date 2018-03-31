<?php

namespace App\Tests\Trello;

use App\Trello\Board;
use App\Trello\BoardId;
use App\Trello\Card;
use PHPUnit\Framework\TestCase;

class DataSourceTest extends TestCase
{
    public function testJoinedCardsAreReturnedCorrectly()
    {
        $card1 = $this->createMock(Card::class);
        $card2 = $this->createMock(Card::class);

        $dataSource = new DataSource();
        $dataSource->joinCard($card1);
        $dataSource->joinCard($card2);

        $this->assertSame([$card1, $card2], $dataSource->getJoinedCards());
    }

    public function testProjectCardsAreReturnedCorrectly()
    {
        $card1 = (new CardBuilder(''))->withDescription('one')->build();
        $card2 = (new CardBuilder(''))->withDescription('two')->build();

        $dataSource = new DataSource();
        $dataSource->addProjectCard($card1);
        $dataSource->addProjectCard($card2);

        $this->assertSame([$card1, $card2], $dataSource->getProjectCards());
    }

    public function testNextActionCardsAreReturnedCorrectly()
    {
        $card1 = $this->createMock(Card::class);
        $card2 = $this->createMock(Card::class);

        $dataSource = new DataSource();
        $dataSource->addNextActionCard($card1);
        $dataSource->addNextActionCard($card2);

        $this->assertSame([$card1, $card2], $dataSource->getNextActionCards());
    }

    public function testGetBoardByIdReturnsNullIfNoMatchingBoardFound()
    {
        $dataSource = new DataSource();

        $this->assertSame(null, $dataSource->getBoardById(new BoardId('any board')));
    }

    public function testGetBoardByIdReturnsCorrectBoard()
    {
        $board = new Board('some id', 'a name', 'a url');

        $dataSource = new DataSource();
        $dataSource->addBoard($board);

        $this->assertSame($board, $dataSource->getBoardById(new BoardId('some id')));
    }

    public function testGetCardsOnTodoListReturnsEmptyListIfNoCardsFound()
    {
        $dataSource = new DataSource();

        $this->assertSame([], $dataSource->getCardsOnTodoList('a list id'));
    }

    public function testGetCardsOnTodoListReturnsCorrectCards()
    {
        $card1 = (new CardBuilder(''))->withBoardId('project1')->build();
        $card2 = (new CardBuilder(''))->withBoardId('project1')->build();

        $card3 = (new CardBuilder(''))->withBoardId('project2')->build();

        $dataSource = new DataSource();
        $dataSource->addTodoCard($card1);
        $dataSource->addTodoCard($card2);
        $dataSource->addTodoCard($card3);

        $this->assertSame([$card1, $card2], $dataSource->getCardsOnTodoList('project1'));
        $this->assertSame([$card3], $dataSource->getCardsOnTodoList('project2'));
    }
}
