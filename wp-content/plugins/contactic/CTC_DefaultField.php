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

require_once('CTC_BaseTransform.php');

class CTC_DefaultField extends CTC_BaseTransform {

    var $defaults = array();

    function __construct() {
        $odd = true;
        $key = '';
        foreach (func_get_args() as $arg) {
            if ($odd) {
                $key = $arg;
            } else {
                $this->defaults[$key] = $arg;
            }
            $odd = !$odd;
        }
    }

    public function addEntry(&$entry) {
        foreach ($this->defaults as $key => $theDefault) {
            if (!array_key_exists($key, $entry) || !$entry[$key]) {
                $entry[$key] = $theDefault;
            }
        }
        $this->data[] = $entry;

    }

}