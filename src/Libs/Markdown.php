<?php
namespace PhpChecklist\Libs;

class Markdown
{
    public static function parse($text)
    {
        $array = explode("\n", $text);
        $array = array_map('trim', $array);
        
        $response = [];
        
        foreach($array as $line)
        {
            if(preg_match('/^# /', $line))
            {
                array_push($response, new Root());
                $response[count($response) -1]->addChild(new Head1($line));
                continue;
            }

            if(preg_match('/^## /', $line))
            {
                if(count($response) == 0) die('Missing head1.' . "\n");
                $response[count($response)-1]->addChild(new Head2($line));
                continue;
            }

            if(preg_match('/^### /', $line))
            {
                if(count($response) == 0) die('Missing head1.' . "\n");
                $response[count($response)-1]->addChild(new Head2($line));
                continue;
            }
        }
        
        return $response;
    }
}