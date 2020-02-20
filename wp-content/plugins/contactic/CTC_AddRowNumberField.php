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

/**
 * Transform to add a column that numbers the rows
 */
class CTC_AddRowNumberField extends CTC_BaseTransform {

    var $fieldName;
    var $start;

    function __construct($fieldName = '#', $start = 1) {
        $this->fieldName = $fieldName;
        $this->start = $start;
    }

    public function getTransformedData() {
        $idx = $this->start;
        foreach ($this->data as &$entry) {
            $entry[$this->fieldName] = $idx++;
        }
        return $this->data;
    }

}
