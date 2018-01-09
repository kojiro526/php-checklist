<?php
namespace PhpChecklist\Test;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PhpChecklist\Libs\Doc\CheckListItem;

class CheckListItemTest extends TestCase
{
    private $test_texts = array(
        <<< EOF1
### aaaa

bbbb

#### 手順

cccc

#### 確認

- [ ] dddd
- [ ] eeee
EOF1
,
        <<< EOF2
### aaaa

#### 手順

cccc

#### 確認

- [ ] dddd
- [ ] eeee
EOF2
,
        <<< EOF3
### aaaa

bbbb

cccc

#### 確認

- [ ] dddd
- [ ] eeee
EOF3
,
        <<< EOF4
### aaaa

bbbb

1. cccc

- [ ] dddd
- [ ] eeee
- [ ] ffff
EOF4
,
    );
    
    public function dataProvider()
    {
        return [
            [
                'data' => $this->test_texts[0],
                'expects' => [
                    'caption' => "aaaa",
                    'note' => "bbbb",
                    'procedure' => "cccc",
                    'expects' => "- [ ] dddd\n- [ ] eeee",
                ]
            ],
            [
                'data' => $this->test_texts[1],
                'expects' => [
                    'caption' => "aaaa",
                    'note' => null,
                    'procedure' => "cccc",
                    'expects' => "- [ ] dddd\n- [ ] eeee",
                ]
            ],
            [
                'data' => $this->test_texts[2],
                'expects' => [
                    'caption' => "aaaa",
                    'procedure' => "bbbb\n\ncccc",
                    'expects' => "- [ ] dddd\n- [ ] eeee",
                ]
            ],
            [
                'data' => $this->test_texts[3],
                'expects' => [
                    'caption' => "aaaa",
                    'procedure' => "bbbb\n\n1. cccc",
                    'expects' => "- [ ] dddd\n- [ ] eeee\n- [ ] ffff",
                ]
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param unknown $data
     * @param unknown $expects
     */
    public function testSection($data, $expects)
    {
        $item = new CheckListItem($data);
        $this->assertEquals($expects['caption'], $item->getCaption());
        if(!empty($item->getNote())){
            $this->assertEquals($expects['note'], $item->getNote()->toString());
        }
        $this->assertEquals($expects['procedure'], $item->getProcedure()->toString());
        $this->assertEquals($expects['expects'], $item->getExpects()->toString());
    }

}
    