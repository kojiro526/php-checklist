<?php
namespace PhpChecklist;

use PhpChecklist\Libs\Markdown;
use PhpChecklist\Libs\File\FileFinder;
use PhpChecklist\Libs\File\Output\ExcelBuilder;
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
        $parser->addOption('split', [
            'short_name' => '-s',
            'long_name' => '--split',
            'description' => 'Split by sheets',
            'action' => 'StoreTrue'
        ]);
        $this->options = $parser->parse()->options;
    }

    public function execute()
    {
        if (! ($input_path = realpath($this->options['input']))) {
            die('File or directory not found.');
        }

        $output_file = $this->options['output'];
        if(!in_array(pathinfo($output_file, PATHINFO_EXTENSION), array('xlsx'))){
            die('Invalid output file format.' . "\n");
        }

        $file_finder = new FileFinder();
        $file_finder->setAllowExtensions([
            'md',
            'markdown'
        ]);
        $input_files = $file_finder->scan($input_path);
        
        $markdown = '';
        foreach ($input_files as $file_path) {
            $markdown .= file_get_contents($file_path) . "\n";
        }
        $root = Markdown::parse($markdown);
        
        $excel_builder = new ExcelBuilder($root);
//        $excel_builder->setFilePath(__DIR__ . '/../tmp/output.xlsx')
        $excel_builder->setFilePath($output_file)
            ->setColumnOffset(2)
            ->setRowOffset(2)
            ->addColumn('No', [
            'width' => 5.5
        ])
            ->addColumn('試験項目', [
            'width' => 19
        ])
            ->addColumn('手順', [
            'width' => 38
        ])
            ->addColumn('確認', [
            'width' => 38
        ])
            ->addColumn('確認者', [
            'width' => 8
        ])
            ->addColumn('確認日', [
            'width' => 8
        ])
            ->addColumn('備考', [
            'width' => 17
        ]);
        $excel_builder->build()->save();
    }
}