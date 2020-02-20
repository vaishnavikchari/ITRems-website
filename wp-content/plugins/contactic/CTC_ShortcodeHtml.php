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

class CTC_ShortcodeHtml extends CTC_ShortCodeLoader {

    /**
     * @param $atts array of short code attributes
     * @param $content string contents inside the shortcode tag
     * @return string value submitted to a form field as selected by $atts. See CTC_ExportToValue.php
     */
    public function handleShortcode($atts, $content = null) {
        if ($content && isset($atts['form'])) {
            $atts = $this->decodeAttributes($atts);
            $atts['fromshortcode'] = true;
            $atts['content'] = $content;

            require_once('CTC_DereferenceShortcodeVars.php');
            $deref = new CTC_DereferenceShortcodeVars;
            $form = $deref->convert($atts['form']);

            require_once('CTC_ExportToHtmlTemplate.php');
            $export = new CTC_ExportToHtmlTemplate();
            return $export->export($form, $atts);
        }
        return '';
    }

}
