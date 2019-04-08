<?php
namespace PhpChecklist\Test\Libs\Option;

require_once __DIR__ . '/../../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PhpChecklist\Libs\Option\RowColor;

class RowColorTest extends TestCase
{
    
    /**
     * 正常系テストデータ
     * @return string[][]|string[][][][]
     */
    public function dataRowColorNominalTestProvider()
    {
        return [
            [
                'data' => 'default=AAAAAA,.required=FFFFFF',
                'expects' => [
                    ['color' => 'AAAAAA', 'key' => []],
                    ['color' => 'FFFFFF', 'key' => ['.required']],
                ]
            ],
        ];
    }
    
    
    /**
     * 正常系
     * 
     * @dataProvider dataRowColorNominalTestProvider
     * @param array $data
     * @param array $expects
     */
    public function testRowColorNominal($data, $expects)
    {
        $row_color = new RowColor($data);
        foreach ($expects as $i => $item)
        {
            $this->assertEquals($item['color'], $row_color->getColor($item['key']));
        }
    }
}