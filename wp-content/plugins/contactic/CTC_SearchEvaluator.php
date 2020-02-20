<?php
/*
    "Contact Form to Database" Copyright (C) 2011-2012 Michael Simpson  (email : michael.d.simpson@gmail.com)

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

include_once('CTC_Evaluator.php');

class CTC_SearchEvaluator implements CTC_Evaluator {

    var $search;
    var $words;
    var $wordCount;

    public function setSearch($search) {
        $this->search = strtolower($search);
        $this->words = explode(' ', $this->search);
        $this->wordCount = count($this->words);
    }

    /**
     * Evaluate expression against input data. This is intended to mimic the search field on DataTables
     * @param  $data array [ key => value]
     * @return boolean result of evaluating $data against expression
     */
    public function evaluate(&$data) {
        if (!$this->search) {
            return true;
        }
        foreach ($data as $key => $value) {
            if ($this->search($value, $this->search)) {
                return true;
            }
        }

        if ($this->wordCount > 1) {
            // treat space in word as delimiter where all words must appear
            $count = 0;
            foreach ($this->words as $word) {
                foreach ($data as $key => $value) {
                    if ($this->search($value, $word)) {
                        $count++;
                        break;
                    }
                }
            }
            if ($count == $this->wordCount) {
                // A match found for each word
                return true;
            }
        }

        return false;
    }

    public function search($haystack, $needle) {
        // Any field can match, case insensitive
        return (false !== strrpos(strtolower($haystack), $needle));
    }

}
