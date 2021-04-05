<?php
namespace Usage;

use Stan\Stan;

class Date {

    /**
     * Get The Difference between timestamp and now
     *
     * @param int $date
     * @return array
     */
    public static function difference(int $date) : array
    {
        $diff = abs(time() - $date);
        $return = array();
        $tmp = $diff;
        $return['second'] = $tmp % 60;
        $tmp = floor(($tmp - $return['second']) / 60);
        $return['minute'] = $tmp % 60;
        $tmp = floor(($tmp - $return['minute']) / 60);
        $return['hour'] = $tmp % 24;
        $tmp = floor(($tmp - $return['hour']) / 24);
        $return['day'] = $tmp;
        return $return;
    }

    public static function formatTime(int $second){
        $return = array();
        $tmp = $second;
        $return['second'] = $tmp % 60;
        $tmp = floor(($tmp - $return['second']) / 60);
        $return['minute'] = $tmp % 60;
        $tmp = floor(($tmp - $return['minute']) / 60);
        $return['hour'] = $tmp % 24;
        $tmp = floor(($tmp - $return['hour']) / 24);
        $return['day'] = $tmp;
        return $return;
    }

    /**
     * Format a Timestamp
     *
     * @param int $time
     * @return string
     */
    public static function format(int $time, string $prefix = "il y a") : string
    {
        $months = array(
            'January' => 'Janv',
            'February' => 'Fév',
            'March' => 'Mars',
            'April' => 'Avril',
            'May' => 'Mai',
            'June' => 'Juin',
            'July' => 'Juillet',
            'August' => 'Août',
            'September' => 'Sept',
            'October' => 'Oct',
            'November' => 'Nov',
            'December' => 'Déc'
        );

        $date = self::difference($time);
        $stan = Stan::getInstance();

        switch ($stan->configs->config->get("LANGUAGE_CODE")) {
            case 'fr':
                if (!empty($date['day']) && $date['day'] != 1) {
                    $dates  = strftime(" %d %B %Y", $time);
                    $return = strtr($dates, $months);
                }
                elseif (!empty($date['day']) && $date['day'] = 1) {
                    $return = 'Hier à ' . date('H:i', $time);
                }
                elseif (!empty($date['hour'])) {
                    $return = $prefix. ' ' . $date['hour'] . ' heures';
                }
                elseif (!empty($date['minute'])) {
                    $return = $prefix. ' ' . $date['minute'] . ' minutes';
                }
                elseif (!empty($date['second'])) {
                    $return = $prefix. ' ' . $date['second'] . ' secondes';
                }
                else {
                    $dates  = strftime(" %d %B %Y", $time);
                    $return = strtr($dates, $months);
                }
                return $return;
                break;
            case 'en':
                $date = strftime(" %d.%b %Y", $time);
                return $date;
                break;
            default:
                $date = strftime(" %d.%b %Y", $time);
                return $date;
        }
    }

    public static function counter(int $second)
    {
        $date = self::formatTime($second);
        $return = "";

        if (!empty($date['day']) && $date['day'] != 1) {
            $return .= $date['day'] . " jours, ";
        }

        if (!empty($date['day']) && $date['day'] = 1) {
            $return .= $date['day'] . " jour, ";
        }

        if (!empty($date['hour'])) {
            $return .= $date['hour'] . " heures, ";
        }

        if (!empty($date['minute'])) {
            $return .= $date['minute'] . " minutes ";
        }

        if (!empty($date['second'])) {
            $return .= "et " . $date['second'] . " secondes ";
        }

        return $return;
    }
}
