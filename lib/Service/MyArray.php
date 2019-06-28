<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Service;

class MyArray
{
    public static function stringToArray(string $string = null, string $sep = '/') :array
    {
        if ($string === null) {
            die('Error MyArray::stringToArray -> $string === null');
        }

        $array = explode($sep, $string);

        return $array;
    }

    public static function clearArray(array $array, string $sep = '', bool $strictMode = true, bool $reIndex = true) :array
    {
        foreach ($array as $key => $val) {
            if ($strictMode && ($val === $sep)) {
                unset($array[$key]);
                continue;
            }
            
            if ($val == $sep) {
                unset($array[$key]);
            }
        }

        if ($reIndex) {
            $array = array_values($array);
        }

        return $array;
    }

    public static function stringToAssocArray(string $string, string $sep1 = '&', string $sep2 = '=')
    {
        $key = 0;
        $array = array();
        $tempArray = explode($sep1, $string);
        foreach ($tempArray as $string) {
            $temp = explode($sep2, $string);
            $array[($temp[0] ?? $key)] = ($temp[1] ?? null);
            $key++;
        }
        return $array;
    }
}
