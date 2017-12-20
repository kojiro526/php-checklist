<?php
namespace PhpChecklist\Libs\Doc;

class CheckListItem extends Section
{

    private $subsection_procedure = '手順';

    private $subsection_expects = '確認';

    private $caption;

    private $note;

    private $procedure;

    private $expects;

    public function __construct($text)
    {
        parent::__construct($text);
        $this->parse();
    }

    public function getProcedure()
    {
        return $this->procedure;
    }

    public function getExpects()
    {
        return $this->expects;
    }

    /*
     * public function getCaption(){
     * return $this->caption;
     * }
     */
    public function toString()
    {
        return $this->raw_text;
    }

    public function setProcedureText($string)
    {
        $this->subsection_procedure = $string;
    }

    public function setExpectsText($string)
    {
        $this->subsection_expects = $string;
    }

    private function getProcedureText()
    {
        return $this->subsection_procedure;
    }

    private function getExpectsText()
    {
        return $this->subsection_expects;
    }

    /**
     * 入力された行からキャプションをパースする
     *
     * パースできなければnullを返す
     *
     * @param string $line            
     * @return string|NULL
     */
    private function parseCaption($line)
    {
        if (preg_match('/^### +(.*)/', $line, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * 入力された行からサブセクションのテキストをパースする
     *
     * パースできなければnullを返す
     *
     * @param string $line            
     * @return string|NULL
     */
    private function parseSubsectionHeader($line)
    {
        if (preg_match('/^#### +(.*)/', $line, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * 試験項目をパースする
     *
     * - 1行目は試験項目のキャプションがレベル3の見出しで指定されていることを期待する。
     * - 試験項目の記述の仕方は以下の通り。
     * - 試験項目内にサブセクションが無い場合は、試験手順と期待する動作をパースする。
     * - この場合、期待する動作はMarkdownのチェックリストの書式で書かれた部分を抜き出す。
     * - 試験項目内にサブセクションがある場合は、サブセクション毎にパースする。
     */
    private function parse()
    {
        $lines = $this->splitContent();
        
        // $this->caption = $this->parseCaption($lines[0]);
        // if (! empty($this->caption))
        // array_shift($lines);
        
        $text = '';
        $note = null;
        $subsections = [];
        $subsection_count = 0;
        foreach ($lines as $line) {
            if ($this->isSubsectionHeading($line)) {
                $subsection_count ++;
                if (! empty($text)) {
                    if ($subsection_count == 1) {
                        $note = new Text($text);
                    } else {
                        array_push($subsections, new Section($text));
                    }
                    $text = '';
                }
            }
            $text .= $line . "\n";
        }
        
        if (! empty($text)) {
            if ($subsection_count == 0) {
                $note = new Text($text);
            } else {
                array_push($subsections, new Section($text));
            }
        }
        
        if (empty($note) && count($subsections) == 0)
            return true;
        if (! empty($note) && count($subsections) == 0) {
            $this->parseWithoutSubsection($note);
            return true;
        }
        $this->parseWithSubsection($subsections, $note);
    }

    private function parseWithSubsection(array $subsections, Text $text = null)
    {
        switch ($subsections[0]->getCaption()) {
            case $this->getProcedureText():
                if (! empty($text)) {
                    $this->note = new Note($text->getRawText());
                }
                break;
            case $this->getExpectsText():
                if (! empty($text)) {
                    $this->procedure = new Procedure($text->getRawText());
                }
                break;
            default:
        }
        
        foreach ($subsections as $subsection) {
            switch ($subsection->getCaption()) {
                case $this->getProcedureText():
                    $this->procedure = new Procedure($subsection->getRawContent());
                    break;
                case $this->getExpectsText():
                    $this->expects = new Expects($subsection->getRawContent());
                    break;
                default:
            }
        }
    }

    private function parseWithoutSubsection(Text $text)
    {
        $procedure_text = '';
        $expects_text = '';
        $is_expects_start = false;
        foreach ($text->split() as $line) {
            if ($this->isExpectsStart($line))
                $is_expects_start = true;
            
            if($is_expects_start){
                $expects_text .= $line . "\n";
            }else{
                $procedure_text .= $line . "\n";
            }
        }
        
        if(!empty($procedure_text))
            $this->procedure = new Procedure($procedure_text);
        if(!empty($expects_text))
            $this->expects = new Procedure($expects_text);
    }

    private function isExpectsStart($line)
    {
        return ! empty(preg_match('/^- \[ \] /', $line));
    }

    /**
     * チェック項目がサブセクションを持っているかどうかを判定する。
     *
     * @param array $lines            
     * @return boolean
     */
    private function hasSubsection($lines)
    {
        foreach ($lines as $line) {
            if ($this->isSubsectionHeader($line))
                return true;
        }
        return false;
    }

    /**
     * 入力された行がサブセクションの見出しか判定する
     *
     * @param string $line            
     * @return boolean
     */
    public function isSubsectionHeading($line)
    {
        return ! empty(preg_match('/^#### /', $line));
    }
}