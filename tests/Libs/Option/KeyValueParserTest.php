<?php
namespace PhpChecklist\Test\Libs\Option;

require_once __DIR__ . '/../../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PhpChecklist\Libs\Option\KeyValueParser;

class KeyValueParserTest extends TestCase
{
    
    /**
     * 正常系テストデータ
     * @return string[][]|string[][][][]
     */
    public function dataKeyValueParserNominalTestProvider()
    {
        return [
            [
                'data' => 'default=AAAAAA',
                'expects' => [
                    'default' => 'AAAAAA'
                ]
            ],
            [
                'data' => 'default=AAAAAA,',
                'expects' => [
                    'default' => 'AAAAAA'
                ]
            ],
            [
                'data' => 'default=AAAAAA,.required=FFFFFF',
                'expects' => [
                    'default' => 'AAAAAA', '.required' => 'FFFFFF'
                ]
            ],
        ];
    }
    
    /**
     * 正常系
     * 
     * @dataProvider dataKeyValueParserNominalTestProvider
     * @param array $data
     * @param array $expects
     */
    public function testKeyValueParserNominal($data, $expects)
    {
        $parsed = KeyValueParser::parse($data);
        $this->assertEquals($expects, $parsed);
    }
    
}
