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

require_once('CTC_ShortCodeLoader.php');

class CTC_ShortcodeJson extends CTC_ShortCodeLoader {

    /**
     * @param  $atts array of short code attributes
     * @param $content string inner content of short code
     * @return string JSON. See CTC_ExportToJson.php
     */
    public function handleShortcode($atts, $content = null) {
        if (isset($atts['form'])) {
            $atts = $this->decodeAttributes($atts);
            $atts['content'] = $content;
            $atts['html'] = true;
            $atts['fromshortcode'] = true;

            require_once('CTC_DereferenceShortcodeVars.php');
            $deref = new CTC_DereferenceShortcodeVars;
            $form = $deref->convert($atts['form']);

            require_once('CTC_ExportToJson.php');
            $export = new CTC_ExportToJson();
            return $export->export($form, $atts);
        }
        return '';
    }
}
