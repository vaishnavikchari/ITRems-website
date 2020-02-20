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

require_once('ContacticPlugin.php');
require_once('CTC_View.php');

class CTCViewImportCsv extends CTC_View
{

    /**
     * @param  $plugin ContacticPlugin
     * @return void
     */
    function display(&$plugin)
    {

        if ($plugin == null) {
            $plugin = new ContacticPlugin;
        }

        $this->pageHeader($plugin);

        $forms = $plugin->getForms();
        $importUrl = $plugin->getAdminUrlPrefix('admin-ajax.php') . 'action=cfdb-importcsv';
        $renameUrl = $plugin->getAdminUrlPrefix('admin-ajax.php') . 'action=cfdb-renameform';
        $clenaupUrl = $plugin->getAdminUrlPrefix('admin-ajax.php') . 'action=cfdb-cleanup';

        ?>
        <h2><?php echo esc_html(__('Import CSV File into Form', 'contactic')); ?></h2>
        <form enctype="multipart/form-data" action="<?php echo $importUrl; ?>" method="post">
            <table>
                <tbody>
                <tr>
                    <td><label for="file"><?php echo esc_html(__('File', 'contactic')); ?></label></td>
                    <td><input type="file" name="file" id="file" size="50" class="file"></td>
                </tr>
                <tr>
                    <td><input type="radio" name="into" id="new" value="new" checked> <?php echo esc_html(__('New Form', 'contactic')); ?></td>
                    <td><input type="text" name="newformname" id="newformname" size="50"/></td>
                </tr>
                <tr>
                    <td><input type="radio" name="into" id="existing" value="into"> <?php echo esc_html(__('Existing Form', 'contactic')); ?></td>
                    <td>
                        <select name="form" id="form">
                            <option value=""></option>
                            <?php
                            foreach ($forms as $formName) {
                                printf("<option value=\"%s\">%s</option>",
                                        esc_attr($formName),
                                        esc_html($formName));
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php echo esc_html(__('Delimiter Character', 'contactic')); ?>
                        <input type="text" name="delim" id="delim" size="1" value=","/>
                        <?php echo esc_html(__('Enclosure Character', 'contactic')); ?>
                        <input type="text" name="encl" id="encl" size="1" value='"'/>
                        <?php echo esc_html(__('Escape Character', 'contactic')); ?>
                        <input type="text" name="esc" id="esc" size="1" value="\"/>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="submit" class="button" name="<?php echo esc_html(__('Import', 'contactic')); ?>" id="importsubmit" value="import">
        </form>

        <script type="text/javascript">
                jQuery('#file').change(function () {
                    var val = jQuery(this).val();
                    val = val.substring(val.lastIndexOf('/') + 1);
                    val = val.substring(val.lastIndexOf('\\') + 1);
                    val = val.replace(/\.([^\.])*$/, "");
                    jQuery('#newformname').val(val);
                });
        </script>
        <form enctype="multipart/form-data" action="<?php echo $renameUrl; ?>" method="post">
            <h2><?php echo esc_html(__('Rename Form', 'contactic')); ?></h2>
            <select name="form" id="form">
                <option value=""></option>
                <?php
                foreach ($forms as $formName) {
                    printf("<option value=\"%s\">%s</option>",
                            esc_attr($formName),
                            esc_html($formName));
                }
                ?>
            </select>
            <td><input type="text" name="newformname" id="renameformname" size="10"/></td>
            <input type="submit" name="rename" id="renamesubmit" class="button" value="<?php echo esc_html(__('Rename', 'contactic')); ?>">
        </form>

        <h2><?php echo esc_html(__('Backup Form to CSV File', 'contactic')); ?></h2>
        <ul>
            <li><?php echo esc_html(__('Backup a form into a CSV file that can be re-imported without loss of data.', 'contactic')); ?></li>
            <li><?php echo esc_html(__('Limitation: this will not export file uploads.', 'contactic')); ?></li>
            <li><?php echo esc_html(__('Limitation: extremely large numbers of records in your form may cause the export operation on your server to run out of memory, thereby not giving you all the rows.', 'contactic')); ?></li>
        </ul>
        <form method="get" action="<?php echo $plugin->getAdminUrlPrefix('admin-ajax.php') ?>">
            <input type="hidden" name="action" value="cfdb-export"/>
            <input type="hidden" name="enc" value="CSV"/>
            <input type="hidden" name="bak" value="true"/>
            <select name="form">
                <option value=""></option>
                <?php
                foreach ($forms as $formName) {
                    printf("<option value=\"%s\">%s</option>",
                            esc_attr($formName),
                            esc_html($formName));
                }
                ?>
            </select>
            <input type="submit" class="button" name="<?php echo esc_html(__('Export', 'contactic')); ?>" value="export">
        </form>
        <h2><?php echo esc_html(__('Data Cleanup', 'contactic')); ?></h2>
        <?php echo esc_html(__('Clean up data that can cause incorrect behavior', 'contactic')); ?>
        <form name="cleanup" action="<?php echo $clenaupUrl; ?>" method="post">
            <input type="submit" class="button" name="cleanup" id="cleanupsubmit" value="<?php echo esc_attr(__('Clean up data', 'contactic')); ?>">
        </form>

    <?php

    }
}