<?php
namespace PhpChecklist\Test;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PhpChecklist\Libs\Doc\Section;

class SectionTest extends TestCase
{
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

}
    