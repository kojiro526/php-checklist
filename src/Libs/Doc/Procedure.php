<?php
namespace PhpChecklist\Libs\Doc;

class Procedure extends Node {
    
    public function __construct($text){
        $this->setRawText($text);
    }

    public function toString(){
        return $this->raw_text;
    }
}
