<?php

namespace App\Tests\Trello;

use App\Trello\Label;
use PHPUnit\Framework\TestCase;

class LabelTest extends TestCase
{
    public function testFromJsonConstructsObjectCorrectly()
    {
        $json = json_encode(['name' => 'Test']);

        $label = Label::fromJson(json_decode($json));

        $this->assertSame('Test', $label->getName());
    }
}
