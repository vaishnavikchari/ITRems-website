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

/**
 * Interface CTC_Transform for user-defined classes that can be used to transform data
 * as it is returned from the database but before it is is formatted for display.
 * See: short code "trans" option.
 */
interface CTC_Transform {

    /**
     * @param $entry array associative array of a single for entry
     * @return void
     */
    public function addEntry(&$entry);

    /**
     * Call this when done adding entries. Apply transform across all entered data,
     * then return the entire set. The returned set may be entirely different data than
     * what was input (e.g. statistics)
     * @return array of associative of array of data.
     */
    public function getTransformedData();

} 