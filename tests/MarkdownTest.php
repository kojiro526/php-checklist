<?php
namespace PhpChecklist\Test;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PhpChecklist\Libs\Markdown;
use PhpChecklist\Libs\File\FileFinder;

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

    /**
     * 正常系テストデータ
     * @return string[][]|string[][][][]
     */
    public function dataProviderForTestMarkdownParserFromFiles()
    {
        return [
            [
                'data' => "./doc/test/doc1",
                'expects' => [
                    'has_metadata' => true,
                    'metadata' => [
                        "title" => "Document title",
                        "subtitle" => "Document subtitle",
                        "author" => "Example Author",
                        "date" => "2018-01-15",
                        "toc-title" => "目次",
                        "abstract" => "This is abstract\nIt consists of two paragraphs.\n"
                    ]
                ]
            ]
        ];
    }

    /**
     * 正常系（実ファイルを元に実行）
     * 
     * @dataProvider dataProviderForTestMarkdownParserFromFiles
     * @param array $data
     * @param array $expects
     */
    public function testMarkdownParserFromFiles($data, $expects)
    {
        $file_finder = new FileFinder();
        $file_finder->setAllowExtensions([
            'md',
            'markdown'
        ]);
        $input_files = $file_finder->scan($data);
        
        $markdown = '';
        foreach ($input_files as $file_path) {
            $markdown .= file_get_contents($file_path) . "\n";
        }
        $root = Markdown::parse($markdown);
        
        $this->assertEquals($expects['has_metadata'], $root->hasMetadata());
        if(! is_null($metadata = $root->getMetadata()))
        {
            $this->assertEquals($expects['metadata'], $metadata->toArray());
            $this->assertEquals($expects['metadata']['title'], $metadata->getTitle());
            $this->assertEquals($expects['metadata']['subtitle'], $metadata->getSubTitle());
            $this->assertEquals($expects['metadata']['author'], $metadata->getAuthor());
            $this->assertEquals($expects['metadata']['date'], $metadata->getDate());
            $this->assertEquals($expects['metadata']['toc-title'], $metadata->getTocTitle());
            $this->assertEquals($expects['metadata']['abstract'], $metadata->getAbstract());
        }
    }
}