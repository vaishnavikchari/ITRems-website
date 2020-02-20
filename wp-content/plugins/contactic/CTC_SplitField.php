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

class CTC_SplitField extends CTC_BaseTransform {

    /**
     * @var string
     */
    var $field;

    /**
     * @var string
     */
    var $delimiter;

    /**
     * CTC_SplitField constructor.
     * @param $field string
     * @param $delimiter string
     */
    public function __construct($field, $delimiter = ',') {
        $this->field = $field;
        $this->delimiter = $delimiter;
    }

    public function addEntry(&$entry) {
        if ($this->delimiter && $this->field && isset($entry[$this->field])) {
            $values = explode($this->delimiter, $entry[$this->field]);
            $count = 1;
            foreach ($values as $value) {
                $entry["{$this->field}-$count"] = $value;
                $count++;
            }
            unset($entry[$this->field]);
        }
        $this->data[] = $entry;
    }

}