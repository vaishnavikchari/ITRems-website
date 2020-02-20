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

require_once('CTC_SortTransform.php');

class CTC_NaturalCTCSortByMultiField extends CTC_SortTransform {

    var $fields = array();

    // todo: replace with splat operator when PHP 5.6 is minimal version
    function __construct($fieldName0,
                         $fieldName1 = null,
                         $fieldName2 = null,
                         $fieldName3 = null,
                         $fieldName4 = null,
                         $fieldName5 = null,
                         $fieldName6 = null,
                         $fieldName7 = null,
                         $fieldName8 = null,
                         $fieldName9 = null) {
        $this->fields[0] = $fieldName0;
        $this->fields[1] = $fieldName1;
        $this->fields[2] = $fieldName2;
        $this->fields[3] = $fieldName3;
        $this->fields[4] = $fieldName4;
        $this->fields[5] = $fieldName5;
        $this->fields[6] = $fieldName6;
        $this->fields[7] = $fieldName7;
        $this->fields[8] = $fieldName8;
        $this->fields[9] = $fieldName9;
    }

    public function sort($a, $b) {
        $result = 0;
        for ($idx = 0; $idx <= 9; ++$idx) {
            if ($result == 0 && $this->fields[$idx]) {
                $result = strnatcmp($a[$this->fields[$idx]], $b[$this->fields[$idx]]);
                if ($result != 0) {
                    break;
                }
            }
        }
        return $result;
    }
}