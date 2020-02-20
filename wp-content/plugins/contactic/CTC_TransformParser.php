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

require_once('CTC_ParserBase.php');
require_once('CTC_TransformByFunctionIterator.php');
require_once('CTC_TransformByClassIterator.php');
require_once('CTC_TransformEndpoint.php');

// Includes these just to have them as known classes in case they are in the transform.
require_once('CTC_SortByField.php');
require_once('CTC_SortByMultiField.php');
require_once('CTC_NaturalSortByField.php');
require_once('CTC_NaturalSortByMultiField.php');
require_once('CTC_SortByDateField.php');
require_once('CTC_SummationRow.php');

require_once('CTC_CountField.php');
require_once('CTC_CountInField.php');
require_once('CTC_DefaultField.php');
require_once('CTC_SplitField.php');
require_once('CTC_SumField.php');
require_once('CTC_MinField.php');
require_once('CTC_MaxField.php');
require_once('CTC_AverageField.php');
require_once('CTC_TotalField.php');
require_once('CTC_AddRowNumberField.php');

require_once('CTC-transform-functions.php');

class CTC_TransformParser extends CTC_ParserBase {

    var $tree = array();

    /**
     * @var array[CTC_DataIterator]
     */
    var $transformIterators = array();


    public function getExpressionTree() {
        return $this->tree;
    }

    public function parse($string) {
        $arrayOfANDedStrings = $this->parseANDs($string); // e.g. "xx=yy()&&zz()" -> ["xx=yy(a,b,c)", "zz"]
        foreach ($arrayOfANDedStrings as $expressionString) {
            $rawExpression = $this->parseExpression(trim($expressionString)); // e.g. ["xx" "=" "yy(a,b,c)"] or ["zz"]
            if (empty($rawExpression)) {
                continue;
            }
            $expression = array();
            $function = null;
            if (count($rawExpression) >= 3) { // e.g. ["xx" "=" "yy(a,b,c)"]
                $expression[] = trim($rawExpression[0]); // field name
                $expression[] = trim($rawExpression[1]); // =
                $function = trim($rawExpression[2]); // function call
            } else {
                $function = trim($rawExpression[0]); // function call
            }
            $function = $this->parseValidFunctionOrClassTransform($function); // ["zz(a,b,c)"] -> ["zz", "a", "b", "c"]
            if (is_array($function)) {
                $expression = array_merge($expression, $function);
            } else {
                $expression[] = $function;
            }
            $this->tree[] = $expression;
        }
    }


    /**
     * Parse a comparison expression into its three components
     * @param  $comparisonExpression string in the form 'value1' . 'operator' . 'value2' where
     * operator is a php comparison operator or '='
     * @return array of string [ value1, operator, value2 ]
     */
    public function parseExpression($comparisonExpression) {
        return preg_split('/(=)/', $comparisonExpression, -1,
                PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }


    public function setupTransforms() {
        if ($this->tree) {
            /** @var $previousTransformIterator CTC_DataIteratorDecorator */
            $previousTransformIterator = null;
            foreach ($this->tree as $transformArray) {
                // [field, =, func, a1, a2, ...] or [func] or [class] or [class, a1, a2, ...]
                $transform = null;
                if (!empty($transformArray)) {
                    $transform = null;
                    if (class_exists($transformArray[0])) {
                        $reflect = new ReflectionClass($transformArray[0]);
                        $args = array_slice($transformArray, 1);
                        $instance = $reflect->newInstanceArgs($args);
                        $transform = new CTC_TransformByClassIterator();
                        /** @var $instance CTC_Transform */
                        $transform->setTransformObject($instance);
                    } else {
                        // assume it is a function
                        $transform = new CTC_TransformByFunctionIterator();
                        $transform->setFunctionEvaluator($this->functionEvaluator);
                        if (count($transformArray) > 1 && $transformArray[1] == '=') {
                            // [field_name, =, function_name, arg1, arg2, ...]
                            $transform->setFieldToAssign($transformArray[0]);
                            $transform->setFunctionArray(array_slice($transformArray, 2));
                        } else {
                            // [function_name, arg1, arg2, ...]
                            $transform->setFunctionArray($transformArray);
                        }
                    }
                    // Set the data source for each transform as the previous transform
                    // to set up a pipeline/decorator pattern.
                    // The first transform is left with null data source to be hooked up later
                    // to query results.
                    $transform->setSource($previousTransformIterator); // is null for first one
                    $previousTransformIterator = $transform;
                    $this->transformIterators[] = $transform;
                }
            }
            if ($previousTransformIterator) {
                // Stick a CFDBTransformEndpoint at the end of the list of transforms
                $transform = new CTC_TransformEndpoint();
                $transform->setSource($previousTransformIterator);
                $this->transformIterators[] = $transform;
            }
        }
    }

    /**
     * @param $dataSource CTC_DataIterator
     */
    public function setDataSource($dataSource) {
        if (count($this->transformIterators) > 0) {
            $this->transformIterators[0]->setSource($dataSource);
        }
    }

    /**
     * @return CTC_DataIteratorDecorator
     */
    public function getIterator() {
        return end($this->transformIterators);
    }

}