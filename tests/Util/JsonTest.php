<?php

namespace Tests\App\Tests\Util;

use App\Util\Json;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    public function testFromStringThrowsExceptionForInvalidJson()
    {
        $this->expectException(InvalidArgumentException::class);

        Json::fromString('invalid json string');
    }

    /**
     * @dataProvider validJsonProvider
     */
    public function testDecodeReturnsCorrectObjectForValidJson(string $validJson, $expectedResult)
    {
        $json = Json::fromString($validJson);

        $this->assertEquals($expectedResult, $json->decode());
    }

    public function validJsonProvider()
    {
        return [
            [json_encode(null), null],
            [json_encode(false), false],
            [json_encode(true), true],
            [json_encode([1, 2, 3]), [1, 2, 3]],
            [json_encode(['id' => 1, 'name' => 'test']), (object) ['id' => 1, 'name' => 'test']]
        ];
    }
}
