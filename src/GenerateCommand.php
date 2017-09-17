<?php
namespace PhpChecklist;

use PhpChecklist\Libs\Markdown;
use PhpChecklist\Libs\File\FileFinder;

class GenerateCommand
{
    private $options;

    public function __construct()
    {
        $parser = new \Console_CommandLine();
        $parser->description = 'Check list generator';
        $parser->addOption('input', [
            'short_name' => '-i',
            'long_name' => '--input',
            'description' => 'Indicate input file',
            'action' => 'StoreString'
        ]);
        $parser->addOption('output', [
            'short_name' => '-o',
            'long_name' => '--output',
            'description' => 'Indicate output file',
            'action' => 'StoreString'
        ]);
        $this->options = $parser->parse()->options;
    }

    public function execute()
    {
        if(!($input_path = realpath($this->options['input'])))
        {
            die('File or directory not found.');
        }
        
        $file_finder = new FileFinder();
        $file_finder->setAllowExtensions(['md', 'markdown']);
        $input_files = $file_finder->scan($input_path);

        $markdown = '';
        foreach ($input_files as $file_path)
        {
            $markdown .= file_get_contents($file_path) . "\n";
        }
        $array = Markdown::parse($markdown);
        foreach($array as $root)
        {
            echo $root->toString();
        }
    }
}