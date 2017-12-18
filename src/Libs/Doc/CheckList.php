<?php
namespace PhpChecklist\Libs\Doc;

use PhpChecklist\Libs\Node;

class CheckList extends Node
{
    public function __construct($text){
        $this->setRawText($text);
        $this->parseCheckListItems();
    }

    public function toString()
    {
        if(preg_match('/^## +(.*)/', $this->raw_text, $matches))
        {
            return $matches[1];
        }
        return $this->raw_text;
    }
    
    /**
     * 入力された行が試験項目の見出しレベルかを判定する。
     *
     * 当面、試験項目の見出しはレベル3で固定するが、将来的にレベル3以外の見出しでも
     * 指定できるようにする余地を残す。
     *
     * @param string $line            
     * @return boolean
     */
    private function isItemHeaderLevel($line)
    {
        return ! empty(preg_match('/^### /', $line));
    }

    
    /**
     * テキストから試験項目をパースする
     * 
     * @return void
     */
    private function parseCheckListItems(){
        $lines = $this->split();
        $lines = array_map('trim', $lines);

        $item_text = '';
        $item_fg = false;
        foreach ($lines as $line) {
            if ($this->isItemHeaderLevel($line)) {
                $item_fg = true;
                if(!empty($item_text)){
                    $this->addChild(new CheckListItem($item_text));
                }
                $item_text = '';
            }
            
            if ($item_fg)
                $item_text .= $line . "\n";
        }
        
        if(!empty($item_text)) $this->addChild(new CheckListItem($item_text));
    }
}