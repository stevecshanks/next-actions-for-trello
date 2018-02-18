<?php

namespace App\Tests\Trello;

use App\Trello\Checklist;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    public function testGetChecklistsReturnsChecklistsInCorrectOrder()
    {
        $checklist1 = $this->createMock(Checklist::class);
        $checklist1->method('getPosition')->willReturn(999);
        $checklist2 = $this->createMock(Checklist::class);
        $checklist2->method('getPosition')->willReturn(1);

        $card = (new CardBuilder('test'))
            ->withChecklist($checklist1)
            ->withChecklist($checklist2)
            ->build();

        $this->assertSame([$checklist2, $checklist1], $card->getChecklists());
    }
}
