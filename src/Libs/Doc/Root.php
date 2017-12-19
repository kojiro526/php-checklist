<?php
namespace PhpChecklist\Libs\Doc;

class Root extends Node
{

    private $check_list_section = null;

    public function toString()
    {
        $text = [];
        foreach ($this->children as $child) {
            array_push($text, $child->toString());
        }
        return join("\n", $text);
    }

    /**
     * パートの配列を返却する
     *
     * @return array
     */
    public function getParts()
    {
        return $this->getChildren();
    }
}