<?php
namespace PhpChecklist\Libs\Doc;

abstract class Node
{
    /**
     * 生データ
     * 
     * @var string
     */
    protected $raw_text;
    
    protected $children = [];
    
    protected $sequence = 0;
    
    /**
     * 生のテキストをセットする
     * 
     * @param string $text
     */
    protected function setRawText($text)
    {
        $this->raw_text = $text;
    }
    
    /**
     * 生のテキストを返却する
     * 
     * @return string
     */
    public function getRawText()
    {
        return $this->raw_text;
    }
    
    /**
     * 子ノードを追加する
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
    
    /*
    public function isTopSection(){
        return false;
    }
    
    public function isCheckSection(){
        return false;
    }
    public function isCheckItem(){
        return false;
    }
    */
    
    public function setSequence($number)
    {
        $this->sequence = $number;
    }
    
    public function getSequence()
    {
        return $this->sequence;
    }
    
    /**
     * 子ノードの数を返却する
     * 
     * @return number
     */
    public function countChildren()
    {
        return count($this->children);
    }
    
    /**
     * 生のテキストを行で分割する
     * 
     * テキストが空の場合は空の配列を返却する
     * 
     * @return array
     */
    public function split()
    {
        if(empty($this->raw_text)) return [];
        return explode("\n", $this->raw_text);
    }
}