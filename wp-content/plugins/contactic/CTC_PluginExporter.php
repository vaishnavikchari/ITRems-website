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

require_once('CTC_Deobfuscate.php');
require_once('CTC_DereferenceShortcodeVars.php');

class CTC_PluginExporter {

    static function doExportFromPost() {

        // Consolidate GET and POST parameters. Allow GET to override POST.
        $params = array_merge($_POST, $_GET);

//        print_r($params);
        foreach ($params as $key => $value) {
            if (is_string($value)) {
                $params[$key] = stripslashes(sanitize_text_field($value));
            }
        }

        if (!isset($params['enc'])) {
            $params['enc'] = 'CSVUTF8';
        }

        $formName = '';
        if (isset($params['form'])) {
            $deref = new CTC_DereferenceShortcodeVars;
            $formName = $deref->convert($params['form']);
        }

        CTC_PluginExporter::export(
                $formName,
                $params['enc'],
                $params);
    }

    static function export($formName, $encoding, $options) {

        switch ($encoding) {
            case 'HTML':
                require_once('CTC_ExportToHtmlTable.php');
                $exporter = new CTC_ExportToHtmlTable();
                $exporter->export($formName, $options);
                break;
            case 'OVERVIEW':
                require_once('CTC_ExportToOverview.php');
                $exporter = new CTC_ExportToOverview();
                $exporter->export($formName, $options);
                break;
            case 'HTMLBOM': // IQY callback
                require_once('CTC_ExportToHtmlTable.php');
                $exporter = new CTC_ExportToHtmlTable();
                $exporter->setUseBom(true);
                $exporter->export($formName, $options);
                break;
            case 'DT':
                require_once('CTC_ExportToHtmlTable.php');
                if (!is_array($options)) {
                    $options = array();
                }
                $options['useDT'] = true;
                if (!isset($options['printScripts'])) {
                    $options['printScripts'] = true;
                }
                if (!isset($options['printStyles'])) {
                    $options['printStyles'] = 'true';
                }
                $exporter = new CTC_ExportToHtmlTable();
                $exporter->export($formName, $options);
                break;
            case 'HTMLTemplate':
                require_once('CTC_ExportToHtmlTemplate.php');
                $exporter = new CTC_ExportToHtmlTemplate();
                $exporter->export($formName, $options);
                break;
            case 'IQY':
                require_once('CTC_ExportToIqy.php');
                $exporter = new CTC_ExportToIqy();
                $exporter->export($formName, $options);
                break;
            case 'CSVUTF8BOM':
                $options['unbuffered'] = 'true';
                require_once('CTC_ExportToCsvUtf8.php');
                $exporter = new CTC_ExportToCsvUtf8();
                $exporter->setUseBom(true);
                $exporter->export($formName, $options);
                break;
            case 'TSVUTF16LEBOM':
                $options['unbuffered'] = 'true';
                require_once('CTC_ExportToCsvUtf16Le.php');
                $exporter = new CTC_ExportToCsvUtf16Le();
                $exporter->export($formName, $options);
                break;
            case 'GLD':
                require_once('CTC_ExportToGoogleLiveData.php');
                $exporter = new CTC_ExportToGoogleLiveData();
                $exporter->export($formName, $options);
                break;
            case 'JSON':
                require_once('CTC_ExportToJson.php');
                $exporter = new CTC_ExportToJson();
                $exporter->export($formName, $options);
                break;
            case 'VALUE':
                require_once('CTC_ExportToValue.php');
                $exporter = new CTC_ExportToValue();
                $exporter->export($formName, $options);
                break;
            case 'COUNT':
                require_once('CTC_ExportToValue.php');
                if (!is_array($options)) {
                    $options = array();
                }
                $options['function'] = 'count';
                unset($options['show']);
                unset($options['hide']);
                $exporter = new CTC_ExportToValue();
                $exporter->export($formName, $options);
                break;
            case 'CSVSJIS':
                require_once('CTC_ExportToCsvUtf8.php');
                $exporter = new CTC_ExportToCsvUtf8();
                $exporter->setUseBom(false);
                $exporter->setUseShiftJIS(true);
                $exporter->export($formName, $options);
                break;
            case 'RSS':
                require_once('CTC_ExportToRSS.php');
                $exporter = new CTC_ExportToRSS();
                $exporter->export($formName, $options);
                break;
            case 'ENTRY':
                require_once('CTC_ExportEntry.php');
                $exporter = new CTC_ExportEntry();
                $exporter->export($formName, $options);
                break;
            case 'xlsx' :
                require_once('CTC_ExportToExcel.php');
                $exporter = new CTC_ExportToExcel();
                $exporter->export($formName, $options);
                break;
            case 'ods' :
                require_once('CTC_ExportToExcel.php');
                $exporter = new CTC_ExportToExcel();
                $options['format'] = 'ods';
                $exporter->export($formName, $options);
                break;
            case 'CSVUTF8':
            default:
                require_once('CTC_ExportToCsvUtf8.php');
                $exporter = new CTC_ExportToCsvUtf8();
                $exporter->setUseBom(false);
                $exporter->export($formName, $options);
                break;
        }
    }
}
