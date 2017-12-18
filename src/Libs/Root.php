<?php
namespace PhpChecklist\Libs;

class Root extends Node
{
    private $check_list_section = null;
    
    public function toString()
    {
        $text = [];
        foreach($this->children as $child)
        {
            array_push($text, $child->toString());
        }
        return join("\n", $text);
    }
    
    /**
     * パートの配列を返却する
     * 
     * @return array
     */
    public function getParts(){
        return $this->getChildren();
    }
    
    /*
    public function getTitle()
    {
        return $this->children[0]->toString();
    }
    
    public function addCheckListSection(CheckList $check_list)
    {
        $this->check_list_section = $check_list;
    }
    
    public function getCheckListSection()
    {
        return $this->check_list_section;
    }
    */
}