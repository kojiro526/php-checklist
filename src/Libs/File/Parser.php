<?php
namespace PhpChecklist\Libs\File;

class Parser
{
    public static function parse(string $dir_path)
    {
        $file_finder = new FileFinder();
        $file_finder->setAllowExtensions(['md', 'markdown']);
        $file_paths = $file_finder->scan($dir_path);
        
        foreach ($file_paths as $file_path)
        {
            $markdown .= file_get_contents($file_path) . "\n";
        }
    }
}