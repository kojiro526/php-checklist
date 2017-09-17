<?php
namespace PhpChecklist\Libs;

class Root extends Node
{
    public function toString()
    {
        $text = [];
        foreach($this->children as $child)
        {
            array_push($text, $child->toString());
        }
        return join("\n", $text);
    }
}