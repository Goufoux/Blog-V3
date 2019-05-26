<?php

namespace Service;

class Generator
{
    const LETTERS = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];

    public static function password(int $max = 15)
    {
        $temp = array();

        for ($i = 0; $i < $max; $i++) {
            if (rand(0, 1)) {
                $temp[] = rand(0, 9);
            } else {
                $temp[] = self::LETTERS[rand(0, count(self::LETTERS))];
            }
        }

        $password = implode("", $temp);

        return $password;
    }
}
