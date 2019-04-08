<?php
namespace PhpChecklist\Libs\Option;

/**
 * コマンドラインの`--label`オプションに対応するクラス
 *
 * @author sasaki
 *        
 */
class RowLabel
{

    private $raw = '';

    private $options = [];

    /**
     * `--label`オプションで与えられたパラメータをセットする。
     *
     * 以下はデフォルトをグレーにし、「必須」項目として`.required`クラスを
     * 指定した行のみ白にする。
     *
     * `--color=".required=必須,.minimum=最小限"`
     *
     * @param string $option            
     */
    public function __construct($option)
    {
        $this->raw = $option;
        $this->options = KeyValueParser::parse($option);
    }

    public function toArray()
    {
        return $this->options;
    }

    /**
     * 各試験項目に指定されたHeader Identifierに基づいて、対応するラベルを返す
     * 
     * 行にはHeader Identifierとして複数のIDやクラスが指定される。
     *
     * 以下の例では`.required`と`.minimum`というクラスが設定されている。
     *
     * > 例： # header { .required .minimum }
     *
     * 上記に対して`--label=".required=必須,.minimum=最小限"`という
     * オプションが与えられた場合、["必須", "最小限"]のような配列を返却する。
     *
     * @param string|array $key
     * @return array
     */
    public function getLabels($key)
    {
        $labels = [];
        if (is_array($key)) {
            foreach ($key as $i => $tag) {
                if (array_key_exists($tag, $this->options)) {
                    array_push($labels, $this->options[$tag]);
                }
            }
            return $labels;
        }
        
        if (array_key_exists($key, $this->options)) {
            return array_push($labels, $this->options[$key]);
        }
        
        return $labels;
    }
    
    /**
     * ラベルオプションが未設定かどうかを判定する
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return count($this->options) == 0;
    }
}
