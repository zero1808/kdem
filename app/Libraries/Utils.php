<?php

namespace App\Libraries;

class Utils {

    public static function getStamp() {
        $now = (string) microtime();
        $now = explode(' ', $now);
        $mm = explode('.', $now[0]);
        $mm = $mm[1];
        $now = $now[1];
        $segundos = $now % 60;
        $segundos = $segundos < 10 ? "$segundos" : $segundos;
        return strval(date("YmdHi", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"))) . "$segundos$mm");
    }

}

?>
