<?php

namespace App\Tests\Trello;

use App\Trello\Checklist;
use App\Trello\ChecklistItem;
use PHPUnit\Framework\TestCase;

class ChecklistTest extends TestCase
{
    public function testGetItemsReturnsItemsInCorrectOrder()
    {
        $item1 = $this->createMock(ChecklistItem::class);
        $item1->method('getPosition')->willReturn(999);
        $item2 = $this->createMock(ChecklistItem::class);
        $item2->method('getPosition')->willReturn(1);

        $checklist = new Checklist([$item1, $item2]);

        $this->assertSame([$item2, $item1], $checklist->getItems());
    }
}
