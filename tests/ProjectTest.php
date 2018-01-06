<?php

namespace App\Tests;

use App\Project;
use App\Tests\Trello\CardBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    /**
     * @dataProvider invalidCardDescriptionProvider
     */
    public function testFromCardThrowsExceptionIfBoardIdCannotBeParsed(string $description)
    {
        $this->expectException(InvalidArgumentException::class);
        $card = (new CardBuilder(''))->withDescription($description)->buildCard();

        Project::fromCard($card);
    }

    public function invalidCardDescriptionProvider()
    {
        return [
            [''],
            ['google.com'],
            ['https://trello.com/c/1234abcd']
        ];
    }

    /**
     * @dataProvider cardDescriptionProvider
     */
    public function testFromCardCreatesCorrectProject(string $description, string $expectedBoardId)
    {
        $card = (new CardBuilder('My Project'))->withDescription($description)->buildCard();

        $project = Project::fromCard($card);

        $this->assertSame('My Project', $project->getName());
        $this->assertSame($expectedBoardId, $project->getBoardId()->getId());
    }

    public function cardDescriptionProvider()
    {
        return [
            ['https://trello.com/b/1234abcd', '1234abcd'],
            ['https://trello.com/b/12345678/my-project-board', '12345678']
        ];
    }
}
