<?php
namespace PhpChecklist\Libs\Doc;

use PhpChecklist\Libs\Node;

class Section extends Node
{
    
    protected $heading_raw;
    protected $content_raw;

    public function __construct($text)
    {
        $this->setRawText($text);
        $lines = $this->split();
        if (count($lines) > 0) {
            if (! $this->isHeading($lines[0])) {
                throw new \Exception('Missing heading.');
            }
            $this->heading_raw = $lines[0];
            
            $content = '';
            for($i=1; $i<count($lines); $i++){
                $content .= $lines[$i] . "\n";
            }
            $this->content_raw = $content;
        }
    }

    /**
     * 入力された行からキャプションをパースする
     *
     * パースできなければnullを返す
     *
     * @param string $line            
     * @return string|NULL
     */
    protected function parseHeading($line)
    {
        if (preg_match('/^#{1,6} +(.*)/', $line, $matches)) {
            return $matches[1];
        }
        return null;
    }
    
    public function toString(){
        return $this->raw_text;
    }

    /**
     * 指定された行が見出し行かどうかを判定する。
     *
     * @param string $line            
     * @return boolean
     */
    protected function isHeading($line)
    {
        return ! empty(preg_match('/^#{1,6} /', $line));
    }
    
    /**
     * セクションのキャプションを返却する
     * 
     * セクションの見出し（Heading）のテキストを返却する
     * 
     * @return string|NULL
     */
    public function getCaption(){
        return $this->parseHeading($this->heading_raw);
    }
    
    /**
     * キャプションを除いたコンテンツ部分のテキストを返却する。
     * 
     * @return mixed
     */
    public function getRawContent(){
        return $this->content_raw;
    }
}