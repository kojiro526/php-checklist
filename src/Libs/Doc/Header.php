<?php
namespace PhpChecklist\Libs\Doc;

class Header
{
    private $raw = '';

    public function __construct($line)
    {
        $trimmed = trim($line);
        if (self::verify($trimmed) === false) {
            throw new \Exception(sprintf("Invalid header format: %s", $line));
        }
        $this->raw = $trimmed;
    }
    
    public function getLevel()
    {
        if(preg_match('/^(#{1,6})/', $this->raw, $matched) === 1 ) {
            return strlen($matched[1]);
        }
        return 0;
    }
        
    public function getCaption()
    {
        if (empty($this->raw))
            return null;
        
        // Tagがあればそれを除去する
        $header_without_tag = $this->raw;
        // 末尾のHeader Identifiersを抜き出す"{ .required }"）
        if (preg_match('/^.*(:?\{(.*?)\})$/', $header_without_tag, $matched) === 1)
        {
            $header_without_tag = trim(preg_replace(sprintf('/%s$/', $matched[1]), '', $header_without_tag));
        }
            
        if (preg_match('/^#{1,6} +(.*?)$/', $header_without_tag, $matched) === 1) {
            return trim($matched[1]);
        }
        return null;
    }
    
    public function getTags()
    {
        if (preg_match('/^.*\{(.*?)\}$/', $this->raw, $matched) === 1)
        {
            return new Tags($matched[1]);
        }
        return new Tags('');
    }
    
    public static function verify($line)
    {
        return preg_match('/^#{1,6} +.*/', $line) === 1;
    }
}