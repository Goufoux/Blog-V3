<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Service;

class Convert
{
    const MONTH = [
        '01' => [
            'full' => 'Janvier',
            'abr' => 'Jan'
        ],
        '02' => [
            'full' => 'Février',
            'abr' => 'Fév'
        ],
        '03' => [
            'full' => 'Mars',
            'abr' => 'Mars'
        ],
        '04' => [
            'full' => 'Avril',
            'abr' => 'Avr',
        ],
        '05' => [
            'full' => 'Mai',
            'abr' => 'Mai'
        ],
        '06' => [
            'full' => 'Juin',
            'abr' => 'Juin'
        ],
        '07' => [
            'full' => 'Juillet',
            'abr' => 'Jui'
        ],
        '08' => [
            'full' => 'Août',
            'abr' => 'Août'
        ],
        '09' => [
            'full' => 'Septembre',
            'abr' => 'Sep'
        ],
        '10' => [
            'full' => 'Octobre',
            'abr' => 'Oct'
        ],
        '11' => [
            'full' => 'Novembre',
            'abr' => 'Nov'
        ],
        '12' => [
            'full' => 'Décembre',
            'abr' => 'Déc'
        ]
    ];

    public static function convertDate(\DateTime $dateTime, string $format = 'default', $monthFormat = 'full')
    {
        $formattedDate = '';

        switch ($format) {
            default:
                $formattedDate = $dateTime->format('d') . " " . self::MONTH[$dateTime->format('m')][$monthFormat] . " " . $dateTime->format('Y');
                break;
        }

        return $formattedDate;
    }

    public static function convertInputTimeToMin(string $string)
    {
        if(empty($string)) {
            return null;
        }

        $time = explode(':', $string);

        if(empty($time[0]) || empty($time[1])) {
            return null;
        }

        $time = ($time[0] * 60) + $time[1];
        return $time; 
    }

    public static function convertMinToTime(int $min)
    {
        if($min <= 0) {
            return null;
        }

        $hours = floor($min / 60);
        $mins = ($min % 60);
        if($hours < 10) {
            $hours = '0'.$hours;
        }
        if($mins < 10) {
            $mins = '0'.$mins;
        }
        return $hours.':'.$mins;
    }

}
