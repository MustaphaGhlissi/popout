<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 05/03/2018
 * Time: 17:13
 */

namespace App\Utils;


class Util
{
    public static function newLines(int $nbLines)
    {
        $goTo = "";
        for($i = 0; $i < $nbLines; $i++):
        {
            $goTo .= "\r\n";
        }endfor;
        return $goTo;
    }
}