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
require_once('CTC_PostDataConverter.php');

class CTC_ShortCodeSaveFormMakerSubmission extends CTC_ShortCodeLoader {

    const FORM_TITLE_FIELD = 'form_title';
    const DEFAULT_FORM_TITLE = 'Form Maker';


    /**
     * @param $atts array (associative) of shortcode inputs
     * @param $content string inner content of short code
     * @return string shortcode content
     */
    public function handleShortcode($atts, $content = null) {
        $converter = new CTC_PostDataConverter();
        $converter->addExcludeField(self::FORM_TITLE_FIELD);
        $title = isset($_POST[self::FORM_TITLE_FIELD]) ? sanitize_text_field($_POST[self::FORM_TITLE_FIELD]) : self::DEFAULT_FORM_TITLE;
        $data = $converter->convert($title);
        if ($data) {
            // Call hook to submit data
            do_action_ref_array('cfdb_submit', array(&$data));
        }
    }

}