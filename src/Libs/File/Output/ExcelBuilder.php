<?php
namespace PhpChecklist\Libs\File\Output;

use PhpChecklist;

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

    private $header_row = 1;

    public function __construct(PhpChecklist\Libs\Doc\Root $root)
    {
        $this->book = new \PHPExcel();
        $this->data_source = $root;
    }

    /**
     * 列の設定を追加する
     * 
     * チェックリストのシートの列の定義を設定する。
     * 
     * @param string $text 列のタイトル
     * @param array $options 列の設定
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
        // 1枚目のシートはBookを生成した際に既に存在するため、2枚目以降からSheetの追加処理を行う
        if ($i == 0) {
            $sheet = $this->book->getActiveSheet();
            $sheet->setTitle($part->getCaption());
        } else {
            $this->book->createSheet()->setTitle($part->getCaption());
            $sheet = $this->book->setActiveSheetIndexByName($part->getCaption());
        }
        
        $sheet->getDefaultStyle()
            ->getFont()
            ->setSize(9);
        $sheet->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        foreach ($this->columns as $j => $column) {
            $this->setupColumns($sheet, $j, $column);
        }
        
        // $sheet->setCellValueByColumnAndRow(0, $row, $part->getSequence());
        // $sheet->setCellValueByColumnAndRow(1, $row, $part->toString());
        $row = $this->getRowPosition(2);
        foreach ($part->getCheckItems() as $j => $item) {
            $sheet->setCellValueByColumnAndRow($this->getColumnPosition(0), $row, $part->getSequence() . '-' . $item->getSequence());
            $sheet->setCellValueByColumnAndRow($this->getColumnPosition(1), $row, $item->getCaption());
            $sheet->setCellValueByColumnAndRow($this->getColumnPosition(2), $row, $item->getProcedure()
                ->toString());
            $sheet->setCellValueByColumnAndRow($this->getColumnPosition(3), $row, $item->getExpects()
                ->toString());
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
        $sheet->getStyleByColumnAndRow($this->getColumnPosition(1), $this->getRowPosition(2), $this->getColumnPosition(3), 1000)
            ->getAlignment()
            ->setWrapText(true);
        // 罫線
        $sheet->getStyleByColumnAndRow($this->getColumnPosition(0), $this->getRowPosition(1), $this->getColumnPosition(6), $row)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
    }

    public function save()
    {
        $this->validate();
        $writer = \PHPExcel_IOFactory::createWriter($this->book, 'Excel2007');
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
     * @param \PHPExcel_Worksheet $sheet
     * @param number $i
     * @param array $column
     */
    private function setupColumns(\PHPExcel_Worksheet $sheet, $i, $column)
    {
        $sheet->setCellValueByColumnAndRow($this->getColumnPosition($i), $this->getRowPosition(1), $column['text']);
        if (array_key_exists('width', $column['options'])) {
            $sheet->getColumnDimensionByColumn($this->getColumnPosition($i))
                ->setWidth($column['options']['width']);
        }
        // セルの色を設定
        $sheet->getStyleByColumnAndRow($this->getColumnPosition($i), $this->getRowPosition(1))
            ->getFill()
            ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('AAAAAA');
        
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
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
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
                return \PHPExcel_Style_Alignment::VERTICAL_TOP;
            case 'center':
                return \PHPExcel_Style_Alignment::VERTICAL_CENTER;
            case 'bottom':
                return \PHPExcel_Style_Alignment::VERTICAL_BOTTOM;
        }
    }
}