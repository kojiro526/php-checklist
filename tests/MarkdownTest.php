<?php
namespace PhpChecklist\Test;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PhpChecklist\Libs\Markdown;

class MarkdownTest extends TestCase
{
    /**
     * 正常系テストデータ
     * @return string[][]|string[][][][]
     */
    public function dataMarkdownParserProvider()
    {
        return [
            [
                'data' => "# aaaa\n\n## bbbb\n\n### cccc",
                'expects' => ["# aaaa\n## bbbb\n### cccc"]
            ]
        ];
    }

    /**
     * 正常系
     * 
     * @dataProvider dataMarkdownParserProvider
     * @param array $data
     * @param array $expects
     */
    public function testMarkdownParser($data, $expects)
    {
        $array = Markdown::parse($data);
        foreach ($array as $i1 => $root)
        {
            $this->assertEquals($root->toString(), $expects[$i1]);
        }
    }
}