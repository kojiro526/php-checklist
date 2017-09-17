<?php
namespace PhpChecklist\Test;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PhpChecklist\Libs\Head1;
use PhpChecklist\Libs\Root;

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
        $root->addChild(new Head1($data));
        $this->assertEquals($expects, $root->toString());
    }
}