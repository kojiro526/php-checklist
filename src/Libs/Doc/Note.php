<?php
namespace PhpChecklist\Libs\Doc;

use PhpChecklist\Libs\Node;

class Note extends Node
{

    public function __construct($text)
    {
        $this->setRawText($text);
    }
    
    public function toString(){
        return $this->raw_text;
    }
}
