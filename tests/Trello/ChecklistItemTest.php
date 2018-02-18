<?php

namespace App\Tests\Trello;

use App\Trello\ChecklistItem;
use PHPUnit\Framework\TestCase;

class ChecklistItemTest extends TestCase
{
    public function testFromJsonConstructsObjectCorrectly()
    {
        $json = json_encode([
            'name' => 'Test',
            'state' => ChecklistItem::COMPLETE,
            'pos' => 99
        ]);

        $item = ChecklistItem::fromJson(json_decode($json));

        $this->assertSame('Test', $item->getName());
        $this->assertSame(true, $item->isComplete());
        $this->assertSame(99, $item->getPosition());
    }
}
