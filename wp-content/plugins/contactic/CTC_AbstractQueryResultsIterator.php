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


abstract class CTC_AbstractQueryResultsIterator extends CTC_DataIterator {

    /**
     * @var string
     */
    var $submitTimeKeyName;

    /**
     * @var int
     */
    var $limitEnd;

    /**
     * @var int
     */
    var $idx;

    /**
     * @var int
     */
    var $limitStart;

    /**
     * @var array
     */
    var $columns;

    /**
     * @var ContacticPlugin
     */
    var $plugin;

    /**
     * @var CTC_Evaluator|CTC_FilterParser|CTC_SearchEvaluator
     */
    var $rowFilter;

    /**
     * @var array
     */
//    var $fileColumns;

    /**
     * @var bool
     */
    var $onFirstRow = false;


    /**
     * Execute the query
     * @param $sql string query
     * @param $queryOptions array associative
     * @return void
     */
    abstract public function queryDataSource(&$sql, $queryOptions);

    /**
     * Get the next row from query results
     * @return array associative
     */
    abstract public function fetchRow();


    /**
     * If you do not iterate over all the rows returned, be sure to call this function
     * on all remaining rows to free resources.
     * @return void
     */
    abstract public function freeResult();

    /**
     * @param  $sql string
     * @param  $rowFilter CTC_Evaluator|CTC_FilterParser|CTC_SearchEvaluator
     * @param  $queryOptions array
     */
    public function query(&$sql, $rowFilter, $queryOptions = array()) {
        $this->rowFilter = $rowFilter;
        $this->row = null;
        $this->plugin = new ContacticPlugin();
        $this->submitTimeKeyName = isset($queryOptions['submitTimeKeyName']) ? $queryOptions['submitTimeKeyName'] : null;
        if (isset($queryOptions['limit'])) {
            $limitVals = explode(',', $queryOptions['limit']);
            if (isset($limitVals[1])) {
                $this->limitStart = trim($limitVals[0]);
                $this->limitEnd = (int) $this->limitStart + (int) trim($limitVals[1]);
            } else if (isset($limitVals[0])) {
                $this->limitEnd = trim($limitVals[0]);
            }
        }
        $this->idx = -1;
        $this->queryDataSource($sql, $queryOptions);

        $this->columns = array();
        $this->row = $this->fetchRow();
        if ($this->row) {
            foreach (array_keys($this->row) as $aCol) {
                // hide this metadata column
                if ('fields_with_file' != $aCol) {
                    $this->columns[] = $aCol;
                    $this->displayColumns[] = $aCol;
                }
            }
            $this->onFirstRow = true;
        } else {
            $this->onFirstRow = false;
        }
    }

    /**
     * Fetch next row into variable
     * @return bool if next row exists
     */
    public function nextRow() {
        while (true) {
            if (!$this->onFirstRow) {
                $this->row = $this->fetchRow();
            }
            $this->onFirstRow = false;

            if (!$this->row) {
                $this->freeResult();
                return false;
            }

            // Format the date
            if (!isset($this->row['submit_time']) &&
                    isset($this->row['Submitted']) && is_numeric($this->row['Submitted'])) {
                $submitTime = $this->row['Submitted'];
                $this->row['submit_time'] = $submitTime;
                $this->row['Submitted'] = $this->plugin->formatDate($submitTime);
            }

            // Determine if row is filtered
            if ($this->rowFilter) {
                $match = $this->rowFilter->evaluate($this->row);
                if (!$match) {
                    continue;
                }
            }

            $this->idx += 1;
            if ($this->limitStart && $this->idx < $this->limitStart) {
                continue;
            }
            if ($this->limitEnd && $this->idx >= $this->limitEnd) {
                while ($this->row = $this->fetchRow()) ;
                $this->freeResult();
                $this->row = null;
                return false;
            }

            // Keep the unformatted submitTime if needed
            if (isset($submitTime) && $this->submitTimeKeyName) {
                $this->row[$this->submitTimeKeyName] = $submitTime;
            }
            break;
        }
        if (!$this->row) {
            $this->freeResult();
        }
        return $this->row ? true : false;
    }


}
