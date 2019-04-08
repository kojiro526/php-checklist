<?php
namespace PhpChecklist\Libs\Option;

/**
 * コマンドラインの`--color`オプションに対応するクラス
 *
 * @author sasaki
 *        
 */
class RowColor
{

    private $raw = '';

    private $options = [];

    /**
     * `--color`オプションで与えられたパラメータをセットする。
     *
     * 以下はデフォルトをグレーにし、「必須」項目として`.required`クラスを
     * 指定した行のみ白にする。
     *
     * `--color="default=AAAAAA,.required=FFFFFF"`
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

    public function getDefault()
    {
        return $this->getColor('default');
    }

    /**
     * 各試験項目に指定されたHeader Identifierに基づいて、最終的な行の色を返却する
     *
     * 行にはHeader Identifierとして複数のIDやクラスが指定される。
     *
     * 以下の例では`.required`と`.minimum`というクラスが設定されている。
     *
     * > 例： # header { .required .minimum }
     *
     * 上記に対して`--color=default="AAAAAA,.required=FFFFFF,.minimum=FF0000"`という
     * オプションが与えられた場合、その行に提供される色は`FF0000`となる。
     *
     * @param string|array $key
     *            一つまたは複数のKey
     *            （Header Identifierに指定されたすべてのID、Classが渡されることを想定している）
     * @return string
     */
    public function getColor($key)
    {
        if (is_array($key)) {
            $color = $this->getDefault();
            foreach ($key as $i => $tag) {
                if (array_key_exists($tag, $this->options)) {
                    $color = $this->options[$tag];
                }
            }
            return $color;
        }
        
        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }
        
        return '';
    }
}
