<?php

use App\NextAction;
use App\Trello\Card;
use App\Trello\Checklist;
use App\Trello\ChecklistItem;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\TestCase;

class NextActionTest extends TestCase
{
    public function testGetNextChecklistItemReturnsNullIfThereAreNoChecklists()
    {
        $card = $this->createMock(Card::class);
        $card->method('getChecklists')->willReturn([]);

        $nextAction = new NextAction($card);

        $this->assertNull($nextAction->getNextChecklistItem());
    }

    public function testGetNextChecklistItemReturnsNullIfAllChecklistsAreEmpty()
    {
        $checklist = $this->createMock(Checklist::class);
        $checklist->method('getItems')->willReturn([]);

        $card = $this->createMock(Card::class);
        $card->method('getChecklists')->willReturn([$checklist]);

        $nextAction = new NextAction($card);

        $this->assertNull($nextAction->getNextChecklistItem());
    }

    public function testGetNextChecklistItemReturnsNullIfAllChecklistItemsAreComplete()
    {
        $item = $this->createMock(ChecklistItem::class);
        $item->method('isComplete')->willReturn(true);

        $checklist = $this->createMock(Checklist::class);
        $checklist->method('getItems')->willReturn([$item]);

        $card = $this->createMock(Card::class);
        $card->method('getChecklists')->willReturn([$checklist]);

        $nextAction = new NextAction($card);

        $this->assertNull($nextAction->getNextChecklistItem());
    }

    public function testGetNextChecklistItemReturnsFirstIncompleteItemWithSingleChecklist()
    {
        $item1 = $this->createMock(ChecklistItem::class);
        $item1->method('isComplete')->willReturn(true);
        $item2 = $this->createMock(ChecklistItem::class);
        $item2->method('isComplete')->willReturn(false);

        $checklist = $this->createMock(Checklist::class);
        $checklist->method('getItems')->willReturn([$item1, $item2]);

        $card = $this->createMock(Card::class);
        $card->method('getChecklists')->willReturn([$checklist]);

        $nextAction = new NextAction($card);

        $this->assertSame($item2, $nextAction->getNextChecklistItem());
    }

    public function testGetNextChecklistItemReturnsFirstIncompleteItemFromMultipleChecklists()
    {
        $item1 = $this->createMock(ChecklistItem::class);
        $item1->method('isComplete')->willReturn(true);
        $checklist1 = $this->createMock(Checklist::class);
        $checklist1->method('getItems')->willReturn([$item1]);

        $item2 = $this->createMock(ChecklistItem::class);
        $item2->method('isComplete')->willReturn(false);
        $checklist2 = $this->createMock(Checklist::class);
        $checklist2->method('getItems')->willReturn([$item2]);

        $card = $this->createMock(Card::class);
        $card->method('getChecklists')->willReturn([$checklist1, $checklist2]);

        $nextAction = new NextAction($card);

        $this->assertSame($item2, $nextAction->getNextChecklistItem());
    }

    public function testIsOverdueReturnsFalseForCardsDueLaterToday()
    {
        $now = Chronos::createFromFormat('Y-m-d', '2018-01-31');
        Chronos::setTestNow($now);

        $card = $this->createMock(Card::class);
        $card->method('getDueDate')->willReturn($now->addHour());

        $nextAction = new NextAction($card);

        $this->assertFalse($nextAction->isOverdue());
    }

    public function testIsOverdueReturnsTrueForCardsDueNow()
    {
        $now = Chronos::createFromFormat('Y-m-d', '2018-01-31');
        Chronos::setTestNow($now);

        $card = $this->createMock(Card::class);
        $card->method('getDueDate')->willReturn($now);

        $nextAction = new NextAction($card);

        $this->assertTrue($nextAction->isOverdue());
    }

    public function testIsDueSoonReturnsFalseForCardsDueInTwoDays()
    {
        $today = Chronos::createFromFormat('Y-m-d', '2018-01-31');
        Chronos::setTestNow($today);

        $card = $this->createMock(Card::class);
        $card->method('getDueDate')->willReturn($today->addDays(2));

        $nextAction = new NextAction($card);

        $this->assertFalse($nextAction->isDueSoon());
    }

    public function testIsDueSoonReturnsTrueForCardsDueTomorrow()
    {
        $today = Chronos::createFromFormat('Y-m-d', '2018-01-31');
        Chronos::setTestNow($today);

        $card = $this->createMock(Card::class);
        $card->method('getDueDate')->willReturn($today->addDay());

        $nextAction = new NextAction($card);

        $this->assertTrue($nextAction->isDueSoon());
    }

    protected function tearDown(): void
    {
        Chronos::setTestNow();
    }
}
