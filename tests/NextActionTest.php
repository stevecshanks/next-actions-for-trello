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

    public function testIsOverdueReturnsFalseForCardsDueToday()
    {
        $dueDate = Chronos::createFromFormat('Y-m-d', '2018-01-31');
        Chronos::setTestNow($dueDate);

        $card = $this->createMock(Card::class);
        $card->method('getDueDate')->willReturn($dueDate);

        $nextAction = new NextAction($card);

        $this->assertFalse($nextAction->isOverdue());
    }

    public function testIsOverdueReturnsFalseForCardsDueYesterdayToday()
    {
        $dueDate = Chronos::createFromFormat('Y-m-d', '2018-01-31');
        Chronos::setTestNow($dueDate->addDay());

        $card = $this->createMock(Card::class);
        $card->method('getDueDate')->willReturn($dueDate);

        $nextAction = new NextAction($card);

        $this->assertTrue($nextAction->isOverdue());
    }

    protected function tearDown()
    {
        Chronos::setTestNow();
    }
}
