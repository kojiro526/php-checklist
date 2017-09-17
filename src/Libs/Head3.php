<?php
namespace PhpChecklist\Libs;

class Head3 extends Node
{
    public function __construct($text)
    {
        $this->setRawText($text);
    }
    
    public function toString()
    {
        return $this->raw_text;
    }
}