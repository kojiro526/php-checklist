<?php
namespace PhpChecklist\Libs\Option;

class KeyValueParser
{

    public static function parse($option)
    {
        $options = [];
        if (is_null($option) || $option == '') return $options;

        $items = explode(',', $option);
        foreach ($items as $i => $item) {
            $key_value = explode('=', $item);
            if (count($key_value) === 2) {
                $options[$key_value[0]] = $key_value[1];
            }
        }
        
        return $options;
    }
}
