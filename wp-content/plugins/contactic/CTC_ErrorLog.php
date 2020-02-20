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

require_once('CTC_DateFormatter.php');

class CTC_ErrorLog {

    /**
     * @var String path to file to write
     */
    var $outputFilePath;

    /**
     * @var String email address
     */
    var $emailAddress;

    /**
     * @var CTC_DateFormatter
     */
    var $dateFormatter;


    function __construct($dateFormatter = null, $destination = null) {
        $this->dateFormatter = $dateFormatter;
        if ($destination) {
            if ($this->isEmailAddress($destination)) {
                $this->emailAddress = $destination;
            } else {
                if (strpos($destination, DIRECTORY_SEPARATOR) === 0) {
                    $this->outputFilePath = $destination;
                } else {
                    $this->outputFilePath = ABSPATH . $destination;
                }
            }
        }
    }

    /**
     * @param $message String
     */
    public function log($message) {
        $date = time();
        if ($this->dateFormatter) {
            $date = $this->dateFormatter->formatDate($date);
        }
        $fullMessage = sprintf("CFDB Error (%s): %s\n", $date, $message);
        if ($this->outputFilePath) {
            error_log($fullMessage, 3, $this->outputFilePath);
        } else if ($this->emailAddress) {
            error_log($fullMessage, 1, $this->emailAddress);
        } else {
            error_log($fullMessage, 0);
        }
    }

    /**
     * @param $ex Exception
     */
    public function logException($ex) {
        $message = sprintf("%s\n\tat %s:%s\n%s", $ex->getMessage(),
                $ex->getFile(), $ex->getLine(),
                "\t" . str_replace("\n", "\n\t", $ex->getTraceAsString()));
        $this->log($message);
    }

    public function isEmailAddress($email) {
        $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}/';
        return (bool)preg_match($pattern, $email);
    }

    /**
     * @return String
     */
    public function getOutputFilePath() {
        return $this->outputFilePath;
    }

    /**
     * @return String
     */
    public function getEmailAddress() {
        return $this->emailAddress;
    }


}