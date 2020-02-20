<?php
/*
    "Contact Form to Database" Copyright (C) 2011-2013 Michael Simpson  (email : michael.d.simpson@gmail.com)

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

require_once('CTC_View.php');
require_once(dirname( __FILE__ ).'/../ContacticPlugin.php');
require_once(dirname( __FILE__ ).'/../CTC_ExportToHtmlTable.php');

class CTC_ViewWhatsInDB extends CTC_View {

    public function loadStyles() {
        // Enqueue and register some styles
        wp_enqueue_style('contactic_css_styles', plugins_url('../assets/css/styles.css', __FILE__));
        wp_register_style('contactic_css_bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
        wp_register_style('datatables_css', '//cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.css');
        wp_register_script('datatables_js', '//cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/af-2.3.2/b-1.5.4/b-colvis-1.5.4/b-flash-1.5.4/b-html5-1.5.4/b-print-1.5.4/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.js');
        wp_register_style('contactic_fonts', '//fonts.googleapis.com/css?family=Poppins:300,400,500',null, null);
        wp_enqueue_style('contactic_css_bootstrap');
        wp_enqueue_style('contactic_fonts');
        wp_enqueue_style('datatables_css');
        wp_enqueue_script('datatables_js');

        wp_enqueue_script( 'jquery-ui-datepicker' );

        // You need styling for the datepicker. For simplicity I've linked to Google's hosted jQuery UI CSS.
        wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css' );
        //wp_register_style('jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker3.min.css' );
        wp_enqueue_style( 'jquery-ui' );
    }



    function display(&$plugin) {
        if ($plugin == null) {
            $plugin = new ContacticPlugin;
        }
        echo '<div id="cfdb-admin">';
        $canEdit = $plugin->canUserDoRoleOption('CanChangeSubmitData');
        $this->pageHeader($plugin);
        $this->loadStyles();

        global $wpdb;
        $tableName = $plugin->getSubmitsTableName();
        
        // Identify which forms have data in the database
        $formsList = $plugin->getForms();
        if (count($formsList) == 0) {
            echo esc_html(__('No form submissions in the database', 'contactic'));
            return;
        }
        $page = 1;
        if (isset($_REQUEST['dbpage'])) {
            // sanitize as int value
            $page = intval($_REQUEST['dbpage'] ? $_REQUEST['dbpage'] : '0');
        }
        $currSelection = null;
        if (isset($_REQUEST['form_name'])) {
            // $currSelection is unsanitized to pass to parametrized DB query.
            // $currSelectionEscaped is the sanitized version for display on the page
            $currSelection = $_REQUEST['form_name'] ? sanitize_text_field($_REQUEST['form_name']) : '0';
        }
        else if (isset($_REQUEST['form'])) {
            // $currSelection is unsanitized to pass to parametrized DB query.
            // $currSelectionEscaped is the sanitized version for display on the page
            $currSelection = $_REQUEST['form'] ? sanitize_text_field($_REQUEST['form']) : '0';
        }

        if ($currSelection) {
            $currSelection = stripslashes($currSelection);
            $currSelection = htmlspecialchars_decode($currSelection, ENT_QUOTES);
            $currSelection = strip_tags($currSelection); // guard against xss
        }

        // Sanitized version of $currSelection for display on the page
        $currSelectionEscaped = htmlspecialchars($currSelection, ENT_QUOTES, 'UTF-8');

        $tableHtmlId = 'table-'.substr(sha1($currSelectionEscaped), 0, 14);

        // If there is only one form in the DB, select that by default
        if (!$currSelection && count($formsList) == 1) {
            $currSelection = $formsList[0];
            // Bug fix: Need to set this so the Editor plugin can reference it
            $_REQUEST['form_name'] = $formsList[0];
        }
        if ($currSelection) {
            // Check for delete operation
            if (isset($_POST['cfdbdel']) &&
                    $canEdit &&
                    wp_verify_nonce($_REQUEST['_wpnonce'])) {
                if (isset($_POST['submit_time'])) {
                    $submitTime = sanitize_text_field($_POST['submit_time']);
                    $wpdb->query(
                        $wpdb->prepare(
                            "delete from `$tableName` where `form_name` = '%s' and `submit_time` = %F",
                            $currSelection, $submitTime));
                }
                else if (isset($_POST['all'])) {
                    $wpdb->query(
                        $wpdb->prepare(
                            "delete from `$tableName` where `form_name` = '%s'", $currSelection));
                }
                elseif (isset($_POST['rows_delete']) && !empty($_POST['rows_delete'])) {
                    $rows = explode(',', sanitize_text_field($_POST['rows_delete']));
                    foreach ($rows as $row) { // checkboxes
                        if (!empty($row)) {
                            $wpdb->query(
                                $wpdb->prepare(
                                    "delete from `$tableName` where `form_name` = '%s' and `submit_time` = %F",
                                    $currSelection, $row));
                        }
                    }
                }
            }
            else if (isset($_POST['delete_wpcf7']) &&
                    $canEdit &&
                    wp_verify_nonce($_REQUEST['_wpnonce'])) {
                $plugin->delete_wpcf7_fields($currSelection);
                $plugin->add_wpcf7_noSaveFields();
            }
            else if (isset($_POST['mass_status_change']) &&
                    $canEdit &&
                    wp_verify_nonce($_REQUEST['_wpnonce'])) {
                $rows = explode(',', sanitize_text_field($_POST['rows_status']));
                foreach ($rows as $row) { // checkboxes
                    if (!empty($row)) {
                        if (!$wpdb->query(
                            $wpdb->prepare(
                                "UPDATE `$tableName` SET `field_value`='%s' WHERE `form_name` = '%s' and `submit_time` = %F and `field_name`='_ctc_status'",
                                sanitize_text_field($_POST['mass_status_change']), $currSelection, $row))) {
                            $wpdb->query(
                            $wpdb->prepare(
                                "INSERT INTO `$tableName` (`submit_time`, `form_name`, `field_name`, `field_value`, `field_order`) 
                                VALUES (%F, '%s', '_ctc_status', '%s', 1002)",
                                $row, $currSelection, sanitize_text_field($_POST['mass_status_change'])));
                        }
                    }
                }
            }
            else if (isset($_POST['mass_usefulness_change']) &&
                    $canEdit &&
                    wp_verify_nonce($_REQUEST['_wpnonce'])) {
                $rows = explode(',', sanitize_text_field($_POST['rows_usefulness']));
                foreach ($rows as $row) { // checkboxes
                    if (!empty($row)) {
                        if (!$wpdb->query(
                            $wpdb->prepare(
                                "UPDATE `$tableName` SET `field_value`='%s' WHERE `form_name` = '%s' and `submit_time` = %F and `field_name`='_ctc_usefulness'",
                                sanitize_text_field($_POST['mass_usefulness_change']), $currSelection, $row))) {
                            $wpdb->query(
                            $wpdb->prepare(
                                "INSERT INTO `$tableName` (`submit_time`, `form_name`, `field_name`, `field_value`, `field_order`) 
                                VALUES (%F, '%s', '_ctc_usefulness', '%s', 1001)",
                                $row, $currSelection, sanitize_text_field($_POST['mass_usefulness_change'])));
                        }
                    }
                }
            }
        }
        // Form selection drop-down list
        $pluginDirUrl = $plugin->getPluginDirUrl();

        ?>
    <div class="wrap">
	    
	<h1>Contacts</h1>

	<div id="contacts_select">
		<div>
                <form method="get" action="<?php echo $_SERVER['REQUEST_URI']?>" name="displayform" id="displayform">
                    <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page'] ? $_REQUEST['page'] : '') ?>"/>
                    <select name="form_name" id="form_name" onchange="this.form.submit();">
                        <option value=""><?php echo esc_html(__('* Select a form *', 'contactic')); ?></option>
                        <?php foreach ($formsList as $formName) {
                            $selected = ($formName == $currSelection) ? "selected" : "";
                            $formNameEscaped = htmlspecialchars($formName, ENT_QUOTES, 'UTF-8');
                        ?>
                        <option disabled>─────────────</option>
                        <option value="<?php echo $formNameEscaped ?>" <?php echo $selected ?>><?php echo $formNameEscaped ?></option>
                        <?php } ?>
                    </select>
                </form>
		</div>


		<div>
                <?php if ($currSelection) { ?>
                <script type="text/javascript" language="Javascript">
                    var showHideExportLinkDelimiter = function() {
                        var enc = jQuery('#enc_cntl').val();
                        if (['CSVUTF8BOM', 'CSVUTF8', 'CSVSJIS'].indexOf(enc) > -1) {
                            jQuery('#csvdelim_span').show();
                        }
                        else {
                            jQuery('#csvdelim_span').hide();
                        }
                    };
                    jQuery(document).ready(function() {
                        showHideExportLinkDelimiter();
                        jQuery('#enc_cntl').change(showHideExportLinkDelimiter)
                    });
                    function getDelimiterValue() {
                        return jQuery('#csv_delim').val();
                    }
                    function changeDbPage(page) {
                        var newdiv = document.createElement('div');
                        newdiv.innerHTML = "<input id='dbpage' name='dbpage' type='hidden' value='" + page + "'>";
                        var dispForm = document.forms['displayform'];
                        dispForm.appendChild(newdiv);
                        dispForm.submit();
                    }
                    function getSearchFieldValue() {
                        var searchVal = '';
                        if (typeof jQuery == 'function') {
                            try {
                                searchVal = jQuery('#<?php echo $tableHtmlId;?>_filter input').val();
                            }
                            catch (e) {
                            }
                        }
                        return searchVal;
                    }
                    function exportData(encSelect) {
                        var enc = encSelect.options[encSelect.selectedIndex].value;

                        var checkedValues = [];
                        jQuery('input[id^="delete_"]:checked').each(function() {
                            checkedValues.push(this.name);
                        });
                        checkedValues = checkedValues.join(',');

                        var url;
                        if (enc == 'GLD') {
                            alert("<?php echo esc_js(__('You will now be navigated to the builder page where it will generate a function to place in your Google Spreadsheet', 'contactic')); ?>");
                            url = '<?php echo $plugin->getAdminUrlPrefix('admin.php') ?>page=ContacticPluginShortCodeBuilder&form=<?php echo urlencode($currSelection) ?>&enc=' + enc;
                            if (checkedValues) {
                                url += "&filter=submit_time[in]" + checkedValues;
                            }
                            location.href = url;
                        }
                        else {
                            url = '<?php echo $plugin->getAdminUrlPrefix('admin-ajax.php') ?>action=cfdb-export&form=<?php echo urlencode($currSelection) ?>&enc=' + enc;
                            var delimiter = getDelimiterValue();
                            if (delimiter) {
                                url += "&delimiter=" + encodeURIComponent(delimiter);
                            } else {
                                url += "&regionaldelimiter=true";
                            }
                            var searchVal = getSearchFieldValue();
                            if (searchVal) {
                                url += '&search=' + encodeURIComponent(searchVal);
                            }
                            if (checkedValues) {
                                url += "&filter=submit_time[in]" + checkedValues;
                            }
                            //alert(url);
                            location.href = url;
                        }
                    }
                </script>
                <form name="exportcsv" action="<?php echo $_SERVER['REQUEST_URI']?>">
                    <input type="hidden" name="unbuffered" value="true"/>
                    <select size="1" name="enc" id="enc_cntl">
                        <option><?php echo esc_html(__('* Choose export format *', 'contactic')); ?></option>
                        <option disabled>─────────────</option>
                        <option id="CSVUTF8" value="CSVUTF8">
                            <?php echo esc_html(__('Plain CSV (UTF-8)', 'contactic')); ?>
                        </option>
                        <option disabled>─────────────</option>
                        <option id="GLD" value="GLD">
                            <?php echo esc_html(__('Google Spreadsheet Live Data', 'contactic')); ?>
                        </option>
                        <option disabled>─────────────</option>              
                        <option id="xlsx" value="xlsx">
                            <?php echo esc_html(__('Excel .xlsx', 'contactic')); ?>
                        </option>
                         <option id="IQY" value="IQY">
                            <?php echo esc_html(__('Excel Internet Query', 'contactic')); ?>
                        </option>
                        <option id="CSVUTF8BOM" value="CSVUTF8BOM">
                            <?php echo esc_html(__('Excel CSV (UTF8-BOM)', 'contactic')); ?>
                        </option>
                        <option id="TSVUTF16LEBOM" value="TSVUTF16LEBOM">
                            <?php echo esc_html(__('Excel TSV (UTF16LE-BOM)', 'contactic')); ?>
                        </option>
                        <option id="CSVSJIS" value="CSVSJIS">
                            <?php echo esc_html(__('Excel CSV for Japanese (Shift-JIS)', 'contactic')); ?>
                        </option>                    
                        <option disabled>─────────────</option>
                        <option id="ods" value="ods">
                            <?php echo esc_html(__('OpenDocument .ods', 'contactic')); ?>
                        </option>
                        <option disabled>─────────────</option>
                        <option id="HTML" value="HTML">
                            <?php echo esc_html(__('HTML', 'contactic')); ?>
                        </option>
                        <option id="JSON" value="JSON">
                            <?php echo esc_html(__('JSON', 'contactic')); ?>
                        </option>
                    </select>
                    <input id="exportButton" name="exportButton" type="button" class="button"
                           value="<?php echo esc_attr(__('Export', 'contactic')); ?>"
                           onclick="exportData(this.form.elements['enc'])"/>
                    <span id="csvdelim_span" style="display:none">
                        <br />
                        <label for="csv_delim"><?php echo esc_html(__('CSV Delimiter', 'contactic')); ?></label>
                        <input id="csv_delim" type="text" size="2" value=""/>
                    </span>
                    <span style="font-size: x-small;"><br /><?php echo '<a href="admin.php?page=' . $plugin->getSlug('shortcodes') . '">' .
                          __('Advanced Export', 'contactic') . '</a>' ?>
                </form>
                <?php } ?>
		</div>


		<div>
                <?php if ($currSelection && $canEdit) { ?>
                <form action="<?php echo $_SERVER['REQUEST_URI']?>" method="post">
                    <input name="form_name" type="hidden" value="<?php echo $currSelectionEscaped ?>"/>
                    <input name="all" type="hidden" value="y"/>
                    <?php wp_nonce_field(); ?>
                    <input id="cfdbdeleteall" name="cfdbdel" type="submit" class="button"
                           value="<?php echo esc_attr(__('Delete All This Form\'s Records', 'contactic')); ?>"
                           onclick="return confirm('<?php echo esc_js(__('Are you sure you want to delete all the data for this form?', 'contactic')); ?>')"/>
                </form>
<!--                <br/>-->
<!--                    <form action="--><?php //echo $_SERVER['REQUEST_URI']?><!--" method="post">-->
<!--                        <input name="form_name" type="hidden" value="--><?php //echo $currSelectionEscaped ?><!--"/>-->
<!--                        --><?php //wp_nonce_field(); ?>
<!--                        <input id="delete_wpcf7" name="delete_wpcf7" type="submit" class="button"-->
<!--                               value="--><?php //echo esc_attr(__('Remove _wpcf7 columns', 'contactic')) ?><!--"/>-->
                    </form>
                <?php } ?>
		</div>
		</div>
		
        <!-- TABLEAU -->
        <div class="row tableau">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h4 class="card-title">Contacts</h4>
                        </div>
                        <div id="contactic1_wrapper" class="dataTables_wrapper dt-bootstrap4">

                            <?php
                                if ($currSelection) {
                                // Show table of form data
                                $exporter = new CTC_ExportToHtmlTable();
                                $dbRowCount = $exporter->getDBRowCount($currSelection);
                                $maxRows = 1; // we only need columns
                                $startRow = 0;
                                // Pick up any options that the user enters in the URL.
                                // This will include extraneous "form_name" and "page" GET params which are in the URL
                                // for this admin page
                                $options = array_merge($_POST, $_GET);
                                $options['canDelete'] = $canEdit;
                                if ($maxRows) {
                                    $limitStart = ($startRow < 1) ? 0 : ($startRow - 1);
                                    $options['limit'] = "$limitStart,$maxRows";
                                }
                                
                                $options['id'] = $tableHtmlId;
                                $options['class'] = '';
                                $options['style'] = "#$tableHtmlId {padding:0;} #$tableHtmlId td > div { max-height: 100px;  min-width:75px; overflow: auto; font-size: small;}"; // don't let cells get too tall
                                
                                $columns = $exporter->export($currSelection, $options, true);

                                $blacklisted_fields = $plugin->getNoSaveFields();

                                ?>

                                <div class="col-12">
                                    <p id="date_filter" style="text-align: right">
                                        <label id="date-label-from" class="date-label"><?php _e('From: ', 'contactic'); ?><input class="date_range_filter date" type="text" id="datepicker_from" /></label>
                                        <label id="date-label-to" class="date-label"><?php _e('To: ', 'contactic'); ?><input class="date_range_filter date" type="text" id="datepicker_to" /></label>
                                    </p>
                                </div>

                                <script type="text/javascript" language="Javascript">

                                    (function ($) {
                                        function mass_status_change(dt, status){
                                            var oData = dt.rows('.selected').ids();
                                            $('#mass_status_change').val(status);
                                            $('#rows_status').val('');
                                            for (var i=0; i < oData.length ;i++){
                                               $('#rows_status').val($('#rows_status').val() + oData[i] + ',');
                                            }
                                            $('#selectedrows_status').submit();
                                        }

                                        function mass_usefulness_change(dt, usefulness){
                                            var oData = dt.rows('.selected').ids();
                                            $('#mass_usefulness_change').val(usefulness);
                                            $('#rows_usefulness').val('');
                                            for (var i=0; i < oData.length ;i++){
                                               $('#rows_usefulness').val($('#rows_usefulness').val() + oData[i] + ',');
                                            }
                                            $('#selectedrows_usefulness').submit();
                                        }


                                        $(document).ready(function() {
                                            var url = "admin.php?page=<?php echo $plugin->getSlug('submissions') ?>&form_name=<?php echo urlencode($currSelection) ?>&submit_time=";

                                            // Date range filter
                                            var minDateFilter = "";
                                            var maxDateFilter = "";

                                            oTable = $('#<?php echo $tableHtmlId ?>').dataTable( {
                                                serverSide: true,
                                                stateSave: true,
                                                ajax: {
                                                    url: "<?php echo $plugin->getAdminUrlPrefix('admin-ajax.php') ?>action=load_contacts&form_name=<?php echo urlencode($currSelection); ?>",
                                                    type: "POST",
                                                    data: function ( d ) {
                                                        d.minDateFilter = minDateFilter;
                                                        d.maxDateFilter = maxDateFilter;
                                                    }
                                                },
                                                scrollX: true,
                                                scrollY: '50vh',
                                                paging: true,
                                                dom: 'Bfrtip',
                                                pageLength: <?php echo $plugin->getOption('MaxRows', '100', true); ?>,
                                                buttons: [ 
                                                            'pageLength',
                                                            {
                                                                extend: 'collection',
                                                                text: '<?php echo esc_html(__('Mass Action', 'contactic'))?>',
                                                                buttons: [
                                                                    {
                                                                        text: "<?php echo esc_html(__('Delete', 'contactic'))?>",
                                                                        action: function ( e, dt, node, config ) {
                                                                            var oData = oTable.api().rows('.selected').ids();
                                                                            if (confirm('<?php echo esc_js(__('Are you sure you want to delete the selected data for this form?', 'contactic')); ?>')) {
                                                                                $('#rows_delete').val('');
                                                                                for (var i=0; i < oData.length ;i++){
                                                                                   $('#rows_delete').val($('#rows_delete').val() + oData[i] + ',');
                                                                                }
                                                                                $('#selectedrows_delete').submit();
                                                                            }
                                                                        }
                                                                    },
                                                                    {
                                                                        text: '',
                                                                        className: 'dt_button_separator',
                                                                    },
                                                                    {
                                                                        text: "<?php echo esc_html(__('Set Status : Todo', 'contactic'))?>",
                                                                        action: function ( e, dt, node, config ) {
                                                                            mass_status_change(dt, 'Todo');
                                                                        }
                                                                    },
                                                                    {
                                                                        text: "<?php echo esc_html(__('Set Status : Done', 'contactic'))?>",
                                                                        action: function ( e, dt, node, config ) {
                                                                            mass_status_change(dt, 'Done');
                                                                        }
                                                                    },
                                                                    {
                                                                        text: '',
                                                                        className: 'dt_button_separator',
                                                                    },
                                                                    {
                                                                        text: "<?php echo esc_html(__('Set Usefulness : Usefull', 'contactic'))?>",
                                                                        action: function ( e, dt, node, config ) {
                                                                            mass_usefulness_change(dt, 'Usefull');
                                                                        }
                                                                    },
                                                                    {
                                                                        text: "<?php echo esc_html(__('Set Usefulness : Useless', 'contactic'))?>",
                                                                        action: function ( e, dt, node, config ) {
                                                                            mass_usefulness_change(dt, 'Useless');
                                                                        }
                                                                    },
                                                                    {
                                                                        text: "<?php echo esc_html(__('Set Usefulness : Spam', 'contactic'))?>",
                                                                        action: function ( e, dt, node, config ) {
                                                                            mass_usefulness_change(dt, 'Spam');
                                                                        }
                                                                    }
                                                                ]
                                                            },
                                                            { extend:'colvis',
                                                              columns:':gt(0)'
                                                            }
                                                        ],
                                                rowId: 'submit_time',
                                                columns: [
                                                    { data: null,
                                                      defaultContent: '',
                                                      className: 'select-checkbox',
                                                      orderable: false
                                                    },
                                                    <?php
                                                        foreach ($columns as $aCol) {
                                                            if (!in_array($aCol, $blacklisted_fields)){
                                                                ?>
                                                                {
                                                                    data: "<?php echo substr($aCol, 0, 40); ?>",
                                                                    name: '<?php echo substr($aCol, 0, 40); ?>'
                                                                },
                                                                <?php
                                                            }
                                                        }
                                                    ?>
                                                ],
                                                columnDefs: [{
                                                                orderable: false,
                                                                className: 'select-checkbox',
                                                                targets:   0
                                                            },
                                                            {
                                                                render : function (data, type, row) {
                                                                    return '<a href="#" class="opensubmitdetailsmodal" data-id="' + row.submit_time + '" >' + data + '</a>';
                                                                },
                                                                targets:   1
                                                            }],
                                                select: {
                                                    style:    'multi',
                                                    selector: 'td:first-child'
                                                },
                                                order: [[ 1, 'desc' ]]
                                            });

                                            $('#selectall').click( function () {
                                                if (this.checked) {
                                                    oTable.api().rows().select();
                                                }else{
                                                    oTable.api().rows().deselect();
                                                }
                                            });

                                            $("#datepicker_from").datepicker({
                                                "onSelect": function(date) {
                                                    minDateFilter = new Date(date).getTime() / 1000;
                                                    if (isNaN(minDateFilter)) minDateFilter = '';
                                                    oTable.fnDraw();
                                                }
                                            }).keyup(function() {
                                                minDateFilter = new Date(this.value).getTime() / 1000;
                                                if (isNaN(minDateFilter)) minDateFilter = '';
                                                oTable.fnDraw();
                                            });

                                            $("#datepicker_to").datepicker({
                                                "onSelect": function(date) {
                                                    maxDateFilter = new Date(date).getTime() / 1000;
                                                    if (isNaN(maxDateFilter)) maxDateFilter = '';
                                                    oTable.fnDraw();
                                                }
                                            }).keyup(function() {
                                                maxDateFilter = new Date(this.value).getTime() / 1000;
                                                if (isNaN(maxDateFilter)) maxDateFilter = '';
                                                oTable.fnDraw();
                                            });

                                        });
                                    })(jQuery);
                                </script>
                                <?php

                                if ($canEdit) {
                                    ?>
                            <form action="<?php echo $_SERVER['REQUEST_URI']?>" method="post" id="selectedrows_status">
                                <input name="form_name" type="hidden" value="<?php echo $currSelectionEscaped ?>"/>
                                <input id="mass_status_change" name="mass_status_change" type="hidden" value="mass_status_change"/>
                                <input type="hidden" name="rows_status" id="rows_status" value="">
                                <?php wp_nonce_field(); ?>
                            </form>
                            <form action="<?php echo $_SERVER['REQUEST_URI']?>" method="post" id="selectedrows_usefulness">
                                <input name="form_name" type="hidden" value="<?php echo $currSelectionEscaped ?>"/>
                                <input id="mass_usefulness_change" name="mass_usefulness_change" type="hidden" value="mass_usefulness_change"/>
                                <input type="hidden" name="rows_usefulness" id="rows_usefulness" value="">
                                <?php wp_nonce_field(); ?>
                            </form>
                            <form action="<?php echo $_SERVER['REQUEST_URI']?>" method="post" id="selectedrows_delete">
                                <input name="form_name" type="hidden" value="<?php echo $currSelectionEscaped ?>"/>
                                    <input id="cfdbdelete" name="cfdbdel" type="hidden" value="rows"/>
                                    <input type="hidden" name="rows_delete" id="rows_delete" value="">
                                    <?php wp_nonce_field(); ?>
                                    <?php
                                }
                                ?>
                                <table id="<?php echo $tableHtmlId; ?>" class="display table table-hover table-striped dataTable no-footer ui celled nowrap" role="grid" aria-describedby="contactic1_info">
                                    <thead>
                                    <tr>
                                    <?php if ($canEdit) { ?>
                                    <th>
                                       <input type="checkbox" id="selectall" />
                                    </th>
                                    <?php
                                    }
                                    foreach ($columns as $aCol) {
                                        if (!in_array($aCol, $blacklisted_fields)){
                                            ?>
                                            <th><?php echo substr($aCol, 0, 40); ?></th>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tr>
                                    </thead>
                                </table>
                                <?php if ($canEdit) {
                                    ?>
                                </form>
                            <?php

                                }
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.TABLEAU -->
    <div class="clear"></div>        
  
    <?php
           if ($currSelection && 'true' == $plugin->getOption('ShowQuery', 'false', true)) {
            ?>
        <div id="query" style="direction: ltr; margin: 20px; border: dotted #d3d3d3 1pt;">
            <strong><?php echo esc_html(__('Query:', 'contactic')); ?></strong><br/>
            <pre><?php echo $exporter->getPivotQuery($currSelection); ?></pre>
        </div>
        <?php

        }
        if ($currSelection) {
            ?>
        <script type="text/javascript" language="Javascript">
            var addColumnLabelText = '<?php echo esc_attr(__('Add Column', 'contactic')); // input button value attribute ?>';
            var deleteColumnLabelText = '<?php echo esc_attr(__('Delete Column', 'contactic')); // input button value attribute ?>';
        </script>
        <?php
            do_action_ref_array('cfdb_edit_setup', array($plugin));
        }
        echo '</div>'; // cfdb-admin


        $this->outputModal($plugin, $tableHtmlId);

    }

    public function outputModal($plugin, $tableHtmlId) { ?>

        <!-- Details -->
        <div class="modal fade" id="modal-contact" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Contact</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-body table-responsive" id="contactic2">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $(document).on("click", ".opensubmitdetailsmodal", function () {

                    // open modal from js side
                    jQuery('#modal-contact').modal('show');
                    jQuery('#modal-contact').appendTo('body');
                    $.ajax({
                        url: ajaxurl,
                        data: {
                            action: 'submit_details',
                            submit_time: $(this).data('id'),
                            i: $(this).data('id').toString().replace('.', '_')
                        }
                    }).done(function(response) {
                        $('#contactic2').html(response);
                        if ($('#form_name').val().length > 0) $('.modal-title').html('Contact - "' + $('#form_name').val() + '" form');
                        $("#contactic2 tr td").each(function() {
                            var cellText = $.trim($(this).text());
                            if ((cellText.length == 0) || cellText.substring(0, 5) == '_ctc_') {
                                $(this).parent().remove();
                            }
                        });
                    }).fail(function() {
                        $('#contactic2').html("Network error, please retry.");
                    });
                });
                $(document).on("click", "#delete", function () {
                    if (confirm('<?php echo esc_js(__('Are you sure you want to delete this contact ?', 'contactic')); ?>')){
                        $.ajax({
                            url: ajaxurl,
                            data: {
                                action: 'submit_delete',
                                submit_time: $(this).data('id')
                            }
                        }).done(function(response) {
                            location.reload();
                        }).fail(function() {
                        		alert("<?php echo esc_js(__('Network error, please retry.', 'contactic')); ?>");
                        });
                    }
                });

                $(document).on("click", ".changeusefulness", function () {

                    submit_time = $(this).parent().data('id');
                    usefulness =  $(this).text();
                    i = $(this).parent().data('i');

                    $.ajax({
                        url: ajaxurl,
                        data: {
                            action: 'submit_usefulness',
                            submit_time: submit_time,
                            usefulness: usefulness
                        },
                    }).done(function() {

                        $('#usefulness_' + i).text(usefulness);
                        $('#modalusefulness_' + i).text(usefulness);

                        oTable.api().cell('#' + submit_time, '_ctc_usefulness:name').data(usefulness);

                        switch(usefulness){
                            case 'Usefull':
                                usefulnessclass = 'btn-success';
                                break;
                            case 'Useless':
                                usefulnessclass = 'btn-danger';
                                break;
                            case 'Spam':
                                usefulnessclass = 'btn-dark';
                                break;
                            default:
                                usefulnessclass = 'btn-warning';
                        }
                        $('#usefulness_' + i).addClass('btn-danger btn-dark btn-warning btn-success')
                        if (usefulnessclass != 'btn-danger') $('#usefulness_' + i).removeClass('btn-danger');
                        if (usefulnessclass != 'btn-dark') $('#usefulness_' + i).removeClass('btn-dark');
                        if (usefulnessclass != 'btn-warning') $('#usefulness_' + i).removeClass('btn-warning');
                        if (usefulnessclass != 'btn-success') $('#usefulness_' + i).removeClass('btn-success');
                        $('#modalusefulness_' + i).addClass('btn-danger btn-dark btn-warning btn-success')
                        if (usefulnessclass != 'btn-danger') $('#modalusefulness_' + i).removeClass('btn-danger');
                        if (usefulnessclass != 'btn-dark') $('#modalusefulness_' + i).removeClass('btn-dark');
                        if (usefulnessclass != 'btn-warning') $('#modalusefulness_' + i).removeClass('btn-warning');
                        if (usefulnessclass != 'btn-success') $('#modalusefulness_' + i).removeClass('btn-success');
                    }).fail(function() {
                        alert("<?php _e('Network error, please retry.', 'contactic'); ?>");
                    });
                });
                $(document).on("click", ".changestatus", function () {

                    submit_time = $(this).data('id');
                    status =  $(this).text();
                    i = $(this).data('i');

                    $.ajax({
                        url: ajaxurl,
                        data: {
                            action: 'submit_status',
                            submit_time: submit_time,
                            status: status
                        },
                    }).done(function(response) {
                        if (status == 'Todo'){
                            status = 'Done';
                        }else{
                            status = 'Todo';
                        }
                        oTable.api().cell('#' + submit_time, '_ctc_status:name').data(status);
                        $('#modalstatus_' + i).text(status);
                        if (status == 'Done'){
                            $('#modalstatus_' + i).removeClass('btn-danger');
                            $('#modalstatus_' + i).addClass('btn-success');
                        }else{
                            $('#modalstatus_' + i).removeClass('btn-success');
                            $('#modalstatus_' + i).addClass('btn-danger');
                        }
                    }).fail(function() {
                        alert("<?php _e('Network error, please retry.', 'contactic'); ?>");
                    });
                });
                
            });
        </script>

        <?php
    }

    public function outputFooter() {


    }


}