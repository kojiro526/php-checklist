<?php
namespace PhpChecklist\Libs\Doc;

class Note extends Node
{

    public function __construct($text)
    {
        $this->setRawText($text);
    }
    
    /**
     * テキストを返却する
     * 
     * {@inheritDoc}
     * @see \PhpChecklist\Libs\Doc\Node::toString()
     */
    public function toString(){
        return $this->trimTail($this->raw_text);
    }
}
