<?php
namespace PhpChecklist\Libs\File\Output;

use PhpChecklist;
use PhpChecklist\Libs\Doc\Procedure;
use PhpChecklist\Libs\Doc\Root;
use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * 出力するExcelファイルを組み立てる。
 *
 * @author sasaki
 *        
 */
class ExcelBuilder
{

    private $book;

    private $data_source;

    private $columns = [];

    private $file_path;

    private $column_offset = 0;

    private $row_offset = 0;

    private $row_color = null;

    private $header_row = 1;

    public function __construct(PhpChecklist\Libs\Doc\Root $root)
    {
        $this->book = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $this->data_source = $root;
        
        // 以降のシート生成を簡潔にするために初期状態で存在するシートを全て削除する（通常は初期状態で1シート存在する）
        for ($i = 0; $i < $this->book->getSheetCount(); $i ++) {
            $this->book->removeSheetByIndex($i);
        }
    }

    /**
     * 列の設定を追加する
     *
     * チェックリストのシートの列の定義を設定する。
     *
     * @param string $text
     *            列のタイトル
     * @param array $options
     *            列の設定
     * @return \PhpChecklist\Libs\File\Output\ExcelBuilder
     */
    public function addColumn($text, $options)
    {
        array_push($this->columns, [
            'text' => $text,
            'options' => $options
        ]);
        return $this;
    }

    /**
     * 出力するファイルのパスをセットする
     *
     * @param string $file_path            
     * @return \PhpChecklist\Libs\File\Output\Excel
     */
    public function setFilePath($file_path)
    {
        $this->file_path = $file_path;
        return $this;
    }

    /**
     * 列位置のオフセットを指定する。
     *
     * @param number $offset            
     * @return \PhpChecklist\Libs\File\Output\Excel
     */
    public function setColumnOffset($offset)
    {
        $this->column_offset = $offset;
        return $this;
    }

    /**
     * 行位置のオフセットを指定する。
     *
     * @param number $offset            
     * @return \PhpChecklist\Libs\File\Output\Excel
     */
    public function setRowOffset($offset)
    {
        $this->row_offset = $offset;
        return $this;
    }

    public function setRowColor($row_color)
    {
        $this->row_color = $row_color;
        return $this;
    }

    /**
     * Excelファイルを生成する
     *
     * @return \PhpChecklist\Libs\File\Output\ExcelBuilder
     */
    public function build()
    {
        foreach ($this->data_source->getParts() as $i => $part) {
            if (! $part->hasCheckListSection())
                continue;
            $this->renderSheet($part, $i);
        }
        return $this;
    }

    /**
     * チェックリストのシートをレンダリングする
     *
     * @param PhpChecklist\Libs\Doc\Part $part            
     * @param number $i            
     */
    private function renderSheet(PhpChecklist\Libs\Doc\Part $part, $i)
    {
        $this->book->createSheet()->setTitle($part->getCaption());
        $sheet = $this->book->setActiveSheetIndexByName($part->getCaption());
        
        $this->book->getDefaultStyle()
            ->getFont()
            ->setSize(9)
            ->setName('ＭＳ ゴシック');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        foreach ($this->columns as $j => $column) {
            $this->setupColumns($sheet, $j + 1, $column);
        }
        
        // $sheet->setCellValueByColumnAndRow(0, $row, $part->getSequence());
        // $sheet->setCellValueByColumnAndRow(1, $row, $part->toString());
        
        // 行を出力
        // 行の高さは自動的に決まるが、そのままだと下部の余白がゼロになってしまうため、改行を加えて余白を表現するようにした。
        // 行の高さを明示的に指定するのは、自動調整された高さを取得することができなかったため断念した。
        $row = $this->getRowPosition(2);
        foreach ($part->getCheckItems() as $j => $item) {
            $sheet->setCellValueByColumnAndRow($this->getColumnPosition(1), $row, $part->getSequence() . '-' . $item->getSequence());
            $sheet->setCellValueByColumnAndRow($this->getColumnPosition(2), $row, $item->getCaption() . "\n");
            if (! empty($item->getProcedure())) {
                
                $procedure_text = '';
                if (! empty($item->getNote()))
                    $procedure_text .= $item->getNote()->toString() . "\n\n";
                $procedure_text .= $item->getProcedure()->toString();
                $procedure_tmp = new Procedure($procedure_text);
                
                $sheet->setCellValueByColumnAndRow($this->getColumnPosition(3), $row, $procedure_tmp->toString() . "\n");
                if ($procedure_tmp->isNeedPrefixWithQuote())
                    $sheet->getStyleByColumnAndRow($this->getColumnPosition(3), $row)
                        ->setQuotePrefix(true);
            }
            if (! empty($item->getExpects())) {
                $sheet->setCellValueByColumnAndRow($this->getColumnPosition(4), $row, $item->getExpects()
                    ->toString() . "\n");
                if ($item->getExpects()->isNeedPrefixWithQuote())
                    $sheet->getStyleByColumnAndRow($this->getColumnPosition(4), $row)
                        ->setQuotePrefix(true);
            }
            
            $color = $this->row_color->getColor($item->getHeader()->getTags()->toArray());
            $sheet->getStyleByColumnAndRow($this->getColumnPosition(1), $row, $this->getColumnPosition(7), $row)
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB($color);

            // $sheet->getStyleByColumnAndRow(1, $row)->getAlignment()->setIndent(1);
            $row = $row + 1;
        }
        
        // 列幅調整
        /*
         * $sheet->getColumnDimensionByColumn(1)->setAutoSize(true);
         * $sheet->calculateColumnWidths(1);
         * $sheet->getColumnDimensionByColumn(1)->setAutoSize(false);
         * $width = $sheet->getColumnDimensionByColumn(1)->getWidth();
         * $sheet->getColumnDimensionByColumn(1)->setWidth($width * 1.5);
         */
        // テキストを折り返す
        $sheet->getStyleByColumnAndRow($this->getColumnPosition(2), $this->getRowPosition(2), $this->getColumnPosition(4), 1000)
            ->getAlignment()
            ->setWrapText(true);
        // 罫線
        $sheet->getStyleByColumnAndRow($this->getColumnPosition(1), $this->getRowPosition(1), $this->getColumnPosition(7), $row)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

    }

    public function save()
    {
        $this->validate();
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->book, 'Xlsx');
        $writer->save($this->file_path);
    }

    private function validate()
    {
        if (empty($this->file_path))
            throw new \Exception('Missing file path.');
    }

    /**
     * 列ごとの設定を反映
     *
     * @param
     *            \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     * @param number $i            
     * @param array $column            
     */
    private function setupColumns(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, $i, $column)
    {
        $sheet->setCellValueByColumnAndRow($this->getColumnPosition($i), $this->getRowPosition(1), $column['text']);
        if (array_key_exists('width', $column['options'])) {
            $sheet->getColumnDimensionByColumn($this->getColumnPosition($i))
                ->setWidth($column['options']['width']);
        }
        // セルの色を設定
        $sheet->getStyleByColumnAndRow($this->getColumnPosition($i), $this->getRowPosition(1))
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('D9D9D9');
        
        // セルの縦揃えを設定
        if (array_key_exists('verticalAlign', $column['options']) && in_array($column['options']['verticalAlign'], [
            'top',
            'center',
            'bottom'
        ])) {
            $sheet->getStyleByColumnAndRow($this->getColumnPosition($i), $this->getRowPosition(2), $this->getColumnPosition($i), 1000)
                ->getAlignment()
                ->setVertical($this->getStyleVerticalAlignment($column['options']['verticalAlign']));
        } else {
            $sheet->getStyleByColumnAndRow($this->getColumnPosition($i), $this->getRowPosition(2), $this->getColumnPosition($i), 1000)
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        }
    }

    /**
     * オフセットを加味した列の開始位置を取得する
     *
     * @param number $i            
     * @return number
     */
    private function getColumnPosition($i)
    {
        return $i + $this->column_offset;
    }

    /**
     * オフセットを加味した行の開始位置を取得する
     *
     * @param number $i            
     * @return number
     */
    private function getRowPosition($i)
    {
        return $i + $this->row_offset;
    }

    private function getStyleVerticalAlignment($str)
    {
        switch ($str) {
            case 'top':
                return \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP;
            case 'center':
                return \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
            case 'bottom':
                return \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM;
        }
    }
}