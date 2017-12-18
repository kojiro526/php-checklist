<?php
namespace PhpChecklist\Libs;

use PhpChecklist\Libs\Doc\Part;

class Markdown
{

    /**
     * Markdownテキストのパーサ
     *
     * Markdownで書かれたテスト項目書をパースする
     *
     * @param string $text 全てのMarkdownファイルを結合したテキスト
     * @return PhpChecklist\Libs\Doc\Root
     */
    public static function parse($text)
    {
        $lines = explode("\n", $text);
        $lines = array_map('trim', $lines);
        
        $part_text = '';
        $root = new Root();
        
        foreach ($lines as $line) {
            if (preg_match('/^# /', $line)) {
                if (! empty($part_text)) {
                    $root->addChild(new Part($part_text));
                }
                $part_text = '';
            }
            
            $part_text .= $line . "\n";
        }
        
        if (! empty($part_text))
            $root->addChild(new Part($part_text));
        
        return $root;
    }
}