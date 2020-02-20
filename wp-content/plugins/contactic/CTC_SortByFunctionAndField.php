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

require_once('CTC_SortTransform.php');
require_once('CTC_PermittedFunctions.php');

class CTC_SortByFunctionAndField extends CTC_SortTransform {

    var $functionName;
    var $fieldName;
    var $reverse;
    var $functionPermitted;

    /**
     * @param $functionName string name of sort function such as strcmp, strcasecmp, strnatcmp
     * @param $fieldName
     * @param $ascDesc string 'ASC' or 'DESC'
     */
    function __construct($functionName, $fieldName, $ascDesc = 'ASC') {
        $this->functionName = $functionName;
        $this->fieldName = $fieldName;
        $this->reverse = strtoupper($ascDesc) == 'DESC';
        $this->functionPermitted = CTC_PermittedFunctions::getInstance()->isFunctionPermitted($functionName);
    }

    /**
     * @param $a array associative containing $this->field_name
     * @param $b array associative containing $this->field_name
     * @return int -1 if $a>$b, 0 if equal, 1 if $a<$b
     */
    public function sort($a, $b) {
        if (!$this->functionPermitted) {
            trigger_error('Function not permitted by CFDB: ' . $this->functionName, E_USER_NOTICE);
            return 0;
        }

        if (!isset($a[$this->fieldName]) || !isset($b[$this->fieldName])) {
            return 0; // ambiguous due to field missing
        }

        $result = call_user_func_array($this->functionName, array($a[$this->fieldName], $b[$this->fieldName]));
        if ($this->reverse) {
            $result *= -1;
        }
        return $result;
    }


} 