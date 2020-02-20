<?php


/*
    "Contactic.io" Copyright (C) 2018 Contactic.io

    This file is part of Contactic_wp plugin.

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

class CTC_ExportToOverview extends CTC_ExportBase implements CTC_Export {

    /**
     * @var bool
     */
    static $wroteDefaultHtmlTableStyle = false;

    /**
     * Echo a table of submitted form data
     * @param string $formName
     * @param array $options
     * @return void|string returns String when called from a short code,
     * otherwise echo's output and returns void
     */
    public function export($formName, $options = null) {

        $this->setOptions($options);

        $this->setCommonOptions(true);

        // Security Check
        if (!$this->isAuthorized()) {
            $this->assertSecurityErrorMessage();
            return;
        }

        // Query DB for the data for that form
        $submitTimeKeyName = 'Submit_Time_Key';
        $this->setDataIterator($formName, $submitTimeKeyName, $merge_col = true);


        ?>

        <table <?php if ($this->htmlTableId) echo "id=\"$this->htmlTableId\" "; if ($this->htmlTableClass) echo "class=\"$this->htmlTableClass\"" ?> >
            <thead>
                <tr role="row">
                    <th class="sorting_asc" tabindex="0" aria-controls="contactic1" rowspan="1" colspan="1" aria-sort="ascending" aria-label=""><?php _e('Date', 'contactic'); ?></th>
                    <th class="sorting d-none d-sm-table-cell" tabindex="0" aria-controls="contactic1" rowspan="1" colspan="1" aria-label=""><?php _e('Source', 'contactic'); ?></th>
                    <th class="sorting" tabindex="0" aria-controls="contactic1" rowspan="1" colspan="1" aria-label=""><?php _e('Page', 'contactic'); ?></th>
                    <th class="sorting d-none d-md-table-cell" tabindex="0" aria-controls="contactic1" rowspan="1" colspan="1" aria-label=""><?php _e('Email', 'contactic'); ?></th>
                    <th class="sorting d-none d-lg-table-cell" tabindex="0" aria-controls="contactic1" rowspan="1" colspan="1" aria-label=""><?php _e('Usefulness', 'contactic'); ?></th>
                    <th class="sorting d-none d-lg-table-cell" tabindex="0" aria-controls="contactic1" rowspan="1" colspan="1" aria-label=""><?php _e('Status', 'contactic'); ?></th>
                    <th class="sorting" tabindex="0" aria-controls="contactic1" rowspan="1" colspan="1" aria-label=""><?php _e('Details', 'contactic'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            $showLineBreaks = $this->plugin->getOption('ShowLineBreaksInDataTable');
            $showLineBreaks = 'false' != $showLineBreaks;
            $i = 1;
            while ($this->dataIterator->nextRow()) {
                $submitKey = '';
                if (isset($this->dataIterator->row[$submitTimeKeyName])) {
                    $submitKey = $this->dataIterator->row[$submitTimeKeyName];
                }
                if (!isset($this->dataIterator->row['_ctc_usefulness']) || empty($this->dataIterator->row['_ctc_usefulness'])){
                    $this->dataIterator->row['_ctc_usefulness'] = 'Undefined';
                }
                if (!isset($this->dataIterator->row['_ctc_status']) || empty($this->dataIterator->row['_ctc_status'])){
                    $this->dataIterator->row['_ctc_status'] = 'Todo';
                }
                $status_class = ($this->dataIterator->row['_ctc_status'] == 'Done') ? 'success' : 'danger';
                switch ($this->dataIterator->row['_ctc_usefulness']) {
                    case 'Usefull':
                        $usefulness_class = 'success';
                        break;
                    case 'Useless':
                        $usefulness_class = 'danger';
                        break;
                    case 'Spam':
                        $usefulness_class = 'dark';
                        break;
                    default:
                        $usefulness_class = 'warning';
                        break;
                }

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
                        $page_title = '<span data-toggle="tooltip" data-placement="top" title="&#9888; It looks like your php server configuration for sessions is incorrect so this cannot be supported by Contactic plugin. Please check it out to unlock our greatest features !">Unknown &#9888;></span>';
                    }
                }

                if (!isset($this->dataIterator->row['_ctc_referer'])){
                    $this->dataIterator->row['_ctc_referer'] = 'Unknown';
                }
                if (empty($this->dataIterator->row['_ctc_referer'])){
                    $this->dataIterator->row['_ctc_referer'] = 'Direct';
                }

                ?>
                <tr>
                    <td class="sorting_1">
                        <?php echo date('Y-m-d H:i', $submitKey); ?>
                    </td>
                    <td class="sorting d-none d-sm-table-cell">
                        <?php echo $this->dataIterator->row['_ctc_referer']; ?>
                    </td>
                    <td>
                        <?php echo $page_title; ?>
                    </td>
                    <td class="sorting d-none d-md-table-cell">
                        <?php 
                            $emails = array();
                            foreach (explode(' ', $this->dataIterator->row['merge']) as $email) {
                                $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                                if (filter_var($email, FILTER_VALIDATE_EMAIL)){
                                    $emails[] = $email;
                                }
                            }
                            $dedup_emails = array_unique($emails);
                            
                            echo implode(', ', $dedup_emails);
                        ?>
                    </td>
                    <td class="sorting d-none d-lg-table-cell" style="text-align:center;">
                        <div class="dropdown">
                            <button type="button" data-toggle="dropdown" data-id="<?php echo $submitKey; ?>" class="btn waves-effect waves-light btn-<?php echo $usefulness_class ?> dropdown-toggle" id="usefulness_<?php echo $i; ?>"><?php echo $this->dataIterator->row['_ctc_usefulness']; ?></button>
                            <div class="dropdown-menu" aria-labelledby="usefulness_<?php echo $i; ?>" data-id="<?php echo $submitKey; ?>" data-i="<?php echo $i ?>">
                                <a class="dropdown-item changeusefulness" href="javascript:;">Usefull</a>
                                <a class="dropdown-item changeusefulness" href="javascript:;">Useless</a>
                                <a class="dropdown-item changeusefulness" href="javascript:;">Spam</a>
                            </div>
                        </div>
                    </td>
                    <td class="sorting d-none d-lg-table-cell" style="text-align:center;">
                        <div class="dropdown">
                            <button type="button" data-id="<?php echo $submitKey; ?>" class="btn waves-effect waves-light btn-<?php echo $status_class ?> dropdown-toggle changestatus" id="status_<?php echo $i; ?>" data-i="<?php echo $i ?>"><?php echo $this->dataIterator->row['_ctc_status']; ?></button>
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <a href="javascript:;" data-toggle="modal" data-target="#modal-contact" data-id="<?php echo $submitKey; ?>" data-i="<?php echo $i; ?>" class="opensubmitdetailsmodal"><i class="far fa-file-alt fa-lg"></i></a>
                    </td>
                <!--
                <?php

                $fields_with_file = null;

                foreach ($this->dataIterator->getDisplayColumns() as $aCol) {
                    $cell = $this->rawValueToPresentationValue(
                        $this->dataIterator->row[$aCol],
                        $showLineBreaks,
                        ($fields_with_file && in_array($aCol, $fields_with_file)),
                        $this->dataIterator->row[$submitTimeKeyName],
                        $formName,
                        $aCol);

                    // NOTE: the ID field is used to identify the cell when an edit happens and we save that to the server
                    printf('<td title="%s"><div id="%s,%s">%s</div></td>',
                        esc_attr($aCol),
                        esc_attr($submitKey),
                        esc_attr($aCol),
                        $cell); // $cell sanitized by rawValueToPresentationValue()
                }
                ?>
                -->
                </tr><?php
                $i++;
            } ?>
            </tbody>
        </table>
        <?php



//        if ($this->isFromShortCode) {
//            // If called from a shortcode, need to return the text,
//            // otherwise it can appear out of order on the page
//            $output = ob_get_contents();
//            ob_end_clean();
//            return $output;
//        }
    }

    public function &rawValueToPresentationValue(&$value, $showLineBreaks, $isUrl, &$submitTimeKey, &$formName, &$fieldName) {
        $value = esc_html($value); // no HTML injection
        if ($showLineBreaks) {
            $value = str_replace("\r\n", '<br/>', $value); // preserve DOS line breaks
            $value = str_replace("\n", '<br/>', $value); // preserve UNIX line breaks
        }
        if ($isUrl) {
            $fileUrl = $this->plugin->getFileUrl($submitTimeKey, $formName, $fieldName);
            $value = "<a href=\"$fileUrl\">$value</a>";
        }

        return $value;
    }
}

