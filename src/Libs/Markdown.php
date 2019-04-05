<?php
namespace PhpChecklist\Libs;

use PhpChecklist\Libs\Doc\Part;
use PhpChecklist\Libs\Doc\Root;
use PhpChecklist\Libs\Doc\Metadata;

class Markdown
{

    /**
     * Markdownテキストのパーサ
     *
     * Markdownで書かれたテスト項目書をパースする
     *
     * @param string $text
     *            全てのMarkdownファイルを結合したテキスト
     * @return PhpChecklist\Libs\Doc\Root
     */
    public static function parse($text)
    {
        $lines = explode("\n", $text);
        
        $yaml_block = '';
        $part_text = '';
        $root = new Root();
        $is_yaml_block = 'notfound';
        
        foreach ($lines as $line) {
            if (preg_match('/^---$/', $line) == 1) {
                switch ($is_yaml_block){
                    case 'notfound':
                        $is_yaml_block = 'start';
                        continue;
                    case 'start':
                        $is_yaml_block = 'founded';
                        continue;
                }
            }

            if (preg_match('/^\.\.\.$/', $line) == 1) {
                if ($is_yaml_block == 'start') {
                    $is_yaml_block = 'founded';
                    continue;
                }
            }
            
            switch ($is_yaml_block){
                case 'start':
                    $yaml_block .= $line . "\n";
                    continue;
                case 'founded':
                    $is_yaml_block = 'done';
                    $root->setMetadata(new Metadata($yaml_block));
                    continue;
            }

            if (preg_match('/^# /', $line)) {
                if (! empty($part_text)) {
                    $part = new Part($part_text);
                    if ($part->hasCheckListSection()) {
                        $root->addChild($part);
                    }
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