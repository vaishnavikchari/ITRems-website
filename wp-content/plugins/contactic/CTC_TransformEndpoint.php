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

require_once('CTC_AbstractQueryResultsIterator.php');
require_once('CTC_DataIteratorDecorator.php');

class CTC_TransformEndpoint extends CTC_DataIteratorDecorator {

    /**
     * @var CTC_TransformEndpointQueryResultsIterator
     */
    var $postProcessor;

    function __construct() {
       $this->postProcessor = new CTC_TransformEndpointQueryResultsIterator($this);
    }

    /**
     * Fetch next row into variable
     * @return bool if next row exists
     */
    public function nextRow() {
        if ($this->postProcessor->nextRow()) {
            $this->row =& $this->source->row;
            if (empty($this->displayColumns)) {
                $this->displayColumns =& $this->source->displayColumns;
            }
            return true;
        } else {
            $this->row = null;
            return false;
        }
    }

    /**
     * @return CTC_TransformEndpointQueryResultsIterator
     */
    public function getPostProcessor() {
        return $this->postProcessor;
    }
}


class CTC_TransformEndpointQueryResultsIterator extends CTC_AbstractQueryResultsIterator {

    /**
     * @var CTC_TransformEndpoint
     */
    var $endPoint;

    function __construct($endPoint) {
        $this->endPoint = $endPoint;
    }

    /**
     * Execute the query
     * @param $sql string query
     * @param $queryOptions array associative
     * @return void
     */
    public function queryDataSource(&$sql, $queryOptions) {
        // Do nothing. Data is in $this->$endPoint->source
    }

    /**
     * Get the next row from query results
     * @return array associative
     */
    public function fetchRow() {
        if($this->endPoint->source->nextRow()) {
            return $this->endPoint->source->row;
        }
        return null;
    }

    /**
     * @return boolean
     */
    public function hasResults() {
        // this is called by nextRow() in superclass
        // return true and let next row sort it out
        return true;
    }

    /**
     * If you do not iterate over all the rows returned, be sure to call this function
     * on all remaining rows to free resources.
     * @return void
     */
    public function freeResult() {
        // Do nothing
    }
}