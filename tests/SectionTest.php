<?php
namespace PhpChecklist\Test;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PhpChecklist\Libs\Doc\Section;

class SectionTest extends TestCase
{
    private $test_texts = [
        "### aaaa\n\nbbbb\n\n#### 手順\n\ncccc\n\n#### 確認\n\n- [ ] dddd\n- [ ] eeee",
    ];
    
    public function dataSectionProvider()
    {
        return [
            [
                'data' => "# aaaa\n\nbbbb",
                'expects' => [
                    'caption' => "aaaa"
                ]
            ]
        ];
    }

    /**
     * @dataProvider dataSectionProvider
     * @param unknown $data
     * @param unknown $expects
     */
    public function testSection($data, $expects)
    {
        $section = new Section($data);
        $this->assertEquals($expects['caption'], $section->getCaption());
    }
    
    public function dataProviderTestSplit()
    {
        return [
            [
                'data' => $this->test_texts[0],
                'expects' => [
                    '### aaaa',
                    '',
                    'bbbb',
                    '',
                    '#### 手順',
                    '',
                    'cccc',
                    '',
                    '#### 確認',
                    '',
                    '- [ ] dddd',
                    '- [ ] eeee',
                ]
            ]
        ];
    }
    
    /**
     * @dataProvider dataProviderTestSplit
     * @param unknown $data
     * @param unknown $expects
     */
    public function testSplit($data, $expects)
    {
        $section = new Section($data);
        $splited = $section->split();
        $this->assertEquals($expects, $splited);
    }

}
    