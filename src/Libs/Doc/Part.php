<?php
namespace PhpChecklist\Libs\Doc;

use PhpChecklist\Libs\Doc\Section;

class Part extends Section
{

    private $check_list;

    public function __construct($text)
    {
        $this->setRawText($text);
        $this->check_list = $this->parseCheckList();
        $lines = $this->split();
        if (count($lines) > 0) {
            if (! $this->isHeading($lines[0])) {
                throw new \Exception('Missing heading.');
            }
            $this->heading_raw = $lines[0];
        }
    }
    
    public function getCheckItems()
    {
        return $this->check_list->getChildren();
    }

    /**
     * パート内に試験項目セクションがあるかどうかを判定する。
     *
     * @return boolean
     */
    public function hasCheckListSection()
    {
        return ! empty($this->check_list);
    }
    
    public function toString(){
        return $this->raw_text;
    }
    
    /**
     * 入力された行が試験項目セクションの見出しかどうかを判定する。
     *
     * @param unknown $line            
     * @return boolean
     */
    private function isCheckListSectionHeader($line)
    {
        if (empty($line))
            return;
        if (preg_match('/^## +(.*)/', $line, $matches)) {
            return $matches[1] == '試験項目';
        }
        return false;
    }

    /**
     * 入力された行が試験項目セクションの見出しレベルかを判定する。
     *
     * 当面、試験項目セクションの指定は見出しレベル2で固定するが、将来的にレベル2以外の見出しでも
     * 指定できるようにする余地を残す。
     *
     * @param string $line            
     * @return boolean
     */
    private function isCheckListHeaderLevel($line)
    {
        return preg_match('/^## /', $line);
    }

    /**
     * パート内のチェックリストをパースする
     * 
     * パート内にチェックリストセクションが存在しない場合はnullを返す。
     *
     * @return null|\PhpChecklist\Libs\Doc\CheckList
     */
    private function parseCheckList()
    {
        $lines = $this->split();
        $checklist_text = '';
        $checklist_fg = false;
        foreach ($lines as $line) {
            if ($this->isCheckListHeaderLevel($line)) {
                $checklist_fg = $this->isCheckListSectionHeader($line);
            }
            
            if ($checklist_fg)
                $checklist_text .= $line . "\n";
        }
        
        if(empty($checklist_text)) return null;
        return new CheckList($checklist_text);
    }
}