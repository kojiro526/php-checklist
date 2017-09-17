<?php
namespace PhpChecklist\Libs;

abstract class Node
{
    /**
     * 生データ
     * 
     * @var string
     */
    protected $raw_text;
    
    protected $children = [];
    
    protected function setRawText($text)
    {
        $this->raw_text = $text;
    }
    
    /**
     * 子ノードを塚する
     * 
     * @param Node $node
     */
    public function addChild(Node $node)
    {
        array_push($this->children, $node);
    }
    
    /**
     * 子ノードの配列を返す
     * 
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }
    
    /**
     * 文字列として出力する
     * 
     * @return string
     */
    abstract public function toString();
}