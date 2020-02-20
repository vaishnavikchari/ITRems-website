<?php

/*
    "Contactic Copyright (C) 2018-2019  (email : wp@contactic.io)

    This file is part of Contactic Wp.

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
require_once(dirname( __FILE__ ).'/../CTC_ExportToHtmlTable.php');
require_once(dirname( __FILE__ ).'/../CTC_ExportToValue.php');
require_once(dirname( __FILE__ ).'/../CTC_ExportToJson.php');
require_once(dirname( __FILE__ ).'/../CTC_ExportToOverview.php');

class CTC_ViewOverview extends CTC_View {

    var $page = 1;

    /**
     * @param  $plugin ContacticPlugin
     * @return void
     */
    function display(&$plugin) {

        $canEdit = $plugin->canUserDoRoleOption('CanChangeSubmitData');
        $this->pageHeader($plugin);



        if (isset($_REQUEST['dbpage'])) {
            // sanitize as int value
            $this->page = intval($_REQUEST['dbpage'] ? $_REQUEST['dbpage'] : '0');
        }

        global $wpdb;
        $tableName = $plugin->getSubmitsTableName();

        $tableHtmlId = 'cf2dbtable';

        // Identify which forms have data in the database, (cf7, ninja...)
        $formsList = $plugin->getForms();


        // Set Current form
        $currSelection = '*';

        //$exporter = new CTCExportToHtmlTable();
        //$dbRowCount = $exporter->getDBRowCount($currSelection);
        //$maxRows = $plugin->getOption('MaxRows', '100', true);
        //$startRow = $this->paginationDiv($plugin, $dbRowCount, $maxRows, $page);

        // Pick up any options that the user enters in the URL.
        // This will include extraneous "form_name" and "page" GET params which are in the URL
        // for this admin page
        $options = array_merge($_POST, $_GET);
        $options['canDelete'] = $canEdit;


        $maxRows = $plugin->getOption('MaxRows', '100', true);
        $options['limit'] = "0,".$maxRows;

        $options['id'] = 'contactic1';
        $options['class'] = 'display table table-hover table-striped table-bordered dataTable';
        $options['style'] = "#$tableHtmlId {padding:0;} #$tableHtmlId td > div { max-height: 100px;  min-width:75px; overflow: auto; font-size: small;}"; // don't let cells get too tall

        $this->loadStyles();
        
        $this->output($plugin, $options);

    }

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

    public function enqueueSettingsPageScripts() {

    }


    /**
     * @param $plugin ContacticPlugin
     */
    public function output($plugin, $options) {

        // 6 last months chart gen
        $contacts = $plugin->month_count();
        for ($i = 5; $i >= 0; $i--) {
           $found = false;
           $months[] = date("M", strtotime( date( 'Y-m-01' )." -$i months"));
           foreach ($contacts as $contact) {
               if ($contact->month == date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"))){
                    $data[] = $contact->contacts;
                    $found = true;
                    break;
               }
           }
           if (!$found) $data[] = 0;
        }

        $usefulness = $plugin->usefulness_distribution();
        $statuses = $plugin->status_distribution();

        $data_usefulness = ['Usefull' => 0, 'Useless' => 0, 'Undefined' => 0, 'Spam' => 0];
        $data_statuses = ['Done' => 0, 'Todo' => 0];

        foreach ($usefulness as $usefulness_count) {
            $data_usefulness[$usefulness_count->usefulness] = $usefulness_count->contacts;
        }
        foreach ($statuses as $status_count) {
            $data_statuses[$status_count->status] = $status_count->contacts;
        }


        ?>
        <div class="wrap">
        <h1><?php _e('Overview', 'contactic'); ?></h1>

            <!-- GRAPHS -->

            <div class="row d-flex align-items-stretch">

                <!-- column -->
                <div class="col-lg-4 graph">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title"><?php _e('Contacts', 'contactic'); ?></h4>
                            <div>
                                <canvas id="year-chart" class="chartjs-render-monitor" style="display: block;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    new Chart(document.getElementById("year-chart"), {
                        "type": "bar",
                        "data": {
                            "labels": [<?php foreach ($months as $month) { echo '"'.$month.'", '; } ?>],
                            "datasets": [{
                                "label": "",
                                "data": [<?php foreach ($data as $value) { echo $value.', '; } ?>],
                                "fill": false,
                                "backgroundColor": ["rgba(255, 159, 64, 0.2)", "rgba(255, 205, 86, 0.2)", "rgba(75, 192, 192, 0.2)", "rgba(54, 162, 235, 0.2)", "rgba(153, 102, 255, 0.2)", "rgba(201, 203, 207, 0.2)"],
                                "borderColor": ["rgb(255, 159, 64)", "rgb(255, 205, 86)", "rgb(75, 192, 192)", "rgb(54, 162, 235)", "rgb(153, 102, 255)", "rgb(201, 203, 207)"],
                                "borderWidth": 1
                            }]
                        },
                        "options": {
                            legend:{
                                display:false
                            },
                            "scales": {
                                "yAxes": [{
                                    "ticks": {
                                        "beginAtZero": true
                                    }
                                }]
                            }
                        }
                    });
                </script>
                <!-- column -->

                <!-- column -->
                <div class="col-lg-4 graph">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title"><?php _e('Usefulness', 'contactic'); ?></h4>
                            <canvas id="usefull-chart" class="chartjs-render-monitor" style="display: block;"></canvas>
                        </div>
                    </div>
                </div>
                <script>
                    new Chart(document.getElementById("usefull-chart"), {
                        "type": "doughnut",
                        "data": {
                            "labels": ["Usefull", "Useless", "Undefined", "Spam"],
                            "datasets": [{
                                "data": [<?php echo $data_usefulness['Usefull'].', '.
                                                $data_usefulness['Useless'].', '.
                                                $data_usefulness['Undefined'].', '.
                                                $data_usefulness['Spam'] ?>],
                                "backgroundColor": ["rgb(0, 194, 146)", "rgb(228,106,118)", "rgb(254,193,7)","rgb(52,58,64)"]
                            }]
                        },
                        "options": {
                            legend:{
                                position:'left'
                            },
                        }
                    });
                </script>
                <!-- column -->

                <!-- column -->
                <div class="col-lg-4 graph">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title"><?php _e('Todo', 'contactic'); ?></h4>
                            <canvas id="todo-chart" class="chartjs-render-monitor" style="display: block;"></canvas>
                        </div>
                    </div>
                </div>
                <script>
                    new Chart(document.getElementById("todo-chart"), {
                        "type": "pie",
                        "data": {
                            "labels": ["Done", "Todo" ],
                            "datasets": [{
                                "data": [<?php echo $data_statuses['Done'].', '.$data_statuses['Todo'] ?>],
                                "backgroundColor": ["rgb(0, 194, 146)", "rgb(228,106,118)"]
                            }]
                        },
                        "options": {
                            legend:{
                                position:'left'
                            },
                        }
                    });
                </script>
                <!-- column -->

            </div>

            <!-- /.GRAPHS -->

            <?php /*
            <div class="card">
                <div class="card-body">
                    <div>
                        <h4 class="card-title">Get more stats and tracking on your leads</h4>
                    </div>
                    <div>
                        <p><a href="admin.php?page=ContacticPluginSettings#contactic">Connect your metrics to contactic.io, start here.</a></p>
                        <p>We do not collect the e-mails or messages, only the metrics to help you in your campaigns and give you more analytics on your leads.</p>
                    </div>
                </div>
            </div>
            */ ?>

            <!-- TABLEAU -->

            <div class="row tableau">

                <div class="col">

                    <div class="card">

                        <div class="card-body">

                            <div>
                                <h4 class="card-title"><?php _e('Contacts Export', 'contactic'); ?></h4>
                            </div>

                            <div id="contactic1_wrapper" class="dataTables_wrapper dt-bootstrap4">

                                <div class="row">
                                    <div class="col-6">
                                        <h6 class="card-subtitle"><?php _e('Export all data to the format you want', 'contactic'); ?></h6>
                                        <div class="dt-buttons d-none d-md-block">
                                            <a target="export" href="<?php echo $plugin->getAdminUrlPrefix('admin-ajax.php') ?>action=cfdb-export&form=*&enc=CSVUTF8" class="dt-button buttons-csv buttons-html5 btn btn-primary mr-1" tabindex="0" aria-controls="contactic1"><span>CSV</span></a>
                                            <a target="export" href="<?php echo $plugin->getAdminUrlPrefix('admin-ajax.php') ?>action=cfdb-export&form=*&enc=ods" class="dt-button buttons-csv buttons-html5 btn btn-primary mr-1" tabindex="0" aria-controls="contactic1"><span>ODS</span></a>
                                            <a target="export" href="<?php echo $plugin->getAdminUrlPrefix('admin-ajax.php') ?>action=cfdb-export&form=*&enc=xlsx" class="dt-button buttons-excel buttons-html5 btn btn-primary mr-1" tabindex="0" aria-controls="contactic1"><span>Excel</span></a>
                                            <a target="export" href="<?php echo $plugin->getAdminUrlPrefix('admin-ajax.php') ?>action=cfdb-export&form=*&enc=HTML" class="dt-button buttons-csv buttons-html5 btn btn-primary mr-1" tabindex="0" aria-controls="contactic1"><span>HTML</span></a>
                                            <a target="export" href="<?php echo $plugin->getAdminUrlPrefix('admin-ajax.php') ?>action=cfdb-export&form=*&enc=JSON" class="dt-button buttons-csv buttons-html5 btn btn-primary mr-1" tabindex="0" aria-controls="contactic1"><span>JSON</span></a>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <p id="date_filter" style="text-align: right">
                                            <label id="date-label-from" class="date-label"><?php _e('From: ', 'contactic'); ?><input class="date_range_filter date" type="text" id="datepicker_from" /></label>
                                            <label id="date-label-to" class="date-label"><?php _e('To: ', 'contactic'); ?><input class="date_range_filter date" type="text" id="datepicker_to" /></label>
                                        </p>
                                    </div>
                                </div>

                                <?php

                                $exporter = new CTC_ExportToOverview();
                                $exporter->export('*', $options);

                                ?>

                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.TABLEAU -->

        </div>

        <?php

        $this->outputModal($plugin);
        $this->outputFooter();



    }


    public function outputModal($plugin) { ?>

        <!-- Details -->
        <div class="modal fade" id="modal-contact" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php _e('Contact', 'contactic'); ?></h5>
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
                $('#modal-contact').parent().on('show.bs.modal', function(e){ $(e.relatedTarget.attributes['data-target'].value).appendTo('body'); });
                $(document).on("click", ".opensubmitdetailsmodal", function () {
                    $.ajax({
                        url: ajaxurl,
                        data: {
                            action: 'submit_details',
                            submit_time: $(this).data('id'),
                            i: $(this).data('i')
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
                        		alert("<?php _e('Network error, please retry.', 'contactic'); ?>");
                        });
                    }
                });
                $(document).on("click", ".changeusefulness", function () {
                    $.ajax({
                        url: ajaxurl,
                        data: {
                            action: 'submit_usefulness',
                            submit_time: $(this).parent().data('id'),
                            usefulness: $(this).text()
                        },
                        submit_time: $(this).parent().data('id'),
                        usefulness: $(this).text(),
                        i: $(this).parent().data('i')
                    }).done(function(response) {
                        $('#usefulness_' + this.i).text(this.usefulness);
                        $('#modalusefulness_' + this.i).text(this.usefulness);
                        switch(this.usefulness){
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
                        $('#usefulness_' + this.i).addClass('btn-danger btn-dark btn-warning btn-success')
                        if (usefulnessclass != 'btn-danger') $('#usefulness_' + this.i).removeClass('btn-danger');
                        if (usefulnessclass != 'btn-dark') $('#usefulness_' + this.i).removeClass('btn-dark');
                        if (usefulnessclass != 'btn-warning') $('#usefulness_' + this.i).removeClass('btn-warning');
                        if (usefulnessclass != 'btn-success') $('#usefulness_' + this.i).removeClass('btn-success');
                        $('#modalusefulness_' + this.i).addClass('btn-danger btn-dark btn-warning btn-success')
                        if (usefulnessclass != 'btn-danger') $('#modalusefulness_' + this.i).removeClass('btn-danger');
                        if (usefulnessclass != 'btn-dark') $('#modalusefulness_' + this.i).removeClass('btn-dark');
                        if (usefulnessclass != 'btn-warning') $('#modalusefulness_' + this.i).removeClass('btn-warning');
                        if (usefulnessclass != 'btn-success') $('#modalusefulness_' + this.i).removeClass('btn-success');
                    }).fail(function() {
                        alert("<?php _e('Network error, please retry.', 'contactic'); ?>");
                    });
                });
                $(document).on("click", ".changestatus", function () {
                    $.ajax({
                        url: ajaxurl,
                        data: {
                            action: 'submit_status',
                            submit_time: $(this).data('id'),
                            status: $(this).text()
                        },
                        submit_time: $(this).data('id'),
                        status: $(this).text(),
                        i: $(this).data('i')
                    }).done(function(response) {
                        if (this.status == 'Todo'){
                            this.status = 'Done';
                        }else{
                            this.status = 'Todo';
                        }
                        $('#status_' + this.i).text(this.status);
                        $('#modalstatus_' + this.i).text(this.status);
                        if (this.status == 'Done'){
                            $('#status_' + this.i).removeClass('btn-danger');
                            $('#modalstatus_' + this.i).removeClass('btn-danger');
                            $('#status_' + this.i).addClass('btn-success');
                            $('#modalstatus_' + this.i).addClass('btn-success');
                        }else{
                            $('#status_' + this.i).removeClass('btn-success');
                            $('#modalstatus_' + this.i).removeClass('btn-success');
                            $('#status_' + this.i).addClass('btn-danger');
                            $('#modalstatus_' + this.i).addClass('btn-danger');
                        }
                    }).fail(function() {
                        alert("<?php _e('Network error, please retry.', 'contactic'); ?>");
                    });
                });

                oTable = $('#contactic1').DataTable({
                    "order": [[ 0, "desc" ]],
                    "columns": [
                        { "data": "<?php _e('Date', 'contactic'); ?>" },
                        { "data": "<?php _e('Source', 'contactic'); ?>" },
                        { "data": "<?php _e('Page', 'contactic'); ?>" },
                        { "data": "<?php _e('Email', 'contactic'); ?>" },
                        { "data": "<?php _e('Usefulness', 'contactic'); ?>" },
                        { "data": "<?php _e('Status', 'contactic'); ?>" },
                        { "data": "<?php _e('Details', 'contactic'); ?>", orderable: false }
                    ]
                });



                $("#datepicker_from").datepicker({
                    "onSelect": function(date) {
                        minDateFilter = new Date(date).getTime();
                        oTable.draw();
                    }
                }).keyup(function() {
                    minDateFilter = new Date(this.value).getTime();
                    oTable.draw();
                });

                $("#datepicker_to").datepicker({
                    "onSelect": function(date) {
                        maxDateFilter = new Date(date).getTime();
                        oTable.draw();
                    }
                }).keyup(function() {
                    maxDateFilter = new Date(this.value).getTime();
                    oTable.draw();
                });

                // Date range filter
                minDateFilter = "";
                maxDateFilter = "";


                $.fn.dataTableExt.afnFiltering.push(
                    function(oSettings, aData, iDataIndex) {
                        if (typeof aData._date == 'undefined') {
                            aData._date = new Date(aData[0]).getTime();
                        }

                        if (minDateFilter && !isNaN(minDateFilter)) {
                            if (aData._date <= minDateFilter) {
                                return false;
                            }
                        }

                        if (maxDateFilter && !isNaN(maxDateFilter)) {
                            if (aData._date >= maxDateFilter) {
                                return false;
                            }
                        }

                        return true;
                    }
                );

                <?php 
                    global $session_handler_error;
                    if ($session_handler_error){
                        ?>
                        $('[data-toggle="tooltip"]').tooltip();
                        <?php
                    }
                ?>
            });
        </script>

        <?php
    }

    public function outputFooter() {



    }




}

