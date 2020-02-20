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

require_once('CTC_View.php');

class CTC_ViewOptions extends CTC_View {

    public function loadStyles() {

	    // Enqueue and register some styles
        wp_enqueue_style('contactic_css_styles', plugins_url('../assets/css/styles.css', __FILE__));
        wp_register_style('contactic_fonts', '//fonts.googleapis.com/css?family=Poppins:300,400,500',null, null);
        wp_register_style('contactic_css_bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
        wp_enqueue_style('contactic_css_bootstrap');
        wp_enqueue_style('contactic_fonts');


	}

    /**
     * @param  $plugin ContacticPlugin
     * @return void
     */
    function display(&$plugin) {
        $this->pageHeader($plugin);
        $this->loadStyles();
        if ($this->outputHeader()) {
            $this->output($plugin);
        }
    }

    public function enqueueSettingsPageScripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script( 'contactic_js_bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js', array('jquery'), '20120206', true );
    }
    
    /**
     * @param $plugin ContacticPlugin
     */
    public function output($plugin) {
        ?>

        <div id="ctc_options_tabs">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class='nav-link active' data-toggle="tab" role="tab" aria-selected="true" aria-controls="integrations" id="saving-tab"  href="#integrations"><?php _e('Integrations', 'contactic'); ?></a>
                </li>
                <li class="nav-item">
                    <a class='nav-link' data-toggle="tab" role="tab" aria-selected="false" aria-controls="security" id="security-tab" href="#security"><?php _e('Security', 'contactic'); ?></a>
                </li>
                <li class="nav-item">
                    <a class='nav-link' data-toggle="tab" role="tab" aria-selected="false" aria-controls="saving" id="saving-tab" href="#saving"><?php _e('Saving', 'contactic'); ?></a>
                </li>
                <li class="nav-item">
                    <a class='nav-link' data-toggle="tab" role="tab" aria-selected="false" aria-controls="export" id="export-tab" href="#export"><?php _e('Export', 'contactic'); ?></a>
                </li>
                <li class="nav-item">
                    <a class='nav-link' data-toggle="tab" role="tab" aria-selected="false" aria-controls="adminview" id="adminview-tab" href="#adminview"><?php _e('Admin View', 'contactic'); ?></a>
                </li>
                <li class="nav-item">
                    <a class='nav-link' data-toggle="tab" role="tab" aria-selected="false" aria-controls="system" id="system-tab" href="#system"><?php _e('System', 'contactic'); ?></a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" role="tabpanel" aria-labelledby="integrations-tab" id="integrations">
                    <h6><?php _e('Capture form submissions from these plugins', 'contactic') ?></h6>
                    <?php
                    $filter = function ($name) {
                        return strpos($name, 'IntegrateWith') === 0 || $name == 'GenerateSubmitTimeInCF7Email' || $name == 'GenerateSourceInCF7Email';
                    };
                    $this->outputSettings($filter, $plugin);
                    ?>
                </div>
                <div class="tab-pane fade" role="tabpanel" aria-labelledby="security-tab" id="security">
                    <?php
                    $filter = function ($name) {
                        return in_array($name, array(
                                'HideAdminPanelFromNonAdmins', 'CanSeeSubmitDataViaShortcode', 'CanSeeSubmitData', 'CanChangeSubmitData',
                                'FunctionsInShortCodes', 'AllowRSS'));
                    };
                    $this->outputSettings($filter, $plugin);
                    ?>
                    <p>
                        <a target="_blank" href="https://cfdbplugin.com/?page_id=625" style="font-weight: bold"><?php _e('Notes on security settings', 'contactic'); ?></a>
                    </p>
                </div>
                <div class="tab-pane fade" role="tabpanel" aria-labelledby="saving-tab"  id="saving">
                    <?php
                    $filter = function ($name) {
                        return in_array($name, array(
                                'TrackingEnabled', 'Timezone', 'NoSaveFields', 'NoSaveForms',
                                'SaveCookieData', 'SaveCookieNames'));
                    };
                    $this->outputSettings($filter, $plugin);
                    ?>
                </div>
                <div class="tab-pane fade" role="tabpanel" aria-labelledby="export-tab"  id="export">
                    <?php
                    $filter = function ($name) {
                        return in_array($name, array(
                                'SubmitDateTimeFormat', 'UseCustomDateTimeFormat', 'ShowFileUrlsInExport'));
                    };
                    $this->outputSettings($filter, $plugin);
                    ?>
                </div>
                <div class="tab-pane fade" role="tabpanel" aria-labelledby="adminview-tab"  id="adminview">
                    <?php
                    $filter = function ($name) {
                        return in_array($name, array(
                                'MaxRows', 'MaxVisibleRows', 'InputMerge', 'HorizontalScroll', 'UseDataTablesJS',
                                'ShowLineBreaksInDataTable', 'ShowQuery'));
                    };
                    $this->outputSettings($filter, $plugin);
                    ?>
                </div>
                <div class="tab-pane fade" role="tabpanel" aria-labelledby="system-tab"  id="system">
                    <?php $this->outputSystemSettings($plugin);
                    $filter = function ($name) {
                        return in_array($name, array(
                                'ErrorOutput', 'DropOnUninstall', '_version'));
                    };
                    $this->outputSettings($filter, $plugin);
                    ?>
                </div>
            </div>
        </div>

        <?php
        $this->outputFooter();
    }

    /**
     * @return bool false means don't display additional contents because PHP version is too old
     */
    public function outputHeader() {
        if (version_compare(phpversion(), '5.3') < 0) {
            printf('<h1>%s</h1>',
                    __('PHP Upgrade Needed', 'contactic'));
            _e('This page requires PHP 5.3 or later on your server.', 'contactic');
            echo '<br/>';
            _e('Your server\'s PHP version: ', 'contactic');
            echo phpversion();
            echo '<br/>';
            printf('<a href="https://wordpress.org/about/requirements/">%s</a>',
                    __('See WordPress Recommended PHP Version', 'contactic'));
            return false;
        }

    ?>

        <div class="wrap">
	    
	    <h1>Options</h1>    
	    
	    
            <form method="post" action="">

            <?php
            $settingsGroup = get_class($this) . '-settings-group';

            settings_fields($settingsGroup);
            return true;

        }

    public function outputFooter() {
        ?>

                <p class="submit" style="text-align: center">
                    <input style="height:auto !important;padding: 8px 16px" type="submit" class="btn btn-primary" value="<?php echo esc_attr(__('Save changes', 'contactic')); ?>"/>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * @param $plugin ContacticPlugin
     */
    public function outputSystemSettings(&$plugin) {
        ?>
        <table class="cfdb-options-table">
            <tbody>
            <?php
            if (function_exists('php_uname')) {
                try { ?>
                    <tr>
                        <td><?php echo esc_html(__('System', 'contactic')); ?></td>
                        <td><?php echo php_uname(); ?></td>
                    </tr>
                    <?php
                } catch (Exception $ex) {
                }
            } ?>
            <tr>
                <td><?php echo esc_html(__('PHP Version', 'contactic')); ?></td>
                <td><?php echo phpversion(); ?>
                    <?php
                    if (version_compare('5.2', phpversion()) > 0) {
                        echo '&nbsp;&nbsp;&nbsp;<span style="background-color: #ffcc00;">';
                        echo esc_html(__('(WARNING: This plugin may not work properly with versions earlier than PHP 5.2)', 'contactic'));
                        echo '</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td><?php echo esc_html(__('MySQL Version', 'contactic')); ?></td>
                <td><?php echo $plugin->getMySqlVersion() ?>
                    <?php
                    echo '&nbsp;&nbsp;&nbsp;<span style="background-color: #ffcc00;">';
                    if (version_compare('5.0', $plugin->getMySqlVersion()) > 0) {
                        echo esc_html(__('(WARNING: This plugin may not work properly with versions earlier than MySQL 5.0)', 'contactic'));
                    }
                    echo '</span>';
                    ?>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            </tbody>
        </table>
        <?php
    }


    /**
     * @param $filterFunction callable
     * @param $plugin ContacticPlugin
     */
    public function outputSettings($filterFunction, &$plugin) {
        $optionMetaData = $plugin->getOptionMetaData();
        if ($optionMetaData == null) {
            return;
        }

        ?>
        <table class="cfdb-options-table">
            <tbody>
            <?php
            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                if ($filterFunction($aOptionKey)) {
                    $displayText = is_array($aOptionMeta) ? $aOptionMeta[0] : $aOptionMeta;
                    $displayText = __($displayText, 'contactic');
                    ?>
                    <tr valign="middle">
                        <td><p><label for="<?php echo $aOptionKey ?>"><?php echo $displayText ?></label></p></td>
                        <td>
                            <?php $plugin->createFormControl($aOptionKey, $aOptionMeta, $plugin->getOption($aOptionKey)); ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>

        <?php

    }

}

