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

require_once('CTC_WpdbResultIterator.php');
require_once('CTC_WpdbUnbufferedResultIterator.php');

/**
 * @singleton
 */
class CTC_QueryResultIteratorFactory {

    /**
     * @var CTC_AbstractQueryResultsIterator mock instance
     */
    var $mock;

    public static function getInstance() {
        static $inst = null;
        if ($inst === null) {
            $inst = new CTC_QueryResultIteratorFactory();
        }
        return $inst;
    }

    /**
     * @param $mock CTC_AbstractQueryResultsIterator mock for CTC_QueryResultIterator
     */
    public function setQueryResultsIteratorMock($mock) {
        $this->mock = $mock;
    }

    public function clearMock() {
        $this->mock = null;
    }

    /**
     * Factory method for getting a new CTC_QueryResultIterator or mock.
     * @param $unbuffered bool
     * @return CTC_AbstractQueryResultsIterator (or mock)
     */
    public function newQueryIterator($unbuffered = false) {
        if ($this->mock) {
            return $this->mock;
        }
        if ($unbuffered) {
          return new CTC_WpdbUnbufferedResultIterator;
        } else {
            return new CTC_WpdbResultIterator;
        }
    }

} 