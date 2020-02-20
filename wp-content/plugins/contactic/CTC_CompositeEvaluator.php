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

include_once('CTC_Evaluator.php');

class CTC_CompositeEvaluator implements CTC_Evaluator {

    /**
     * @var CTC_Evaluator[]
     */
    var $evaluators;

    /**
     * @param $evaluators CTC_Evaluator[]
     */
    public function setEvaluators($evaluators) {
        $this->evaluators = $evaluators;
    }

    public function evaluate(&$data) {
        if (is_array($this->evaluators)) {
            foreach ($this->evaluators as $anEvaluator) {
                if (!$anEvaluator->evaluate($data)) {
                    return false;
                }
            }
        }
        return true;
    }

}