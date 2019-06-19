<?php

namespace App\Tests\Trello;

use App\Trello\Board;
use App\Trello\BoardId;
use App\Trello\Config;
use App\Trello\ListId;
use PHPUnit\Framework\TestCase;

class FakeJsonApiTest extends TestCase
{
    public function testFetchCardsIAmAMemberOfReturnsJoinedCards()
    {
        $card = (new CardBuilder('my card'))->build();

        $dataSource = new DataSource();
        $dataSource->joinCard($card);
        FakeJsonApi::setDataSource($dataSource);

        $api = new FakeJsonApi($this->createMock(Config::class));

        $results = $api->fetchCardsIAmAMemberOf();
        $this->assertCount(1, $results);
        $this->assertSame($card->getId(), $results[0]->getId());
    }

    public function testFetchListsOnBoardReturnsEmptyArrayForNonTodoLists()
    {
        $board = $this->createMock(Board::class);

        $dataSource = new DataSource();
        $dataSource->addBoard($board);
        FakeJsonApi::setDataSource($dataSource);

        $api = new FakeJsonApi($this->createMock(Config::class));

        $this->assertSame([], $api->fetchListsOnBoard($board));
    }

    public function testFetchListsOnBoardReturnsTodoList()
    {
        $board = $this->createMock(Board::class);
        $board->method('getId')->willReturn('an id');

        $card = (new CardBuilder('some card'))->withBoardId('an id')->build();

        $dataSource = new DataSource();
        $dataSource->addBoard($board);
        $dataSource->addTodoCard($card);
        FakeJsonApi::setDataSource($dataSource);

        $api = new FakeJsonApi($this->createMock(Config::class));

        $results = $api->fetchListsOnBoard($board);

        $this->assertCount(1, $results);
        $this->assertSame('Todo', $results[0]->getName());
    }

    public function testFetchBoardReturnsNullForUnknownBoardId()
    {
        $dataSource = new DataSource();
        FakeJsonApi::setDataSource($dataSource);

        $api = new FakeJsonApi($this->createMock(Config::class));

        $this->assertNull($api->fetchBoard(new BoardId('some board')));
    }

    public function testFetchBoardReturnsCorrectBoard()
    {
        $board = $this->createMock(Board::class);
        $board->method('getId')->willReturn('an id');

        $dataSource = new DataSource();
        $dataSource->addBoard($board);
        FakeJsonApi::setDataSource($dataSource);

        $api = new FakeJsonApi($this->createMock(Config::class));

        $result = $api->fetchBoard(new BoardId('an id'));

        $this->assertSame($board->getId(), $result->getId());
    }

    public function testFetchCardsOnListReturnsEmptyArrayForUnknownList()
    {
        $dataSource = new DataSource();
        FakeJsonApi::setDataSource($dataSource);

        $api = new FakeJsonApi($this->createMock(Config::class));

        $this->assertSame([], $api->fetchCardsOnList(new ListId('some list')));
    }

    public function testFetchCardsOnListReturnsProjectCards()
    {
        $card = (new CardBuilder('a project'))->build();

        $dataSource = new DataSource();
        $dataSource->addProjectCard($card);
        FakeJsonApi::setDataSource($dataSource);

        $config = $this->createMock(Config::class);
        $config->method('getProjectsListId')->willReturn(new ListId('projects'));

        $api = new FakeJsonApi($config);

        $results = $api->fetchCardsOnList(new ListId('projects'));

        $this->assertCount(1, $results);
        $this->assertSame($card->getId(), $results[0]->getId());
    }

    public function testFetchCardsOnListReturnsNextActionCards()
    {
        $card = (new CardBuilder('some card'))->build();

        $dataSource = new DataSource();
        $dataSource->addNextActionCard($card);
        FakeJsonApi::setDataSource($dataSource);

        $config = $this->createMock(Config::class);
        $config->method('getNextActionsListId')->willReturn(new ListId('actions'));

        $api = new FakeJsonApi($config);

        $results = $api->fetchCardsOnList(new ListId('actions'));

        $this->assertCount(1, $results);
        $this->assertSame($card->getId(), $results[0]->getId());
    }

    public function testFetchCardsOnListReturnsTodoListCards()
    {
        $card = (new CardBuilder('some card'))->withBoardId('123')->build();

        $dataSource = new DataSource();
        $dataSource->addTodoCard($card);
        FakeJsonApi::setDataSource($dataSource);

        $api = new FakeJsonApi($this->createMock(Config::class));

        $results = $api->fetchCardsOnList(new ListId('123'));

        $this->assertCount(1, $results);
        $this->assertSame($card->getId(), $results[0]->getId());
    }
}
