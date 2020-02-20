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

require_once('CTC_ExportBase.php');
require_once('CTC_Export.php');
require_once('CTC_ShortCodeContentParser.php');

class CTC_ExportToHtmlTemplate extends CTC_ExportBase implements CTC_Export {

    /**
     * @param $formName string
     * @param $options array of option_name => option_value
     * @return void|string
     */
    public function export($formName, $options = null) {
        $this->setOptions($options);
        $this->setCommonOptions(true);

        $filelinks = '';
        $wpautop = false;
        $stripBR = false;
        $substituteEmptyStringForUnknownFields = false;
        if ($this->options && is_array($this->options)) {
            if (isset($this->options['filelinks'])) {
                $filelinks = $this->options['filelinks'];
            }
            if (isset($this->options['wpautop'])) {
                $wpautop = $this->options['wpautop'] == 'true';
            }
            if (isset($this->options['stripbr'])) {
                $stripBR = $this->options['stripbr'] == 'true';
            }
            if (isset($this->options['unknownfields'])) {
                $substituteEmptyStringForUnknownFields = $this->options['unknownfields'] == 'true';
            }
        }

        // Security Check
        if (!$this->isAuthorized()) {
            $this->assertSecurityErrorMessage();
            return;
        }

        // Headers
        $this->echoHeaders('Content-Type: text/html; charset=UTF-8');


        if (empty($options) || !isset($options['content'])) {
            return;
        }

        // Get the data
        $submitTimeKeyName = 'Submit_Time_Key';
        $this->setDataIterator($formName, $submitTimeKeyName);
        //$this->clearAllOutputBuffers(); // will mess up admin view of single entry

        if ($this->isFromShortCode) {
            ob_start();
        }

        $options['content'] = $this->modifyContent($options['content']);

        $matches = array();
        preg_match_all('/\$\{([^}]+)\}/', $options['content'], $matches);

        $colNamesToSub = array();
        $varNamesToSub = array();
        if (!empty($matches) && is_array($matches[1])) {
            foreach ($matches[1] as $aSubVar) {
                // Each is expected to be a name of a column
                if (in_array($aSubVar, $this->dataIterator->getDisplayColumns())) {
                    $colNamesToSub[] = $aSubVar;
                    $varNamesToSub[] = '${' . $aSubVar . '}';
                }
                elseif ($aSubVar == 'submit_time') {
                    $colNamesToSub[] = 'submit_time';
                    $varNamesToSub[] = '${submit_time}';
                }
                elseif ($aSubVar == '_ctc_referer') {
                    $colNamesToSub[] = '_ctc_referer';
                    $varNamesToSub[] = '${_ctc_referer}';
                }elseif ($aSubVar == '_ctc_last_page_title') {
                    $colNamesToSub[] = '_ctc_last_page_title';
                    $varNamesToSub[] = '${_ctc_last_page_title}';
                }
            }
        }


        // WordPress likes to wrap the content in <br />content<p> which messes up things when
        // you are putting
        //   <tr><td>stuff<td></tr>
        // as the content because it comes out
        //   <br /><tr><td>stuff<td></tr><p>
        // which messed up the table html.
        // So we try to identify that and strip it out.
        // This is related to http://codex.wordpress.org/Function_Reference/wpautop
        // see also http://wordpress.org/support/topic/shortcodes-are-wrapped-in-paragraph-tags?replies=4
        if (!$wpautop) {
            //echo 'Initial: \'' . esc_html($options['content']) . '\'';
            if (substr($options['content'], 0, 6) == '<br />' &&
                substr($options['content'], -3, 3) == '<p>') {
                $options['content'] = substr($options['content'], 6, strlen($options['content']) - 6 - 3);
            }
            if (substr($options['content'], 0, 4) == '</p>' &&
                substr($options['content'], -3, 3) == '<p>') {
                $options['content'] = substr($options['content'], 4, strlen($options['content']) - 4 - 3);
            }
            //echo '<br/>Stripped: \'' . esc_html($options['content']) . '\'';
        }

        if ($stripBR) {
            // Strip out BR tags presumably injected by wpautop
            $options['content'] = str_replace('<br />', '', $options['content']);
        }

        // Break out sections: Before, Template, After
        $before = '';
        $template = '';
        $after = '';
        if (isset($options['content'])) {
            $contentParser = new CTC_ShortCodeContentParser;
            list($before, $template, $after) = $contentParser->parseBeforeContentAfter($options['content']);
        }

        if ($before) {
            // Allow for short codes in "before"
            echo do_shortcode($before);
        }

        while ($this->dataIterator->nextRow()) {
            // todo: Evaluation IF-expressions

            if (empty($colNamesToSub)) {
                // Process nested short codes
                echo do_shortcode($template);
            }
            else {
                $fields_with_file = null;
                if ($filelinks != 'name' &&
                    isset($this->dataIterator->row['fields_with_file']) &&
                    $this->dataIterator->row['fields_with_file'] != null) {
                    $fields_with_file = explode(',', $this->dataIterator->row['fields_with_file']);
                }
                $replacements = array();
                foreach ($colNamesToSub as $aCol) {
                    if ($fields_with_file && in_array($aCol, $fields_with_file)) {
                        switch ($filelinks) {
                            case 'url':
                                $replacements[] = $this->plugin->getFileUrl($this->dataIterator->row[$submitTimeKeyName], $formName, $aCol);
                                break;
                            case 'link':
                                if (isset($this->dataIterator->row[$aCol])) {
                                    $replacements[] =
                                            '<a href="' .
                                            esc_attr($this->plugin->getFileUrl($this->dataIterator->row[$submitTimeKeyName], $formName, $aCol)) .
                                            '">' .
                                            esc_html($this->dataIterator->row[$aCol]) .
                                            '</a>';
                                } else {
                                    $replacements[] = '';
                                }
                                break;
                            case 'image':
                            case 'img':
                                if (isset($this->dataIterator->row[$aCol])) {
                                    $replacements[] =
                                            '<img src="' .
                                            esc_attr($this->plugin->getFileUrl($this->dataIterator->row[$submitTimeKeyName], $formName, $aCol)) .
                                            '" alt="' .
                                            esc_attr($this->dataIterator->row[$aCol]) .
                                            '" />';
                                } else {
                                    $replacements[] = '';
                                }
                                break;
                            case 'name':
                            default:
                                if (isset($this->dataIterator->row[$aCol])) {
                                    $replacements[] = htmlentities($this->dataIterator->row[$aCol], null, 'UTF-8');
                                }
                        }
                    } else {
                        if (isset($this->dataIterator->row[$aCol])) {
                            if ($aCol == '_ctc_last_page_title'){
                                $page_title = 'Unknown';
                                if (isset($this->dataIterator->row['_ctc_last_page_title']) && isset($this->dataIterator->row['_ctc_last_page_uri'])){
                                    $title = $this->dataIterator->row['_ctc_last_page_title'];
                                    $anchor = strlen($title) > 30 ? esc_attr(trim(substr($title, 0, 30)).'&hellip;') : esc_attr($title);
                                    $page_title = "<a href='".$this->dataIterator->row['_ctc_last_page_uri']."' title=\"".esc_attr($title)."\" target='ctc_page'>".$anchor."</a>";
                                }elseif (isset($this->dataIterator->row['_ctc_last_page_ID']) && ($this->dataIterator->row['_ctc_last_page_ID'] > 0)){
                                    $title = get_the_title($this->dataIterator->row['_ctc_last_page_ID']);
                                    $anchor = strlen($title) > 30 ? esc_attr(trim(substr($title, 0, 30)).'&hellip;') : esc_attr($title);
                                    $page_title = "<a href='".get_permalink($this->dataIterator->row['_ctc_last_page_ID'])."' title=\"".esc_attr($title)."\" target='ctc_page'>".$anchor."</a>";
                                }elseif (preg_match('/^Page ID (\d+) - .*$/', $this->dataIterator->row['FormName'], $matches)){
                                    $post_id = $matches[1];
                                    $page_title = "<a href='".get_permalink($post_id)."' target='ctc_page'>".get_the_title($post_id)."</a>";
                                }

                                if ($page_title == 'Unknown'){
                                    global $session_handler_error;
                                    if ($session_handler_error){
                                        $page_title = '<span data-toggle="tooltip" data-placement="top" title="&#9888; It looks like your php server configuration for sessions is incorrect so this cannot be supported by Contactic plugin. Please check it out to unlock our greatest features !">Unknown &#9888;</span>';
                                    }
                                }
                                $replacements[] = $page_title;
                            }elseif ($aCol == '_ctc_referer'){
                                $replacements[] = (empty($this->dataIterator->row[$aCol])) ? htmlentities('Direct', null, 'UTF-8') : htmlentities($this->dataIterator->row[$aCol], null, 'UTF-8');
                            }else{
                                $replacements[] = htmlentities($this->dataIterator->row[$aCol], null, 'UTF-8');
                            }
                        } else {
                            if ($aCol == '_ctc_last_page_title'){
                                $page_title = 'Unknown';
                                global $session_handler_error;
                                if ($session_handler_error){
                                    $page_title = '<span data-toggle="tooltip" data-placement="top" title="&#9888; It looks like your php server configuration for sessions is incorrect so this cannot be supported by Contactic plugin. Please check it out to unlock our greatest features !">Unknown &#9888;</span>';
                                }
                                $replacements[] = $page_title;
                            }elseif ($aCol == '_ctc_referer'){
                                $replacements[] = 'Unknown';
                            }else{
                                $replacements[] = '';
                            }
                        }
                    }
                }
                // Preserve line breaks in the field
                foreach ($replacements as $i => $repl) {
                    $replacements[$i] = nl2br($replacements[$i]); // preserve line breaks
                }

                // Replace variables
                $output = str_replace($varNamesToSub, $replacements, $template);
                if ($substituteEmptyStringForUnknownFields) {
                    $output = preg_replace('/\${[^}]+}/', '', $output);
                }

                // Process nested short codes
                echo do_shortcode($output);
            }
        }

        if ($after) {
            // Allow for short codes in "after"
            echo do_shortcode($after);
        }


        if ($this->isFromShortCode) {
            // If called from a shortcode, need to return the text,
            // otherwise it can appear out of order on the page
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }

    }

    /**
     * Intended to be overridden
     * @param $template string
     * @return string
     */
    public function modifyContent($template) {
        return $template;
    }

}
