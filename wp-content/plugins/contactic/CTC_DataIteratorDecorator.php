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

require_once('CTC_DataIterator.php');

abstract class CTC_DataIteratorDecorator extends CTC_DataIterator {

    /**
     * @var CTC_DataIterator
     */
    var $source;

    /**
     * @param $source CTC_DataIterator
     */
    public function setSource($source) {
        $this->source = $source;
    }

    public function getDisplayColumns() {
        if (empty($this->displayColumns)) {
            return $this->source->getDisplayColumns();
        }
        return $this->displayColumns;
    }

}