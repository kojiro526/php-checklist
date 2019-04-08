<?php
namespace PhpChecklist\Test;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PhpChecklist\Libs\Doc\Header;

class HeaderTest extends TestCase
{

    /**
     * 正常系テストデータ
     * @return string[][]|string[][][][]
     */
    public function dataHeaderNominalTestProvider()
    {
        return [
            [
                'data' => "# hogehoge",
                'expects' => ['level' => 1, 'caption' => 'hogehoge', 'tags' => []]
            ],
            [
                'data' => "# hogehoge fugafuga",
                'expects' => ['level' => 1, 'caption' => 'hogehoge fugafuga', 'tags' => []]
            ],
            [
                'data' => "###### hogehoge",
                'expects' => ['level' => 6, 'caption' => 'hogehoge', 'tags' => []]
            ],
            [
                // 余計な空白が入っていても無視する
                'data' => "######      hogehoge     \n",
                'expects' => ['level' => 6, 'caption' => 'hogehoge', 'tags' => []]
            ],
            [
                'data' => "# hogehoge { #id .class1 }",
                'expects' => ['level' => 1, 'caption' => 'hogehoge', 'tags' => ['#id', '.class1']]
            ],
            [
                // ヘッダテキストとHeader Identifiersの間にスペースが無くても認識する
                'data' => "# hogehoge{ #id .class1 }",
                'expects' => ['level' => 1, 'caption' => 'hogehoge', 'tags' => ['#id', '.class1']]
            ],
            [
                // 最後の中括弧のみHeader Identifiersとして認識する
                'data' => "# hogehoge { #idx .classx }  { #id .class1 }",
                'expects' => ['level' => 1, 'caption' => 'hogehoge { #idx .classx }', 'tags' => ['#id', '.class1']]
            ],
            [
                // 同じ内容の中括弧があっても、最後の中括弧のみHeader Identifiersとして認識する
                'data' => "# hogehoge { #id .class1 }  { #id .class1 }",
                'expects' => ['level' => 1, 'caption' => 'hogehoge { #id .class1 }', 'tags' => ['#id', '.class1']]
            ],
            [
                // 重複するIDやClassはユニークになる
                'data' => "# hogehoge { #id   #id  .class1    .class1 }",
                'expects' => ['level' => 1, 'caption' => 'hogehoge', 'tags' => ['#id', '.class1']]
            ]
        ];
    }

    /**
     * 正常系
     * 
     * @dataProvider dataHeaderNominalTestProvider
     * @param array $data
     * @param array $expects
     */
    public function testHeaderNominal($data, $expects)
    {
        $header = new Header($data);
        $this->assertEquals($expects['level'], $header->getLevel());
        $this->assertEquals($expects['caption'], $header->getCaption());
        $this->assertEquals($expects['tags'], $header->getTags()->toArray());
    }
}