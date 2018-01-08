<?php
namespace PhpChecklist\Test;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PhpChecklist\Libs\Doc\Root;
use PhpChecklist\Libs\Doc\Section;

class RootTest extends TestCase
{
    
    public function dataRootProvider()
    {
        return [
            [
                'data' => '# aaaa',
                'expects' => "# aaaa"
            ]
        ];
    }

    /**
     * @dataProvider dataRootProvider
     * @param unknown $data
     * @param unknown $expects
     */
    public function testRoot($data, $expects)
    {
        $root = new Root();
        $root->addChild(new Section($data));
        $this->assertEquals($expects, $root->toString());
    }
}