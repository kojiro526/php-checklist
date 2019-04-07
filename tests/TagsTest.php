<?php
namespace PhpChecklist\Test;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PhpChecklist\Libs\Doc\Header;
use PhpChecklist\Libs\Doc\Tags;

class TagsTest extends TestCase
{

    /**
     * 正常系テストデータ
     * @return string[][]|string[][][][]
     */
    public function dataTagsNominalTestProvider()
    {
        return [
            [
                'data' => "#foo .baa",
                'expects' => ['tags' => ['#foo', '.baa']]
            ],
        ];
    }

    /**
     * 正常系
     * 
     * @dataProvider dataTagsNominalTestProvider
     * @param array $data
     * @param array $expects
     */
    public function testTagsNominal($data, $expects)
    {
        $tags = new Tags($data);
        $this->assertEquals($expects['tags'], $tags->toArray());
    }

    /**
     * 正常系テストデータ
     * @return string[][]|string[][][][]
     */
    public function dataTagsExistsNominalTestProvider()
    {
        return [
            [
                'data' => "#foo .baa",
                'expects' => [
                    ['key' => '#foo', 'result' => true],
                    ['key' => '.baa', 'result' => true],
                    ['key' => '  .baa  ', 'result' => true],
                    ['key' => '#hoge', 'result' => false],
                    ['key' => '', 'result' => false],
                    ['key' => null, 'result' => false],
                ]
            ],
        ];
    }

    /**
     * 正常系
     * 
     * @dataProvider dataTagsExistsNominalTestProvider
     * @param array $data
     * @param array $expects
     */
    public function testTagsExistsNominal($data, $expects)
    {
        $tags = new Tags($data);
        
        foreach($expects as $row)
        {
            $this->assertEquals($row['result'], $tags->has($row['key']), sprintf('data: %s, key: %s', $data, $row['key']));
        }
    }
}