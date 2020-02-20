<?php
/*
    "Contactic" Copyright (C) 2019 Contactic.io - Copyright (C) 2011-2015 Michael Simpson

    This file is part of Contactic.

    Contactic is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Contactic is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contactic.
    If not, see <http://www.gnu.org/licenses/>.
*/

class CTC_Deobfuscate {

    // Taken from http://ditio.net/2008/11/04/php-string-to-hex-and-hex-to-string-functions/
    static function hexToStr($hex) {
        $string = '';
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }

    static function deobfuscateHexString($hex, $key) {
        $hexString = CTC_Deobfuscate::hexToStr($hex);
        return CTC_Deobfuscate::deobfuscateString($hexString, $key);
    }

    static function deobfuscateString($string, $key) {
        if (function_exists('mcrypt_decrypt')) {
            // Although php5-mycrypt may be installed, it may not be listed in
            // php.ini file (like below), thus the function is not defined
            // extension=/usr/lib/php5/20121212/mcrypt.so
            return mcrypt_decrypt(MCRYPT_3DES, $key, $string, 'ecb');
        }
        return '';
    }

}
