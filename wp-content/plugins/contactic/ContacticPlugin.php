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

require_once('CTC_PluginLifeCycle.php');
require_once('CTC_ShortcodeTable.php');
require_once('CTC_ShortcodeDataTable.php');
require_once('CTC_ShortcodeValue.php');
require_once('CTC_ShortcodeCount.php');
require_once('CTC_ShortcodeJson.php');
require_once('CTC_ShortcodeHtml.php');
require_once('CTC_ShortcodeExportUrl.php');
require_once('CTC_ShortCodeSavePostData.php');
require_once('CTC_ShortCodeSaveFormMakerSubmission.php');
require_once('CTC_Deobfuscate.php');
require_once('CTC_DateFormatter.php');
require_once('CTC_ErrorLog.php');

/**
 * Implementation for CTC_PluginLifeCycle.
 */

class ContacticPlugin extends CTC_PluginLifeCycle implements CTC_DateFormatter {

    public function getPluginDisplayName() {
        return 'Contactic';
    }

    protected function getMainPluginFileName() {
        return 'contact-form-7-db.php';
    }

    public function getOptionMetaData() {
        return array(
            // Integrations
                'IntegrateWithCF7' =>  array('<a target="_cf7" href="https://wordpress.org/plugins/contact-form-7/">Contact Form 7</a>', 'true', 'false'),
                /*'IntegrateWithCF7SavePageTitle' => array('&#x21B3; ' . __('Save Page Title from Contact Form 7 submissions', 'contactic'), 'false', 'true'),
                'IntegrateWithCF7SavePageUrl' => array(__('&#x21B3; ' . 'Save Page URL from Contact Form 7 submissions', 'contactic'), 'false', 'true'),
                'IntegrateWithCF7SaveSubmittedPageUrl' => array(__('&#x21B3; ' . 'Save Submitted From Page URL from Contact Form 7 submissions', 'contactic'), 'false', 'true'),*/
                'GenerateSubmitTimeInCF7Email' => array(__('&#x21B3; ' . 'Generate [submit_time] tag for Contact Form 7 email', 'contactic'), 'false', 'true'),
                'GenerateSourceInCF7Email' => array(__('&#x21B3; ' . 'Include source in Contact Form 7 email', 'contactic'), 'false', 'true'),
                'IntegrateWithCalderaForms' => array('<a target="_caldera" href="https://wordpress.org/plugins/caldera-forms/">Caldera Forms</a>', 'true', 'false'),
                'IntegrateWithCFormsII' => array('<a target="_cf2" href="https://wordpress.org/plugins/cforms2/">CformsII</a>', 'true', 'false'),
                'IntegrateWithEnfoldThemForms' => array('<a target="_enfld" href="http://themeforest.net/item/enfold-responsive-multipurpose-theme/4519990">Enfold Theme</a>', 'true', 'false'),
                'IntegrateWithFSCF' => array('<a target="_fscf" href="https://wordpress.org/plugins/si-contact-form/">Fast Secure Contact Form</a>', 'true', 'false'),
                'IntegrateWithFormCraft' => array('<a target="_fcrft" href="http://codecanyon.net/item/formcraft-premium-wordpress-form-builder/5335056">FormCraft Premium</a>', 'true', 'false'),
                'IntegrateWithFormMaker' => array('<a target="_fmkr" href="https://wordpress.org/plugins/form-maker/">Form Maker</a><br>&#x21B3; Use shortcode: <a target="_doc" href="https://contactic.io/docs/">[cfdb-save-form-maker-post]</a>', 'true', 'false'),
                'IntegrateWithFMS' => array('<a target="_fms" href="http://codecanyon.net/item/forms-management-systemwordpress-frontend-plugin/8978741">Forms Management System</a>', 'true', 'false'),
                'IntegrateWithFormidableForms' => array('<a target="_formidable" href="https://wordpress.org/plugins/formidable/">Formidable Forms</a>', 'true', 'false'),
                'IntegrateWithGravityForms' => array('<a target="_gravityforms" href="http://www.gravityforms.com">Gravity Forms</a>', 'true', 'false'),
                'IntegrateWithJetPackContactForm' => array('<a target="_jetpack" href="https://wordpress.org/plugins/jetpack/">JetPack Contact Form</a>', 'true', 'false'),
                'IntegrateWithNinjaForms' => array('<a target="_ninjaforms" href="https://wordpress.org/plugins/ninja-forms/">Ninja Forms</a>', 'true', 'false'),
                'IntegrateWithQuform' => array('<a target="_quform" href="http://codecanyon.net/item/quform-wordpress-form-builder/706149/">Quform</a>', 'true', 'false'),
                'IntegrateWithVerySimpleContactForm' => array('<a target="_vscf" href="https://wordpress.org/plugins/very-simple-contact-form/">Very Simple Contact Form</a> and <a target="_vscf" href="https://wordpress.org/plugins/very-simple-signup-form/">Very Simple Signup Form</a>', 'true', 'false'),
                'IntegrateWithWrContactForms' => array('<a target="_wr" href="https://wordpress.org/plugins/wr-contactform/">WR ContactForm</a>', 'true', 'false'),

            // Security
                'HideAdminPanelFromNonAdmins' => array(__('Allow only Administrators to see CFDB administration screens', 'contactic'), 'true', 'false'),
                'CanSeeSubmitDataViaShortcode' => array(__('Can See Submission when using shortcodes', 'contactic'),
                        'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber', 'Anyone'),
                'CanSeeSubmitData' => array(__('Can See Submission data', 'contactic'),
                        'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber', 'Anyone'),
                'CanChangeSubmitData' => array(__('Can Edit/Delete Submission data', 'contactic'),
                        'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber', 'Anyone'),
                'FunctionsInShortCodes' => array(__('Allow Any Function in Short Codes', 'contactic') .
                        ' <a target="_blank" href="https://cfdbplugin.com/?page_id=1073">' . __('(Creates a security hole)', 'contactic') . '</a>', 'false', 'true'),
                'AllowRSS' => array(__('Allow RSS URLs', 'contactic') .
                        ' <a target="_blank" href="https://cfdbplugin.com/?p=918">' . __('(Creates a security hole)', 'contactic') . '</a>', 'false', 'true'),

            // Saving
                'TrackingEnabled' => array(__('Track user origin (source & page, if false : admin-ajax.php call on front pages is disabled)', 'contactic'), 'true', 'false'),
                'Timezone' => array(__('Timezone to capture Submit Time. Blank will use WordPress Timezone setting. <a target="_blank" href="http://www.php.net/manual/en/timezones.php">Options</a>', 'contactic')),
                'NoSaveFields' => array(__('Do not save <u>fields</u> in DB named (comma-separated list, no spaces)', 'contactic')),
                'NoSaveForms' => array(__('Do not save <u>forms</u> in DB named (comma-separated list, no spaces)', 'contactic')),
                'SaveCookieData' => array(__('Save Cookie Data with Form Submissions', 'contactic'), 'false', 'true'),
                'SaveCookieNames' => array(__('Save only cookies in DB named (comma-separated list, no spaces, and above option must be set to true)', 'contactic')),

            // Export
                'UseCustomDateTimeFormat' => array(__('Use Custom Date-Time Display Format (below)', 'contactic'), 'true', 'false'),
                'SubmitDateTimeFormat' => array('<a target="_blank" href="http://php.net/manual/en/function.date.php">' . __('Date-Time Display Format', 'contactic') . '</a>'),
                'ShowFileUrlsInExport' => array(__('Export URLs instead of file names for uploaded files', 'contactic'), 'false', 'true'),

            // Admin View
                'MaxRows' => array(__('Maximum number of rows to retrieve from the DB for the Admin display', 'contactic')),
                'MaxVisibleRows' => array(__('#Rows (of maximum above) visible in the Admin datatable', 'contactic')),
                'InputMerge' => array(__('Merge these input fields names to get the e-mail on the <a href="admin.php?page=ContacticPlugin">overview page</a>', 'contactic')),
                'HorizontalScroll' => array(__('Use fixed width in Admin datatable', 'contactic'), 'true', 'false'),
                //'UseDataTablesJS' => array(__('Use Javascript-enabled tables in Admin Database page', 'contactic'), 'true', 'false'),
                'ShowLineBreaksInDataTable' => array(__('Show line breaks in submitted data table', 'contactic'), 'true', 'false'),
                'ShowQuery' => array(__('Show the query used to display results', 'contactic'), 'false', 'true'),

            // System
                'ErrorOutput' => array(__('Error output file or email address', 'contactic')),
                'DropOnUninstall' => array(__('Drop this plugin\'s Database table on uninstall', 'contactic'), 'false', 'true'),
            //'SubmitTableNameOverride' => array(__('Use this table to store submission data rather than the default (leave blank for default)', 'contactic'))
            //'_version' => array('Installed Version'), // For testing upgrades
        );
    }

    public function getWebhookMetaData() {
        return array(
            'PipedriveApiKey' => array(__('Pipedrive API Key', 'contactic')),
            'PipedriveConfig' => array(__('Pipedrive Configuration', 'contactic')),
            'SlackWebhookUrl' => array(__('Slack Webhook Url', 'contactic')),
            'WebhookConf' => array(__('Webhook Configuration', 'contactic'))

        );
    }

    public function getWebhookSlackConf() {
            return array(
                'SlackBotName' => array(__('Slack Bot Name', 'contactic')),
                'SlackMessage' => array(__('Message', 'contactic')),
                'SlackChannel' => array(__('Slack Channel', 'contactic')),
                'SlackIconUrl' => array(__('Icon Url', 'contactic')),
                'formTrigger'  => array(__('Trigger message on form', 'contactic')),
            );
    }

    public function settingsPage() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'community-yard-sale'));
        }

        $optionMetaData = $this->getOptionMetaData();

        // Save Posted Options
        if ($optionMetaData != null) {
            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                if (isset($_POST[$aOptionKey])) {
                    $this->updateOption($aOptionKey, sanitize_text_field($_POST[$aOptionKey]));
                }
            }
        }
        require_once('view/CTC_ViewOptions.php');
        $optionsView = new CTC_ViewOptions();
        $optionsView->display($this);
    }

    public function webhooksPage() {

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'community-yard-sale'));
        }

        // Save webhook initial conf
        foreach ($this->getWebhookMetaData() as $aWebhookKey => $aWebhookMeta) {
            if (isset($_POST[$aWebhookKey])) {
                $this->updateOption($aWebhookKey, sanitize_text_field($_POST[$aWebhookKey]));
            }
        }

        // Delete Webhook
        if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
            // delete webhook
            $webhookConf = $this->getOption('WebhookConf', array(), true);
            if(isset($webhookConf[(int)$_GET['remove']])) {
                unset($webhookConf[(int)$_GET['remove']]);
                $this->updateOption('WebhookConf', $webhookConf);
                echo '<script type="text/javascript">';
                echo 'window.location = "'.get_admin_url() . 'admin.php?page=' . $this->getSlug('webhooks').'";';
                echo '</script>';
                exit;
            }
        }


        // Save Webhook id conf
        if (isset($_POST['webhook_id']) && isset($_POST['webhook_type'])) {

            $webhookConf = $this->getOption('WebhookConf', array(), true);
            if ($webhookConf  === false) $webhookConf = array();

            if ($_POST['webhook_type'] == 'slack') {

                $conf = array();
                foreach ($this->getWebhookSlackConf() as $aConfKey => $aWConfMeta) {
                    if (isset($_POST[$aConfKey])) {

                        if (is_array($_POST[$aConfKey])) {

                            foreach($_POST[$aConfKey] as $arrK => $arrV) {
                                $conf[$aConfKey][$arrK] = sanitize_text_field($arrV);
                            }

                        } else {
                            $conf[$aConfKey] = sanitize_text_field($_POST[$aConfKey]);
                        }

                    }
                }
                $conf['webhook_type'] = $_POST['webhook_type'];

                // if webhook id exists in webhook conf
                if ($_POST['webhook_id'] == '') {
                    $webhookConf[] = $conf;
                } else {
                    $webhookConf[$_POST['webhook_id']] = $conf;
                }

                $this->updateOption('WebhookConf', $webhookConf);
            }
        }

        require_once('view/CTC_ViewWebhooks.php');
        $webhooksView = new CTC_ViewWebhooks();
        $webhooksView->display($this);
    }

    public function getOptionValueI18nString($optionValue) {
        switch ($optionValue) {
            case 'true':
                return __('true', 'contactic');
            case 'false':
                return __('false', 'contactic');

            case 'Administrator':
                return __('Administrator', 'contactic');
            case 'Editor':
                return __('Editor', 'contactic');
            case 'Author':
                return __('Author', 'contactic');
            case 'Contributor':
                return __('Contributor', 'contactic');
            case 'Subscriber':
                return __('Subscriber', 'contactic');
            case 'Anyone':
                return __('Anyone', 'contactic');
        }
        return $optionValue;
    }

    public function upgrade() {
        global $wpdb;
        $upgradeOk = true;
        $savedVersion = $this->getVersionSaved();

        if (!$savedVersion) {
            // Was some code here from pre-version 1.2 that I removed b/c it is very old and not relevant.

            // A user reported an issue where the saved version in the DB would get cleared out
            // after using the plugin for a week or so. Weird and not reproducible. But the problem is that
            // it would make it re-run through the upgrades below even though they need not be applied.
            // the ALTER TABLE statements would fail and product errors on the page.
            // Without solving the underlying problem, I'm going to have upgrade actions be skipped
            // in the case where no version is recorded in the DB.
        } else {

            // if ($this->isVersionLessThan($savedVersion, '1.0')) {
            //  Do some stuff...
            //}
            if ($this->isVersionLessThan($savedVersion, '1.0.2')) {
                $tableName = $this->getSubmitsTableName_raw();
                $wpdb->show_errors();
                $rows = $wpdb->get_results("SELECT DISTINCT `submit_time`, `form_name` FROM `$tableName` WHERE `form_name` LIKE 'Page ID % - %'");
                foreach ($rows as $aRow) {
                    if (preg_match('/^Page ID (\d+) - .*$/', $aRow->form_name, $matches)){
                        $formName = str_replace('Page ID '.$matches[1].' - ', '', $aRow->form_name);
                        $page_id = $matches[1];
                        $wpdb->query("INSERT INTO `$tableName` (`submit_time`, `form_name`, `field_name`, `field_value`, `field_order`) VALUES (".$aRow->submit_time.", '$formName', '_ctc_last_page_ID', '$page_id', 1003)");
                        $wpdb->query("UPDATE `$tableName` SET `form_name` = '$formName' WHERE `submit_time`=".$aRow->submit_time);
                    }
                }
                $wpdb->hide_errors();
            }
        }

        // Post-upgrade, set the current version in the options
        $codeVersion = $this->getVersion();
        if ($upgradeOk && $codeVersion && $savedVersion != $codeVersion) {
            $this->saveInstalledVersion($codeVersion);
        }
    }

    /**
     * Called by install()
     * You should: Prefix all table names with $wpdb->prefix
     * Also good: additionally use the prefix for this plugin:
     * $table_name = $wpdb->prefix . $this->prefix('MY_TABLE');
     * @return void
     */
    protected function installDatabaseTables() {
        global $wpdb;
        $tableName = $this->getSubmitsTableName_raw();
        $wpdb->show_errors();
        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
            `submit_time` DECIMAL(16,4) NOT NULL,
            `form_name` VARCHAR(127) CHARACTER SET utf8,
            `field_name` VARCHAR(127) CHARACTER SET utf8,
            `field_value` LONGTEXT CHARACTER SET utf8,
            `field_order` INTEGER,
            `file` LONGBLOB)");
        $wpdb->query("ALTER TABLE `$tableName` ADD INDEX `submit_time_idx` ( `submit_time` )");
        $wpdb->query("ALTER TABLE `$tableName` ADD INDEX `form_name_idx` ( `form_name` )");
        $wpdb->query("ALTER TABLE `$tableName` ADD INDEX `field_name_idx` ( `field_name` )");
        $tableName = $this->getSTTableName_raw();
        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
            `submit_time` DECIMAL(16,4) NOT NULL, 
            PRIMARY KEY (submit_time))");
        $tableName = $this->getNoticesTableName_raw();
        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (`id` INT(10) unsigned NOT NULL AUTO_INCREMENT, `type` VARCHAR(20) CHARACTER SET utf8, `message` TEXT CHARACTER SET utf8, `dismissed` TINYINT(1) unsigned NOT NULL DEFAULT '0', PRIMARY KEY (`id`))");

        // check if cf7dbplugin db tables exist and get data into our new plugin tables
        $table_prefix = $wpdb->prefix.'cf7dbplugin_';
        if($wpdb->get_var("SHOW TABLES LIKE '".$table_prefix."st'") == $table_prefix.'st') {
            if($wpdb->get_var("SHOW TABLES LIKE '".$table_prefix."submits'") == $table_prefix.'submits') {
                // the 2 tables are found, ok let's get data and insert it in our tables
                $tableName = $this->getSTTableName_raw();
                $wpdb->query("INSERT INTO `$tableName` 
                                SELECT * FROM `".$table_prefix."st`");
                $tableName = $this->getSubmitsTableName_raw();
                $wpdb->query("INSERT INTO `$tableName` 
                                SELECT * FROM `".$table_prefix."submits`");
            }
        }
        $wpdb->hide_errors();
    }


    /**
     * Called by uninstall()
     * You should: Prefix all table names with $wpdb->prefix
     * Also good: additionally use the prefix for this plugin:
     * $table_name = $wpdb->prefix . $this->prefix('MY_TABLE');
     * @return void
     */
    protected function unInstallDatabaseTables() {
        if ('true' == $this->getOption('DropOnUninstall', 'false', true)) {
            global $wpdb;
            $tableName = $this->getSubmitsTableName();
            $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
            //        $tables = array('SUBMITS');
            //        foreach ($tables as $aTable) {
            //            $tableName = $this->prefixTableName($aTable);
            //            $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
            //        }
        }
    }

    protected function initOptions() {
        // By default ignore CF7 metadata fields
        $this->addOption('NoSaveFields', '/.*wpcf7.*/,_wpnonce,g-recaptcha-response');
        $this->addOption('InputMerge', '%email%,%e-mail%,%e_mail%,%mail%');
    }

    public function add_wpcf7_noSaveFields() {
        $nsfArray = explode(',', $this->getOption('NoSaveFields','', true));
        $wpcf7Fields = array('/.*wpcf7.*/', '_wpnonce');
        foreach ($wpcf7Fields as $aWpcf7) {
           if (!in_array($aWpcf7, $nsfArray)) {
               $nsfArray[] = $aWpcf7;
           }
        }
        $this->updateOption('NoSaveFields', implode(',', $nsfArray));
    }

    public function delete_wpcf7_fields($formName) {
        global $wpdb;
        $wpdb->query($wpdb->prepare(
            'delete from `' . $this->getSubmitsTableName() .
                    "` where `form_name` = '%s' and `field_name` in ('_wpcf7', '_wpcf7_version', '_wpcf7_unit_tag', '_wpnonce', '_wpcf7_is_ajax_call', '_wpcf7_captcha_challenge_captcha')",
            $formName));
    }

    // cleanup first to avoid conflicts
    public function clean_js(){
        global $wp_scripts;
        foreach($wp_scripts->queue as $handle){
            if ($handle != 'contactic_js_bootstrap' && (stripos($handle, 'bootstrap') !== false)){
                wp_dequeue_script($handle);
                wp_deregister_style($handle);
            }
        }
    }

    public function clean_styles(){
        global $wp_styles;
        foreach($wp_styles->queue as $handle){
            if ($handle != 'contactic_css_bootstrap' && stripos($handle, 'bootstrap') !== false){
                wp_dequeue_style($handle);
                wp_deregister_style($handle);
            }
        }
    }

    public function addActionsAndFilters() {
        // Admin notices
        add_action('admin_notices', array(&$this, 'addAdminNotices'));
        add_action( 'wp_ajax_dismiss_contactic_notice', array(&$this, 'ctc_dismiss_notice'));

        // Cronjob
        add_action ('ctc_check_notices', array(&$this, 'ctc_check_notices'));

        // save the post id in session
        add_action( 'wp_footer', array(&$this, 'ctc_get_the_page'));
        add_action( 'wp_ajax_nopriv_get_the_page', array(&$this, 'ctc_get_the_page_ajax'));
        add_action( 'wp_ajax_get_the_page', array(&$this, 'ctc_get_the_page_ajax'));

        // Add the Admin Config page for this plugin

        // Add Config page as a top-level menu item on the Admin page
        add_action('admin_menu', array(&$this, 'createAdminMenu'));

        // Add Database Options page
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

        // ajax get submit details to fill the modal
        add_action( 'wp_ajax_submit_details', array(&$this, 'submitDetails'));
        // ajax delete a contact (ex : from the details modal)
        add_action( 'wp_ajax_submit_delete', array(&$this, 'submitDelete'));
        // ajax usefulness update
        add_action( 'wp_ajax_submit_usefulness', array(&$this, 'submitUsefulnessChange'));
        // ajax status update
        add_action( 'wp_ajax_submit_status', array(&$this, 'submitStatusChange'));
        // ajax contacts load for datatables in serverside mode - contacts page
        add_action( 'wp_ajax_load_contacts', array(&$this, 'loadContacts'));

        // Hook into Contact Form 7 when a form post is made to save the data to the DB
        if ($this->getOption('IntegrateWithCF7', 'true', true) == 'true') {
            require_once('CTC_IntegrationContactForm7.php');
            $integration = new CTC_IntegrationContactForm7($this);
            $integration->registerHooks();
        }

        // Hook into Fast Secure Contact Form
        if ($this->getOption('IntegrateWithFSCF', 'true', true) == 'true') {
            require_once('CTC_IntegrationFSCF.php');
            $integration = new CTC_IntegrationFSCF($this);
            $integration->registerHooks();
        }

        // Hook into JetPack Contact Form
        if ($this->getOption('IntegrateWithJetPackContactForm', 'true', true) == 'true') {
            require_once('CTC_IntegrationJetPack.php');
            $integration = new CTC_IntegrationJetPack($this);
            $integration->registerHooks();
        }

        // Hook into Gravity Forms
        if ($this->getOption('IntegrateWithGravityForms', 'true', true) == 'true') {
            require_once('CTC_IntegrationGravityForms.php');
            $integration = new CTC_IntegrationGravityForms($this);
            $integration->registerHooks();
        }

        // Hook into Formidable Forms
        if ($this->getOption('IntegrateWithFormidableForms', 'true', true) == 'true') {
            require_once('CTC_IntegrationFormidableForms.php');
            $integration = new CTC_IntegrationFormidableForms($this);
            $integration->registerHooks();
        }

        // Hook to work with WR ContactForms
        if ($this->getOption('IntegrateWithWrContactForms', 'true', true) == 'true') {
            require_once('CTC_IntegrationWRContactForm.php');
            $integration = new CTC_IntegrationWRContactForm($this);
            $integration->registerHooks();
        }

        // Hook to work with Quform
        if ($this->getOption('IntegrateWithQuform', 'true', true) == 'true') {
            require_once('CTC_IntegrationQuform.php');
            $integration = new CTC_IntegrationQuform($this);
            $integration->registerHooks();
        }

        // Hook to work with Ninja Forms
        if ($this->getOption('IntegrateWithNinjaForms', 'true', true) == 'true') {
            require_once('CTC_IntegrationNinjaForms.php');
            $integration = new CTC_IntegrationNinjaForms($this);
            $integration->registerHooks();
        }

        // Hook to work with Caldera Forms Forms
        if ($this->getOption('IntegrateWithCalderaForms', 'true', true) == 'true') {
            require_once('CTC_IntegrationCalderaForms.php');
            $integration = new CTC_IntegrationCalderaForms($this);
            $integration->registerHooks();
        }

        // Hook to work with Enfold theme forms
        if ($this->getOption('IntegrateWithEnfoldThemForms', 'true', true) == 'true') {
            require_once('CTC_IntegrationEnfoldTheme.php');
            $integration = new CTC_IntegrationEnfoldTheme($this);
            $integration->registerHooks();
        }

        // Hook to work with CFormsII
        if ($this->getOption('IntegrateWithCFormsII', 'true', true) == 'true') {
            require_once('CTC_IntegrationCFormsII.php');
            $integration = new CTC_IntegrationCFormsII($this);
            $integration->registerHooks();
        }

        // Hook to work with FormCraft
        if ($this->getOption('IntegrateWithFormCraft', 'true', true) == 'true') {
            require_once('CTC_IntegrationFromCraft.php');
            $integration = new CTC_IntegrationFromCraft($this);
            $integration->registerHooks();
        }

        // Hook to work with Very Simple Contact Form
        if ($this->getOption('IntegrateWithVerySimpleContactForm', 'true', true) == 'true') {
            require_once('CTC_IntegrationVerySimpleContactForm.php');
            $integration = new CTC_IntegrationVerySimpleContactForm($this);
            $integration->registerHooks();
        }

        // Hook to work with Forms Management System
        if ($this->getOption('IntegrateWithFMS', 'true', true) == 'true') {
            require_once('CTC_IntegrationFMS.php');
            $integration = new CTC_IntegrationFMS($this);
            $integration->registerHooks();
        }

        // Have our own hook to receive form submissions independent of other plugins
        add_action('cfdb_submit', array(&$this, 'saveFormData'));

        // Register Export URL
        add_action('wp_ajax_nopriv_cfdb-export', array(&$this, 'ajaxExport'));
        add_action('wp_ajax_cfdb-export', array(&$this, 'ajaxExport'));

        // Register Get File URL
        add_action('wp_ajax_nopriv_cfdb-file', array(&$this, 'ajaxFile'));
        add_action('wp_ajax_cfdb-file', array(&$this, 'ajaxFile'));

        // Register Get Form Fields URL
        add_action('wp_ajax_nopriv_cfdb-getFormFields', array(&$this, 'ajaxGetFormFields'));
        add_action('wp_ajax_cfdb-getFormFields', array(&$this, 'ajaxGetFormFields'));

        // Register Validate submit_time value (used in short code builder page)
        add_action('wp_ajax_nopriv_cfdb-validate-submit_time', array(&$this, 'ajaxValidateSubmitTime'));
        add_action('wp_ajax_cfdb-validate-submit_time', array(&$this, 'ajaxValidateSubmitTime'));

        // Login via Ajax instead of login form
        add_action('wp_ajax_nopriv_cfdb-login', array(&$this, 'ajaxLogin'));
        add_action('wp_ajax_cfdb-login', array(&$this, 'ajaxLogin'));

        add_action('wp_ajax_nopriv_cfdb-cleanup', array(&$this, 'ajaxCleanup'));
        add_action('wp_ajax_cfdb-cleanup', array(&$this, 'ajaxCleanup'));

        // Shortcode to add a table to a page
        $sc = new CTC_ShortcodeTable();
        $sc->register(array('cf7db-table', 'cfdb-table')); // cf7db-table is deprecated

        // Shortcode to add a DataTable
        $sc = new CTC_ShortcodeDataTable();
        $sc->register('cfdb-datatable');

        // Shortcode to add a JSON to a page
        $sc = new CTC_ShortcodeJson();
        $sc->register('cfdb-json');

        // Shortcode to add a value (just text) to a page
        $sc = new CTC_ShortcodeValue();
        $sc->register('cfdb-value');

        // Shortcode to add entry count to a page
        $sc = new CTC_ShortcodeCount();
        $sc->register('cfdb-count');

        // Shortcode to add values wrapped in user-defined html
        $sc = new CTC_ShortcodeHtml();
        $sc->register('cfdb-html');

        // Shortcode to generate Export URLs
        $sc = new CTC_ShortcodeExportUrl();
        $sc->register('cfdb-export-link');

        // Shortcode to save data from non-CF7/FSCF forms
        $sc = new CTC_ShortCodeSavePostData();
        $sc->register('cfdb-save-form-post');

        // Shortcode to save data Form Maker submissions
        $sc = new CTC_ShortCodeSaveFormMakerSubmission();
        $sc->register('cfdb-save-form-maker-post');
    }

    public function getCredentialsFromAjaxCall() {
        // Login the user
        $key = 'kx82XcPjq8q8S!xafx%$&7p6';
        $creds = array();
        $user = null;
        $password = null;

        if (!empty($_REQUEST['l'])) {
            $userPass = CTC_Deobfuscate::deobfuscateHexString(sanitize_text_field($_REQUEST['l']), $key);
            $userPass = explode('/', $userPass, 2);
            $count = count($userPass);
            if ($count >= 1) {
                $user = $userPass[0];
                if ($count > 1) {
                    $password = $userPass[1];
                }
            }
        }

        if (!$user) {
            $user = !empty($_REQUEST['username']) ? sanitize_text_field($_REQUEST['username']) : null;
        }
        if (!$user) {
            $user = !empty($_REQUEST['user_login']) ? sanitize_text_field($_REQUEST['user_login']) : null;
        }

        if (!$password) {
            $password = !empty($_REQUEST['password']) ? sanitize_text_field($_REQUEST['password']) : null;
        }
        if (!$password) {
            $password = !empty($_REQUEST['user_password']) ? sanitize_text_field($_REQUEST['user_password']) : null;
        }

        if ($user && $password) {
            $creds['user_login'] = $user;
            $creds['user_password'] = $password;
            $creds['remember'] = !empty($_REQUEST['rememberme']) ? sanitize_text_field($_REQUEST['rememberme']) : null;
        }

        return $creds;
    }

    public function ajaxLogin() {
        if (! is_user_logged_in()) {
            $creds = $this->getCredentialsFromAjaxCall();
            $user = wp_signon($creds, false);
            if (is_wp_error($user)) {
                $this->ajaxRedirectToLogin();
            }
            wp_set_current_user($user->ID);
        }

        // User is logged in. Now do the requested action
        if (!empty($_REQUEST['cfdb-action'])) {
            switch ($_REQUEST['cfdb-action']) {
                case 'cfdb-export':
                    if (!$this->canUserDoRoleOption('CanSeeSubmitData')) {
                        echo '<strong>ERROR</strong>: user ' . $creds['user_login'] . ' is not authorized to export CFDB data';
                        die;
                    }
                    $this->ajaxExport();
                    break;

                default:
                    break;
            }
        }
        die;
    }

    public function ajaxRedirectToLogin() {
        $url = home_url();
        $slashPos = strpos($url, '//');
        $slashPos = strpos($url, '/', $slashPos + 2);
        if ($slashPos !== false) {
            $url = substr($url, 0, $slashPos);
        }
        $redirectUrl = "$url${_SERVER['REQUEST_URI']}";
        $loginUrl = wp_login_url($redirectUrl);
        header("Location: $loginUrl");
        die();
    }

    public function ajaxCheckForLoginAndDoRedirect() {
        if ('Anyone' != $this->getRoleOption('CanSeeSubmitData')) {
            if (!is_user_logged_in()) {
                $creds = $this->getCredentialsFromAjaxCall();
                if (!empty($creds)) {
                    $user = wp_signon($creds, false);
                    if (is_wp_error($user)) {
                        $this->ajaxRedirectToLogin();
                    }
                } else {
                    $this->ajaxRedirectToLogin();
                }
            }
        }
    }

    public function ajaxExport() {
        $this->ajaxCheckForLoginAndDoRedirect();
        require_once('CTC_PluginExporter.php');
        CTC_PluginExporter::doExportFromPost();
        die();
    }

    public function ajaxFile() {
        $this->ajaxCheckForLoginAndDoRedirect();
        require_once('CTC_Die.php');
        if (!$this->canUserDoRoleOption('CanSeeSubmitData') &&
            !$this->canUserDoRoleOption('CanSeeSubmitDataViaShortcode')) {
            CTC_Die::wp_die(__('You do not have sufficient permissions to access this page.', 'contactic'));
        }
        $submitTime = stripslashes(sanitize_text_field($_REQUEST['s']));
        $formName = stripslashes(sanitize_text_field($_REQUEST['form']));
        $fieldName = stripslashes(sanitize_text_field($_REQUEST['field']));
        if (!$submitTime || !$formName || !$fieldName) {
            CTC_Die::wp_die(__('Missing form parameters', 'contactic'));
        }
        $fileInfo = (array)$this->getFileFromDB($submitTime, $formName, $fieldName);
        if ($fileInfo == null) {
            CTC_Die::wp_die(__('No such file.', 'contactic'));
        }

        require_once('CTC_MimeTypeExtensions.php');
        $mimeMap = new CTC_MimeTypeExtensions();
        $mimeType = $mimeMap->get_type_by_filename($fileInfo[0]);
        if (ob_get_level()) {
            ob_end_clean(); // Fix bug where download files can be corrupted
        }
        ob_end_clean(); // Not sure why have to do this on some sites
        if ($mimeType) {
            header('Content-Type: ' . $mimeType);
            header("Content-Disposition: inline; filename=\"$fileInfo[0]\"");
        }
        else {
            header("Content-Disposition: attachment; filename=\"$fileInfo[0]\"");
        }

        echo($fileInfo[1]);
        die();
    }

    public function ajaxGetFormFields() {
        if (!$this->canUserDoRoleOption('CanSeeSubmitData') || !isset($_REQUEST['form'])) {
            die();
        }
        header('Content-Type: application/json; charset=UTF-8');
        header("Pragma: no-cache");
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
        global $wpdb;
        $tableName = $this->getSubmitsTableName();
        $formName = sanitize_text_field($_REQUEST['form']);
        $formNameList = explode(',', $formName);
        if (count($formNameList) > 1) {
            $formNameList[] = $formName;
        }
        $count = count($formNameList);
        $inClausePlaceholders = array_fill(0, $count, '%s');
        $inCloudFormat = implode(', ', $inClausePlaceholders);
        $sql = "SELECT DISTINCT `field_name` FROM `$tableName` WHERE `form_name` IN ($inCloudFormat) ORDER BY field_order";
        $sql = $wpdb->prepare($sql, $formNameList);
        $rows = $wpdb->get_results($sql);
        $fields = array();
        if (!empty($rows)) {
            $fields[] = 'Submitted';
            foreach ($rows as $aRow) {
                $fields[] = $aRow->field_name;
            }
            $fields[] = 'submit_time';
        }
        echo json_encode($fields);
        die();
    }

    public function ajaxValidateSubmitTime() {
        header('Content-Type: text/plain; charset=UTF-8');
        header("Pragma: no-cache");
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
        $submitTime = sanitize_text_field($_REQUEST['submit_time']);

        $invalid = false;
        $time = $submitTime;
        if (!is_numeric($submitTime)) {
            if (version_compare(phpversion(), '5.1.0') == -1) {
                $invalid = -1;
            }
            $this->setTimezone();
            $time = strtotime($submitTime);
        }
        if ($invalid === $time) {
            echo esc_html(__('Invalid: ', 'contactic'));
        }
        else {
            echo esc_html(__('Valid: ', 'contactic'));
        }

        echo "'$submitTime' = $time";

        if ($invalid !== $time) {
            echo " = " . $this->formatDate($time);
        }
        die();
    }

    public function ajaxCleanup() {
        if (!$this->canUserDoRoleOption('CanChangeSubmitData')) {
            die();
        }
        header('Content-Type: text/plain; charset=UTF-8');
        header("Pragma: no-cache");
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
        echo esc_html(__('Checking for conflicting entries. This may take a few minutes.', 'contactic'));
        echo "\n";
        require_once('CTC_CleanupData.php');
        $cleanup = new CTC_CleanupData($this);

        echo esc_html(__('Phase 1 of 3...', 'contactic'));
        $count = $cleanup->cleanupForms();
        echo esc_html(__('Database entries fixed: ', 'contactic'));
        echo ($count);
        echo "\n";

        echo esc_html(__('Phase 2 of 3...', 'contactic'));
        $count = $cleanup->deleteEmptyEntries();
        echo esc_html(__('Database entries fixed: ', 'contactic'));
        echo ($count);
        echo "\n";

        echo esc_html(__('Phase 3 of 3...', 'contactic'));
        $count = $cleanup->cleanupEntries();
        echo esc_html(__('Database entries fixed: ', 'contactic'));
        echo ($count);
        echo "\n";
        die();
    }

    public function addSettingsSubMenuPage() {
//        $this->requireExtraPluginFiles();
//        $displayName = $this->getPluginDisplayName();
//        add_submenu_page('wpcf7', //$this->getSlug('sumbissions'),
//                         $displayName . ' Options',
//                         __('Database Options', 'contactic'),
//                         'manage_options',
//                         get_class($this) . 'Settings',
//                         array(&$this, 'settingsPage'));
    }


    public function generateSubmitTime() {
        global $wpdb;
        $table = $this->getSTTableName();
        $time = 0;
        $noDuplicate = false;
        $tries = 0;
        $wpdb->hide_errors(); // avoid submission page from hanging on DB error like table not exists
        while (!$noDuplicate && ++$tries <= 20) {
            // $tries breaks out of loop which would be infinite when table to insert into does not exist
            $time = function_exists('microtime') ? microtime(true) : time();
            // Bug fix: on some systems microtime is in scientific notation when converted to string
            $time = number_format($time, 4, '.', '');
            // Avoid duplicate submission with the same submit_time in the DB
            $noDuplicate = $wpdb->query($wpdb->prepare("INSERT INTO $table VALUES (%s)", $time));
        }
        return $time;
    }

    /**
     * Callback for saving form data. Originally based on Contact Form 7's callback object
     * with submission data in $cf7->posted_data. However that has changed over time.
     * FSCF sends an object matching this data structure. Other form plugins have their data
     * transformed into the expected data structure via other callbacks in this class
     * @param $cf7 object containing posted data
     * @return bool
     */
    public function saveFormData($cf7) {
        try {
            if (
               !empty($cf7->posted_data['submit_time']) &&
                    (is_numeric($cf7->posted_data['submit_time']) ||
                            // Looks like is_numeric may fail on decimal '.' when ',' is the localization
                            preg_match('/^\d+\.?\d*$/', $cf7->posted_data['submit_time']))
            ) {
                $time = $cf7->posted_data['submit_time'];
                unset($cf7->posted_data['submit_time']);
                unset($cf7->posted_data['submit_url']);
            } else {
                $time = $this->generateSubmitTime();
            }
            $cf7->submit_time = $time;

            $ip = $this->getIPAddress();

            // Set up to allow all this data to be filtered
            $cf7->ip = $ip;
            $user = null;
            if (function_exists('is_user_logged_in') && is_user_logged_in()) {
                $current_user = wp_get_current_user(); // WP_User
                $user = $current_user->user_login;
            }
            $cf7->user = $user;
            try {
                $newCf7 = apply_filters('cfdb_form_data', $cf7);
                if ($newCf7 && is_object($newCf7)) {
                    $cf7 = $newCf7;
                    $time = $cf7->submit_time;
                    $ip = $cf7->ip;
                    $user = $cf7->user;
                }
                else {
                    //$this->getErrorLog()->log('CFDB Error: No or invalid value returned from "cfdb_form_data" filter: ' .
                    //        print_r($newCf7, true));
                    // Returning null from cfdb_form_data is a way to stop from saving the form
                    return true;
                }
            }
            catch (Exception $ex) {
                $this->getErrorLog()->logException($ex);
            }

            // Get title after applying filter
            if (isset($cf7->title)) {
                $title = $cf7->title;
            } else {
                $title = 'Unknown';
            }
            $title = stripslashes($title);

            if ($this->fieldMatches($title, $this->getNoSaveForms())) {
                return true; // Don't save in DB
            }

            $tableName = $this->getSubmitsTableName();
            $parametrizedQuery = "INSERT INTO `$tableName` (`submit_time`, `form_name`, `field_name`, `field_value`, `field_order`) VALUES (%s, %s, %s, %s, %s)";
            $parametrizedFileQuery = "INSERT INTO `$tableName` (`submit_time`, `form_name`, `field_name`, `field_value`, `field_order`, `file`) VALUES (%s, %s, %s, %s, %s, %s)";
            $order = 0;
            $noSaveFields = $this->getNoSaveFields();
            $foundUploadFiles = array();
            global $wpdb;

            $cf7->posted_data['_ctc_referer'] = (isset($_SESSION["original_referer"])) ? $_SESSION['original_referer'] : 'Unknown';
            $cf7->posted_data['_ctc_usefulness'] = 'Undefined';
            $cf7->posted_data['_ctc_status'] = 'Todo';
            //$cf7->posted_data['_ctc_user'] = null;

            if (isset($_SESSION['_ctc_last_page_title']) && isset($_SESSION['_ctc_last_page_uri'])){
                $cf7->posted_data['_ctc_last_page_title'] = $_SESSION['_ctc_last_page_title'];
                $cf7->posted_data['_ctc_last_page_uri'] = $_SESSION['_ctc_last_page_uri'];
            }
            
            $slack_message = '';

            foreach ($cf7->posted_data as $name => $value) {
                $nameClean = stripslashes($name);
                if ($this->fieldMatches($nameClean, $noSaveFields)) {
                    continue; // Don't save in DB
                }

                $value = is_array($value) ? implode($value, ', ') : $value;
                $valueClean = stripslashes($value);

                // Check if this is a file upload field
                $didSaveFile = false;
                if ($cf7->uploaded_files && isset($cf7->uploaded_files[$nameClean])) {
                    $foundUploadFiles[] = $nameClean;
                    $filePath = $cf7->uploaded_files[$nameClean];
                    if ($filePath) {
                        $content = file_get_contents($filePath);
                        $didSaveFile = $wpdb->query($wpdb->prepare($parametrizedFileQuery,
                            $time,
                            $title,
                            $nameClean,
                            $valueClean,
                            $order++,
                            $content));
                        if (!$didSaveFile) {
                            $this->getErrorLog()->log("CFDB Error: could not save uploaded file, field=$nameClean, file=$filePath");
                        }
                    }
                }
                if (!$didSaveFile) {
                    $wpdb->query($wpdb->prepare($parametrizedQuery,
                        $time,
                        $title,
                        $nameClean,
                        $valueClean,
                        $order++));
                    if (substr($nameClean, 0, 5) != '_ctc_'){
                        $slack_message .= '*'.$nameClean.'*'.chr(10);
                        $slack_message .= $valueClean.chr(10).chr(10);
                    }
                }
            }

            // Since Contact Form 7 version 3.1, it no longer puts the names of the files in $cf7->posted_data
            // So check for them only only in $cf7->uploaded_files
            // Update: This seems to have been reversed back to the original in Contact Form 7 3.2 or 3.3
            if ($cf7->uploaded_files && is_array($cf7->uploaded_files)) {
                foreach ($cf7->uploaded_files as $field => $filePath) {
                    if (!in_array($field, $foundUploadFiles) &&
                            $filePath &&
                            !$this->fieldMatches($field, $noSaveFields)) {
                        $fileName = basename($filePath);
                        $content = file_get_contents($filePath);
                        $didSaveFile = $wpdb->query($wpdb->prepare($parametrizedFileQuery,
                            $time,
                            $title,
                            $field,
                            $fileName,
                            $order++,
                            $content));
                        if (!$didSaveFile) {
                            $this->getErrorLog()->log("CFDB Error: could not save uploaded file, field=$field, file=$filePath");
                        }
                    }
                }
            }

            // Save Cookie data if that option is true
            if ($this->getOption('SaveCookieData', 'false', true) == 'true' && is_array($_COOKIE)) {
                $saveCookies = $this->getSaveCookies();
                foreach ($_COOKIE as $cookieName => $cookieValue) {
                    if (empty($saveCookies) || $this->fieldMatches($cookieName, $saveCookies)) {
                        $wpdb->query($wpdb->prepare($parametrizedQuery,
                            $time,
                            $title,
                            'Cookie ' . $cookieName,
                            $cookieValue,
                            $order++));
                    }
                }
            }

            // If the submitter is logged in, capture his id
            if ($user && !$this->fieldMatches('Submitted Login', $noSaveFields)) {
                $order = ($order < 9999) ? 9999 : $order + 1; // large order num to try to make it always next-to-last
                $wpdb->query($wpdb->prepare($parametrizedQuery,
                                            $time,
                                            $title,
                                            'Submitted Login',
                                            $user,
                                            $order));
            }

            // fire webhook if needed
            $slackWebhook = $this->getOption('SlackWebhookUrl', false);
            $webhook = $this->getOption('WebhookConf', array(), true);


            if ($webhook !== false && $slackWebhook !== '') {



                foreach ($webhook as $webhookKey => $webhookConf) {
                    if($webhookConf['webhook_type'] == 'slack' && $slackWebhook !== '') {
                        if ($webhookConf['formTrigger'][0] == '*') {
                            $this->slackNotification($slackWebhook, $webhookConf, $slack_message, $title);
                        } else if (in_array($title, $webhookConf['formTrigger'])) {
                            $this->slackNotification($slackWebhook, $webhookConf, $slack_message, $title);
                        } else {
                            // do nothing...
                        }

                    }

                }
            }


                // Capture the IP Address of the submitter
            if (!$this->fieldMatches('Submitted From', $noSaveFields)) {
                $order = ($order < 10000) ? 10000 : $order + 1; // large order num to try to make it always last
                $wpdb->query($wpdb->prepare($parametrizedQuery,
                        $time,
                        $title,
                        'Submitted From',
                        $ip,
                        $order));
            }

        }
        catch (Exception $ex) {

            $this->getErrorLog()->logException($ex);
        }

        // Indicate success to WordPress so it continues processing other unrelated hooks.
        return true;
    }

    /**
     * @param $fieldName string
     * @param $patternsArray array
     * @return boolean true succeeded
     */
    public function slackNotification($webhook, $webhookConf, $slack_message, $title='') {


        $payload = array(
            'text' => 'from wp',
            'username' => 'contactic-bot',
            'icon_url' => '',
            'channel' => '#général',
        );

        if (isset($webhookConf['SlackBotName'])) $payload['username'] = $webhookConf['SlackBotName'];
        if (isset($webhookConf['SlackMessage'])) $payload['text'] = $webhookConf['SlackMessage'];
        if (isset($webhookConf['SlackChannel'])) $payload['channel'] = $webhookConf['SlackChannel'];
        if (isset($webhookConf['SlackIconUrl'])) $payload['icon_url'] = $webhookConf['SlackIconUrl'];

        $payload['text'] .= ' "'.$title.'" '.chr(10).chr(10).$slack_message;

        $post_data = json_encode($payload);


        $result    = file_get_contents($webhook, false, stream_context_create(array(
            'http' => array(
                'header'           => array('Content-Type: application/json'),
                'method'           => 'POST',
                'content'          => $post_data,
            ),
        )));


    }

    /**
     * @param $fieldName string
     * @param $patternsArray array
     * @return boolean true if $fieldName is in $patternsArray or matches any element of it that is a regex
     */
    public function fieldMatches($fieldName, $patternsArray) {
        if (is_array($patternsArray)) {
            foreach($patternsArray as $pattern) {
                if ($fieldName == $pattern) {
                    return true;
                }
                if (strncmp($pattern, '/', 1)  == 0) {
                    if (@preg_match($pattern , $fieldName)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param  $time string form submit time
     * @param  $formName string form name
     * @param  $fieldName string field name (should be an upload file field)
     * @return array of (file-name, file-contents) or null if not found
     */
    public function getFileFromDB($time, $formName, $fieldName) {
        global $wpdb;
        $tableName = $this->getSubmitsTableName();
        $sql = "SELECT `field_value`, `file` FROM `$tableName` WHERE `submit_time` = %F AND `form_name` = %s AND `field_name` = '%s'";
        $sql = $wpdb->prepare($sql, $time, $formName, $fieldName);
        $rows = $wpdb->get_results($sql);
        if ($rows == null || count($rows) == 0) {
            return null;
        }

        return array($rows[0]->field_value, $rows[0]->file);
    }

    /**
     * Install page for this plugin in WP Admin
     * @return void
     */
    public function createAdminMenu() {
        global $_wp_last_object_menu;

        $_wp_last_object_menu++;

        $displayName = $this->getPluginDisplayName();

        $hideFromNonAdmins = $this->getOption('HideAdminPanelFromNonAdmins', 'true', true) != 'false';
        $roleAllowed = 'Administrator';
        if (!$hideFromNonAdmins) {
            $roleAllowed = $this->getRoleOption('CanSeeSubmitData');
            if (!$roleAllowed) {
                $roleAllowed = 'Administrator';
            }
        }

        if (! $this->isUserRoleEqualOrBetterThan($roleAllowed)) {
            return;
        }

        $menuSlug = $this->getSlug();

        //create new top-level menu
        add_menu_page($displayName,                                                     // page_name
            __('Contactic', 'contactic') ,                   // menu_title
            $this->roleToCapability($roleAllowed),                                      // role
            $menuSlug,                                                                  // menu_slug
            array(&$this, 'showOverviewPage'),
            $this->getPluginFileUrl('assets/img/icon-bw-20x20.png'), 
            $_wp_last_object_menu);                                                     // menu position

        add_submenu_page($menuSlug,
            $displayName . ' Overview',
            __('Overview', 'contactic'),
            $this->roleToCapability($roleAllowed),
            $menuSlug,
            array(&$this, 'showOverviewPage')
        );

        add_submenu_page($menuSlug,
            $displayName . ' Contacts',
            __('Contacts', 'contactic'),
            $this->roleToCapability($roleAllowed),
            $this->getSlug('submissions'),
            array(&$this, 'whatsInTheDBPage')
        );


        // Needed for dialog in OverviewPages
        if (strpos($_SERVER['REQUEST_URI'], $menuSlug) !== false) {

            wp_enqueue_script('contactic_js_fontawesome', '//use.fontawesome.com/releases/v5.0.13/js/fontawesome.js', array(), null, $in_footer = false);
            wp_enqueue_script('contactic_js_charts', '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js', array(), null, $in_footer = false);
            wp_enqueue_script('contactic_js_popper', '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array(), null, $in_footer = true);
            wp_enqueue_script('contactic_js_bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js', array(), null, $in_footer = true);
            wp_enqueue_script('contactic_js_fontawesome_r', '//use.fontawesome.com/releases/v5.0.13/js/regular.js', array(), null, $in_footer = true);
            add_action('admin_print_styles', array(&$this, 'clean_styles'), 19);
            add_action('wp_print_scripts', array(&$this, 'clean_js'));
            add_filter('admin_footer_text', array(&$this, 'ctc_admin_footer'));
        }

        // Needed for dialog in ContactsPage
        if (strpos($_SERVER['REQUEST_URI'], $this->getSlug('sumbissions')) !== false) {
            $pluginUrl = $this->getPluginFileUrl() . '/';

            wp_enqueue_script('jquery');
//            wp_enqueue_script('jquery-ui-core');
//            wp_enqueue_script('jquery-ui-dialog', array('jquery-ui-core'));
            wp_enqueue_style('jquery-ui.css', $pluginUrl . 'assets/plugins/jquery-ui/jquery-ui-1.11.4.custom.min.css');
            wp_enqueue_script('contactic_js_fontawesome_r', '//use.fontawesome.com/releases/v5.0.13/js/regular.js', array(), null, $in_footer = true);
            wp_enqueue_script('contactic_js_popper', '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array(), null, $in_footer = true);
            wp_enqueue_script('contactic_js_bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js', array(), null, $in_footer = true);
            add_action('admin_print_styles', array(&$this, 'clean_styles'), 19);
            add_action('wp_print_scripts', array(&$this, 'clean_js'));
            add_filter('admin_footer_text', array(&$this, 'ctc_admin_footer'));
        }

        // Needed for dialog in ShortCodePage
        if (strpos($_SERVER['REQUEST_URI'], $this->getSlug('shortcodes')) !== false) {
            $pluginUrl = $this->getPluginFileUrl() . '/';
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core', array('jquery'));
            wp_enqueue_script('jquery-ui-dialog', array('jquery-ui-core'));
            wp_enqueue_script('jquery-ui-tabs', array('jquery-ui-core'));
            wp_enqueue_script('CF7DBdes', $pluginUrl . 'assets/js/des.js');
            wp_enqueue_script('CF7DBdes', $pluginUrl . 'assets/js/des.js');
            wp_enqueue_style('jquery-ui', plugins_url('/assets/css/jquery-ui.css', __FILE__));
            add_filter('admin_footer_text', array(&$this, 'ctc_admin_footer'));
        }

        // ShortCodes
        add_submenu_page($menuSlug,
                         $displayName . ' Shortcode Builder',
                         __('Shortcodes', 'contactic'),
                         $this->roleToCapability('Administrator'),
                         $this->getSlug('shortcodes'),
                         array(&$this, 'showShortCodeBuilderPage'));

        if ($this->isEditorActive() && $this->canUserDoRoleOption('CanSeeSubmitData')) {
            add_submenu_page($menuSlug,
                    $displayName . ' Import',
                __('Import', 'contactic'),
                $this->roleToCapability($this->getRoleOption('CanChangeSubmitData')),
                    get_class($this) . 'Import',
                array(&$this, 'showShortImportCsvPage'));
        }


        // Needed for the Settings Page
        if (strpos($_SERVER['REQUEST_URI'], $this->getSlug('options')) !== false) {
            require_once('view/CTC_ViewOptions.php');
            $optionsView = new CTC_ViewOptions($this);
            add_action('admin_enqueue_scripts', array(&$optionsView, 'enqueueSettingsPageScripts'));
            add_filter('admin_footer_text', array(&$this, 'ctc_admin_footer'));
        }

        add_submenu_page($menuSlug,
            $displayName . ' Options',
            __('Options', 'contactic'),
            'manage_options',
            $this->getSlug('options'),
            array(&$this, 'settingsPage'));

        add_submenu_page($menuSlug,
            $displayName .  'Webhooks',
            __('Webhooks', 'contactic'),
            'manage_options',
            $this->getSlug('webhooks'),
            array(&$this, 'webhooksPage'));

    }

    function ctc_admin_footer(){
        echo 'Please rate <strong>Contactic</strong> <a href="https://wordpress.org/support/plugin/contactic/reviews/?filter=5#new-post" target="_blank" rel="noopener noreferrer">★★★★★</a> on <a href="https://wordpress.org/support/plugin/contactic/reviews/?filter=5#new-post" target="_blank">WordPress.org</a> to help us spread the word. Thank you from the Contactic team!';
    }

    /**
     * @param string $page the page identifier, can be submissions, overview, shortcodes or webhooks
     *
     * @return string WP Admin slug for page to view DB data
     */
    public function getSlug($page = '') {

        $prefix = 'ContacticPlugin';

        switch ($page) {
            case 'submissions':
                return $prefix.'Submissions';
            case 'overview':
                return $prefix.'Overview';
            case 'shortcodes':
                return $prefix.'ShortCodeBuilder';
            case 'options':
                return $prefix.'Options';
            case 'webhooks':
                return $prefix.'Webhooks';
            default:
                return $prefix;
        }
    }

    public function showShortCodeBuilderPage() {
        require_once('view/CTC_ViewShortCodeBuilder.php');
        $view = new CTC_ViewShortCodeBuilder;
        $view->display($this);
    }

    public function showShortImportCsvPage() {
        require_once('view/CTC_ViewImportCsv.php');
        $view = new CTCViewImportCsv;
        $view->display($this);
    }


    public function showOverviewPage() {
        require_once('view/CTC_ViewOverview.php');
        $view = new CTC_ViewOverview();
        $view->display($this);
    }


    /**
     * Display the Admin page for this Plugin that show the form data saved in the database
     * @return void
     */
    public function whatsInTheDBPage() {
        if (isset($_REQUEST['submit_time'])) {
            $submitTime = sanitize_text_field($_REQUEST['submit_time']);
            require_once('CTC_ExportEntry.php');
            $exp = new CTC_ExportEntry();
            if (isset($_REQUEST['form_name']) && !empty($_REQUEST['form_name'])) {
                $form = stripslashes(sanitize_text_field($_REQUEST['form_name']));
                $form = strip_tags($form); // guard against xss
            } else {
                global $wpdb;
                $table = $this->getSubmitsTableName();
                $form = $wpdb->get_var($wpdb->prepare("SELECT form_name from $table where submit_time = %s LIMIT 1",
                        $submitTime));
            }

            ?>
            <div class="wrap">
            <form action="<?php echo get_admin_url() . 'admin.php?page=' . $this->getSlug('submissions') . "&form_name=" . urlencode($form) ?>"
                  method="post">
                <input name="form_name" type="hidden" value="<?php echo esc_attr($form) ?>"/>
                <input name="<?php echo esc_attr($submitTime) ?>" type="hidden" value="row"/>
                <?php wp_nonce_field(); ?>
                <button id="delete" name="cfdbdel" class="button"
                        onclick="this.form.submit();"><?php echo esc_html(__('Delete', 'contactic')); ?></button>
            </form>
            <?php
            $exp->export($form, array('submit_time' => $submitTime, 'filelinks' => 'link'));
        } else {
            require_once('view/CTC_ViewWhatsInDB.php');
            $view = new CTC_ViewWhatsInDB;
            $view->display($this);
        }
        ?>
        </div>
        <?php
    }

    static $checkForCustomDateFormat = true;
    static $customDateFormat = null;
    static $dateFormat = null;
    static $timeFormat = null;

    /**
     * Format input date string
     * @param  $time int same as returned from PHP time()
     * @return string formatted date according to saved options
     */
    public function formatDate($time) {
        // This method gets executed in a loop. Cache some variable to avoid
        // repeated get_option calls to the database
        if (ContacticPlugin::$checkForCustomDateFormat) {
            if ($this->getOption('UseCustomDateTimeFormat', 'true') == 'true') {
                ContacticPlugin::$customDateFormat = $this->getOption('SubmitDateTimeFormat', 'Y-m-d H:i:s P', true);
            }
            else {
               ContacticPlugin::$dateFormat = get_option('date_format');
               ContacticPlugin::$timeFormat = get_option('time_format');
            }
            $this->setTimezone();
            ContacticPlugin::$checkForCustomDateFormat = false;
        }

        // Support Shamsi(Jalali) dates by looking for a plugin that can produce the correct text for the date
        if (!function_exists('is_plugin_active') && @file_exists(ABSPATH . 'wp-admin/includes/plugin.php')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        if (function_exists('is_plugin_active')) {
            // See if wp-parsidate is active and if so, have it convert the date
            // using its 'parsidate' function
            if (is_plugin_active('wp-parsidate/wp-parsidate.php')) {
                if (function_exists('parsidate')) {
                    if (ContacticPlugin::$customDateFormat) {
                        return parsidate(ContacticPlugin::$customDateFormat, $time);
                    }
                    else {
                        return parsidate(ContacticPlugin::$dateFormat . ' ' . ContacticPlugin::$timeFormat, $time);
                    }
                }
            }
            // See if wp-jalali is active and if so, have it convert the date
            // using its 'jdate' function
            else if (is_plugin_active('wp-jalali/wp-jalali.php') && function_exists('jdate')) {
                if (ContacticPlugin::$customDateFormat) {
                    return jdate(ContacticPlugin::$customDateFormat, $time);
                } else {
                    return jdate(ContacticPlugin::$dateFormat . ' ' . ContacticPlugin::$timeFormat, $time);
                }
            }
        }

        if (ContacticPlugin::$customDateFormat) {
            return date(ContacticPlugin::$customDateFormat, $time);
        }
        else {
            return date_i18n(ContacticPlugin::$dateFormat . ' ' . ContacticPlugin::$timeFormat, $time);
        }
    }

    /**
     * @param  $submitTime string PK for form submission
     * @param  $formName string
     * @param  $fieldName string
     * @return string URL to download file
     */
    public function getFileUrl($submitTime, $formName, $fieldName) {
        return sprintf('%saction=cfdb-file&s=%s&form=%s&field=%s',
                $this->getAdminUrlPrefix('admin-ajax.php'),
                esc_attr($submitTime),
                urlencode($formName),
                urlencode($fieldName));
    }

    /**
     * Returns admin_url with a trailing "?" or "&" ready for parameters to be appended to it.
     * It check the output of admin_url() for a "?"
     * The reason for this method is to deal with installations that have WPML which injects
     * a "?lang=ca" after the admin/ajax urls
     *
     * @param $path string
     * @return string
     */
    public function getAdminUrlPrefix($path) {
        $url = admin_url($path);
        if (strpos($url, '?') === false) {
            return $url . '?';
        } else {
            return $url . '&';
        }
    }

    public function getFormFieldsAjaxUrlBase() {
        return $this->getAdminUrlPrefix('admin-ajax.php') . 'action=cfdb-getFormFields&form=';
    }

    public function getValidateSubmitTimeAjaxUrlBase() {
        return $this->getAdminUrlPrefix('admin-ajax.php') . 'action=cfdb-validate-submit_time&submit_time=';
    }

    /**
     * @return array of string
     */
    public function getNoSaveFields() {
        return $this->parseOption($this->getOption('NoSaveFields'));
    }

    /**
     * @return array of string
     */
    public function getNoSaveForms() {
        return $this->parseOption($this->getOption('NoSaveForms'));
    }

    /**
     * Parse option string that is a comma-delimited set of stings (some of which may be regex's with commas in them)
     * @param $option string
     * @return array
     */
    public function parseOption($option) {
//        return preg_split('/,|;/', $option, -1, PREG_SPLIT_NO_EMPTY);
        $values = array();
        if ($option) {
            $regex = false;
            $esc = false;
            $value = '';
            $len = strlen($option);
            for ($i = 0; $i < $len; $i++) {

                if ($regex && !$esc && $option[$i] == '\\') {
                    $esc = true;
                    $value .= $option[$i];
                    continue;
                }

                if (!$value && $option[$i] == '/') {
                    $regex = true;
                    $value .= $option[$i];
                    continue;
                }

                if (!$regex) {
                    if ($option[$i] == ',') {
                        if ($value) {
                            $values[] = $value;
                        }
                        $value = '';
                    } else {
                        $value .= $option[$i];
                    }
                } else {
                    if ($option[$i] == '/' && !$esc) {
                        $regex = false;
                    }
                    $value .= $option[$i];
                }
                $esc = false;
            }
            if ($value) {
                $values[] = $value;
            }
        }
        return $values;
    }

    /**
     * @return array of string
     */
    public function getSaveCookies() {
        return $this->parseOption($this->getOption('SaveCookieNames'));
    }

    /**
     * @return string
     */
    public function getSubmitsTableName_raw() {
        global $wpdb;
        return $wpdb->prefix . strtolower($this->prefix('SUBMITS'));
    }

    public function getSTTableName_raw() {
        global $wpdb;
        return $wpdb->prefix . strtolower($this->prefix('ST'));
    }

    public function getNoticesTableName_raw() {
        global $wpdb;
        return $wpdb->prefix . strtolower($this->prefix('NOTICES'));
    }

    /**
     * @return string
     */
    public function getSubmitsTableName() {
        $tableName = $this->getSubmitsTableName_raw();
        if (! $this->isTableDefined($tableName)) {
            // This should correct for missing tables and dynamically add them
            // in multisite configurations
            $this->installDatabaseTables();
        }
        return $tableName;
    }

    public function getSTTableName() {
        $tableName = $this->getSTTableName_raw();
        if (! $this->isTableDefined($tableName)) {
            // This should correct for missing tables and dynamically add them
            // in multisite configurations
            $this->installDatabaseTables();
        }
        return $tableName;
    }

    public function getNoticesTableName() {
        $tableName = $this->getNoticesTableName_raw();
        if (! $this->isTableDefined($tableName)) {
            // This should correct for missing tables and dynamically add them
            // in multisite configurations
            $this->installDatabaseTables();
        }
        return $tableName;
    }

    var $cacheIsTableDefined = false;
    public function isTableDefined($tableName) {
        if ($this->cacheIsTableDefined) {
            return true;
        }
        global $wpdb;
        $rows = $wpdb->get_results("SHOW TABLES LIKE '$tableName'");
        $this->cacheIsTableDefined = !empty($rows);
        return $this->cacheIsTableDefined;
    }

    /**
     * @return string URL to the Plugin directory. Includes ending "/"
     */
    public function getPluginDirUrl() {
        //return WP_PLUGIN_URL . '/' . str_replace(basename(__FILE__), '', plugin_basename(__FILE__));
        return $this->getPluginFileUrl('/');
    }


    /**
     * @param string $pathRelativeToThisPluginRoot points to a file with relative path from
     * this plugin's root dir. I.e. file "des.js" in the root of this plugin has
     * url = $this->getPluginFileUrl('des.js');
     * If it was in a sub-folder "js" then you would use
     *    $this->getPluginFileUrl('js/des.js');
     * @return string full url to input file
     */
    public function getPluginFileUrl($pathRelativeToThisPluginRoot = '') {
        return plugins_url($pathRelativeToThisPluginRoot, __FILE__);
    }


    /**
     * @return string URL of the language translation file for DataTables oLanguage.sUrl parameter
     * or null if it does not exist.
     */
    public function getDataTableTranslationUrl() {
        $url = null;
        $locale = get_locale();
        $i18nDir = dirname(__FILE__) . '/dt_i18n/';

        // See if there is a local file
        if (is_readable($i18nDir . $locale . '.json')) {
            $url = $this->getPluginFileUrl() . "/dt_i18n/$locale.json";
        }
        else {
            // Pull the language code from the $local string
            // which is expected to look like "en_US"
            // where the first 2 or 3 letters are for lang followed by '_'
            $lang = null;
            if (substr($locale, 2, 1) == '_') {
                // 2-letter language codes
                $lang = substr($locale, 0, 2);
            }
            else if (substr($locale, 3, 1) == '_') {
                // 3-letter language codes
                $lang = substr($locale, 0, 3);
            }
            if ($lang && is_readable($i18nDir . $lang . '.json')) {
                $url = $this->getPluginFileUrl() . "/dt_i18n/$lang.json";
            }
        }
        return $url;
    }

    public function setTimezone() {
        $timezone = trim($this->getOption('Timezone'));
        if (empty($timezone)) {
            $timezone = get_option('timezone_string');
        }
        if (!empty($timezone)) {
            date_default_timezone_set($timezone);
        }
    }

    /**
     * @return boolean Is the CFDB Editor extension installed?
     */
    public function isEditorInstalled() {
        return get_option('CFDBEditPlugin__installed', false) == true;
    }


    /**
     * @return string|null get the CFDB Editor extension version string.
     * return null if not installed
     */
    public function getEditorSavedVersion() {
        return get_option('CFDBEditPlugin__version', null);
    }

    /**
     * @return array of CFDB Editor plugin data, see: http://codex.wordpress.org/Function_Reference/get_plugin_data
     */
    public function getEditorPluginData() {
            $editPluginFile = WP_PLUGIN_DIR .
                    '/contact-form-to-database-extension-edit/contact-form-to-database-extension-edit.php';
            if(@file_exists($editPluginFile)) {
                $pluginData = get_plugin_data($editPluginFile);
                if (is_array($pluginData)) {
                    return $pluginData;
            }
        }
        return array();
    }

    /**
     * @return bool if CFDB Editor extension plugin is activated
     */
    public function isEditorActive() {
        $editPluginFile = 'contact-form-to-database-extension-edit/contact-form-to-database-extension-edit.php';
        return function_exists('is_plugin_active') && is_plugin_active($editPluginFile);
    }


    public function addAdminNotices() {
        $screen = get_current_screen();
        $display_notices_pages = ['dashboard', 'toplevel_page_contactic_menu', 'plugins', 'contactic_page_ContacticPluginSubmissions'];
        if (in_array($screen->id, $display_notices_pages)){
            global $wpdb;
            $tableName = $this->getNoticesTableName();
            
            wp_enqueue_script('ctc-notice-update', plugins_url( 'assets/js/notice-dismiss.js', __FILE__ ), array( 'jquery' ), '1.0', true);

            // get notice if exists
            $notices = $wpdb->get_results("select * from `$tableName` where dismissed=0 order by `id` desc limit 1");
            foreach ($notices as $notice) {
                ?>
                    <div class="notice notice-<?php echo $notice->type ?> contactic-notice is-dismissible" data-id="<?php echo $notice->id ?>">
                        <p>
                            <?php echo $notice->message ?>
                        </p>
                    </div>
                <?php
            }

            // action notice 
            if (isset($_SESSION['action_notice']) && is_array($_SESSION['action_notice'])){
                ?>
                    <div class="notice notice-<?php echo $_SESSION['action_notice']['type'] ?> is-dismissible">
                        <p>
                            <?php echo $_SESSION['action_notice']['msg'] ?>
                        </p>
                    </div>
                <?php
                unset($_SESSION['action_notice']);
            }
        }
    }

    // ajax saving notice dismiss state
    public function ctc_dismiss_notice() {
        global $wpdb;
        $tableName = $this->getNoticesTableName();
        $wpdb->query("UPDATE `$tableName` SET dismissed=1 WHERE `id`=".intval($_GET['id']));
    }

    /**
     * @return array of form names that have data in the DB
     */
    public function getForms() {
        global $wpdb;
        $forms = array();
        $tableName = $this->getSubmitsTableName();
        $formsFromQuery = $wpdb->get_results("select distinct `form_name` from `$tableName` order by `form_name`");
        foreach ($formsFromQuery as $aRow) {
            $forms[] = $aRow->form_name;
        }
        return $forms;
    }

    /**
     * return CTC_ErrorLog
     */
    public function getErrorLog() {
        $destination = trim($this->getOption('ErrorOutput', '', true));
        return new CTC_ErrorLog($this, $destination);
    }

    // http://stackoverflow.com/questions/1634782/what-is-the-most-accurate-way-to-retrieve-a-users-correct-ip-address-in-php
    public function getIPAddress() {
        $maybeIp = '';
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    } else if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        $maybeIp = $ip;
                    }
                }
            }
        }
        return $maybeIp;
    }

    // save the page uri and title in session
    public function ctc_get_the_page() {
        $pluginUrl = plugins_url('/', __FILE__);
        if (get_option('contactic_TrackingEnabled', true) !== 'false'){
            wp_enqueue_script('get-the-page', $pluginUrl.'assets/js/get-the-page.js', array('jquery'), '1.0', true);
            wp_localize_script('get-the-page', 'ajaxurl', admin_url('admin-ajax.php'));
        }
    }

    // save the page uri and title + referer in session (ajax mode because of page chaching)
    public function ctc_get_the_page_ajax() {
        // referer
        if ( !isset( $_SESSION["original_referer"] ) ){
            if (isset($_POST["ctc_referer"])){
                $referer = parse_url(sanitize_text_field($_POST["ctc_referer"]));
                $_SESSION["original_referer"] = $referer['host'];
            }
        }

        // page uri & title
        if (isset($_POST['ctc_title'])){
            $sep = esc_html(convert_chars(wptexturize(apply_filters( 'document_title_separator', '-'))));
            if(in_array('wordpress-seo/wp-seo.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
                if ($wpseo_titles = get_option('wpseo_titles')){
                    if (isset($wpseo_titles['separator']) && !empty($wpseo_titles['separator'])){
                        switch ($wpseo_titles['separator']) {
                            case 'sc-dash':
                                $sep = '-';
                                break;
                            case 'sc-ndash':
                                $sep = '&ndash;';
                                break;
                            case 'sc-mdash':
                                $sep = '&mdash;';
                                break;
                            case 'sc-colon':
                                $sep = ':';
                                break;
                            case 'sc-middot':
                                $sep = '&middot;';
                                break;
                            case 'sc-bull':
                                $sep = '&bull;';
                                break;
                            case 'sc-star':
                                $sep = '*';
                                break;
                            case 'sc-smstar':
                                $sep = '&#8902;';
                                break;
                            case 'sc-pipe':
                                $sep = '|';
                                break;
                            case 'sc-tilde':
                                $sep = '~';
                                break;
                            case 'sc-laquo':
                                $sep = '&laquo;';
                                break;
                            case 'sc-raquo':
                                $sep = '&raquo;';
                                break;
                            case 'sc-lt':
                                $sep = '&lt;';
                                break;
                            case 'sc-gt':
                                $sep = '&gt;';
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
            $sep = html_entity_decode($sep);
            $title = explode(" $sep ", html_entity_decode(sanitize_text_field($_POST['ctc_title'])));
            $_SESSION['_ctc_last_page_title'] = trim($title[0]);
        }
        if (isset($_POST['ctc_uri'])){
            $_SESSION['_ctc_last_page_uri'] = sanitize_text_field($_POST['ctc_uri']);
        }
        
        die();
    }

    // Let's check notice on our server & save it if exists
    public function ctc_check_notices(){
        $response = wp_remote_get('https://contactic.io/wp-plugin/admin-notice.json');
        if ( ! is_wp_error( $response ) ) { //decode json
            global $wpdb;
            $tableName = $this->getNoticesTableName();
            if ($notice = json_decode(wp_remote_retrieve_body($response))){
                if (isset($notice->adminnotice) && is_object($notice->adminnotice)){
                    $notice = $notice->adminnotice;
                    if (($notice->id > 0 ) && !empty($notice->type) && !empty($notice->message)) {
                        $notices = $wpdb->get_results("select `id` from `$tableName` where `id`=".$notice->id);
                        if (empty($notices)){
                            $wpdb->query("INSERT INTO `$tableName` (`id`, `type`, `message`) VALUES (".$notice->id.", \"".$notice->type."\", \"".$notice->message."\")");
                        }
                    }
                }
            }
        }
    }

    // monthly contacts count
    public function month_count(){
        global $wpdb;
        $tableName = $this->getSTTableName_raw();
        return $wpdb->get_results("select COUNT(`submit_time`) as contacts, FROM_UNIXTIME(`submit_time`, '%Y-%m') as `month` from `$tableName` GROUP BY `month` ORDER BY `month` DESC LIMIT 6");
    }

    // count contact's usefulness flags distribution
    public function usefulness_distribution(){
        global $wpdb;
        $tableName = $this->getSubmitsTableName();
        return $wpdb->get_results("select COUNT(`submit_time`) as contacts, `field_value` as usefulness 
                                    from `$tableName` 
                                    WHERE field_name='_ctc_usefulness'
                                    GROUP BY `usefulness`");
    }

    // count contact's status flags distribution
    public function status_distribution(){
        global $wpdb;
        $tableName = $this->getSubmitsTableName();
        return $wpdb->get_results("select COUNT(`submit_time`) as contacts, `field_value` as `status` 
                                    from `$tableName` 
                                    WHERE field_name='_ctc_status'
                                    GROUP BY `status`");
    }

    // get submit details to display on modal 
    public function submitDetails(){
        if (isset($_REQUEST['submit_time'])) {
            // security check
            if (!$this->canUserDoRoleOption('CanSeeSubmitData')){
                die('error');
            }

            $submitTime = sanitize_text_field($_REQUEST['submit_time']);

            if (!((strlen(substr(strrchr($submitTime, "."), 1)) == 4)
                    && (strlen(substr($submitTime, 0, strpos($submitTime, "."))) == 10))) {
                echo strlen(substr($submitTime, 0, strpos($submitTime, ".")));
                die('error');
            }

            require_once('CTC_ExportEntry.php');
            $exp = new CTC_ExportEntry();
            
            global $wpdb;
            $table = $this->getSubmitsTableName();
            $form = $wpdb->get_var($wpdb->prepare("SELECT form_name from $table where submit_time = %s LIMIT 1", $submitTime));

            $statuses = $wpdb->get_results($wpdb->prepare("SELECT form_name, field_name, field_value 
                                                        from $table 
                                                        where submit_time = %s AND (field_name='_ctc_usefulness' OR field_name='_ctc_status')", $submitTime));
           
            $exp->export($form, array('submit_time' => $submitTime, 'filelinks' => 'link'));

            $contact_usefulness = 'Undefined';
            $contact_status = 'Todo';

            foreach ($statuses as $status) {
                if ($status->field_name == '_ctc_status'){
                    $contact_status = $status->field_value;
                }
                if ($status->field_name == '_ctc_usefulness'){
                    $contact_usefulness = $status->field_value;
                }
            }

            $status_class = ($contact_status == 'Done') ? 'success' : 'danger';
            switch ($contact_usefulness) {
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
            $i = intval($_REQUEST['i']);
            ?>
            <div class="modal-interaction text-right">
                <p class="title"><h6><?php _e('Is it usefull ?', 'contactic'); ?></h6>
                <div class="dropdown">
                    <button type="button" data-toggle="dropdown" data-id="<?php echo $submitTime; ?>" class="btn waves-effect waves-light btn-<?php echo $usefulness_class ?> dropdown-toggle" id="modalusefulness_<?php echo $i; ?>"><?php echo $contact_usefulness; ?></button>
                    <div class="dropdown-menu" aria-labelledby="modalusefulness_<?php echo $i; ?>" data-id="<?php echo $submitTime; ?>" data-i="<?php echo $i; ?>">
                        <a class="dropdown-item changeusefulness" href="javascript:;">Usefull</a>
                        <a class="dropdown-item changeusefulness" href="javascript:;">Useless</a>
                        <a class="dropdown-item changeusefulness" href="javascript:;">Spam</a>
                    </div>
                </div>
            </div>
            <div class="modal-interaction text-right">
                <p class="title"><h6><?php _e('Did you handle the message ?', 'contactic'); ?></h6></p>
                <div class="dropdown">
                    <button type="button" data-id="<?php echo $submitTime; ?>" class="btn waves-effect waves-light btn-<?php echo $status_class ?> dropdown-toggle changestatus" id="modalstatus_<?php echo $i; ?>" data-i="<?php echo $i ?>"><?php echo $contact_status; ?></button>
                </div>
            </div>
            <div class="modal-interaction">
                <input type="hidden" id="form_name" value="<?php echo htmlentities(stripslashes($form)); ?>">
                <button id="delete" name="cfdbdel" type="button" data-id="<?php echo $submitTime; ?>"
                        class="btn btn-lg waves-effect waves-light btn-danger text-center"
                        ><?php echo esc_html(__('Delete', 'contactic')); ?></button>
            </div>
            <?php
        }
        ?>
        </div>
        <?php
        exit();
    }

    // get submit details to display on modal 
    public function submitDelete(){
        if (isset($_REQUEST['submit_time'])) {
            // security check
            if (!$this->canUserDoRoleOption('CanChangeSubmitData')){
                die('error');
            }
            $submitTime = sanitize_text_field($_REQUEST['submit_time']);
            if (!((strlen(substr(strrchr($submitTime, "."), 1)) == 4)
                    && (strlen(substr($submitTime, 0, strpos($submitTime, "."))) == 10))) {
                echo strlen(substr($submitTime, 0, strpos($submitTime, ".")));
                die('error');
            }
            global $wpdb;
            $tableName = $this->getSubmitsTableName();
            $wpdb->query($wpdb->prepare("DELETE from $tableName where submit_time = %s", $submitTime));
            $tableName = $this->getSTTableName_raw();
            $wpdb->query($wpdb->prepare("DELETE from $tableName where submit_time = %s", $submitTime));
            $_SESSION['action_notice'] = array('type' => 'success', 'msg' => 'Contact successfully deleted.');
        }
    }

    // ajax update submit usefulness
    public function submitUsefulnessChange(){
        if (isset($_REQUEST['submit_time']) && isset($_REQUEST['usefulness'])) {
            // security check
            if (!$this->canUserDoRoleOption('CanChangeSubmitData')){
                die('error');
            }
            $submitTime = sanitize_text_field($_REQUEST['submit_time']);
            if (!((strlen(substr(strrchr($submitTime, "."), 1)) == 4)
                    && (strlen(substr($submitTime, 0, strpos($submitTime, "."))) == 10))) {
                echo strlen(substr($submitTime, 0, strpos($submitTime, ".")));
                die('error');
            }
            global $wpdb;
            $table = $this->getSubmitsTableName();
            $form = $wpdb->get_var($wpdb->prepare("SELECT form_name from $table where submit_time = %s LIMIT 1", $submitTime));
            $status = $wpdb->get_results($wpdb->prepare("SELECT form_name, field_name, field_value 
                                                        from $table 
                                                        where submit_time = %s AND field_name='_ctc_usefulness'", $submitTime));
            $usefulness = sanitize_text_field($_REQUEST['usefulness']);
            if (count($status) > 0){
                $wpdb->update($table, array('field_value' => $usefulness), 
                                        array('submit_time' => $submitTime, 'field_name' => '_ctc_usefulness'));
            }else{
                $wpdb->insert($table, array('submit_time' => $submitTime, 'form_name' => $form, 
                                            'field_name' => '_ctc_usefulness', 'field_value' => $usefulness, 'field_order' => 1001));
            }
            die('success');
        }
    }

    // ajax update submit status
    public function submitStatusChange(){
        if (isset($_REQUEST['submit_time']) && isset($_REQUEST['status'])) {
            // security check
            if (!$this->canUserDoRoleOption('CanChangeSubmitData')){
                die('error');
            }
            $submitTime = sanitize_text_field($_REQUEST['submit_time']);
            if (!((strlen(substr(strrchr($submitTime, "."), 1)) == 4)
                    && (strlen(substr($submitTime, 0, strpos($submitTime, "."))) == 10))) {
                echo strlen(substr($submitTime, 0, strpos($submitTime, ".")));
                die('error');
            }
            global $wpdb;
            $table = $this->getSubmitsTableName();
            $form = $wpdb->get_var($wpdb->prepare("SELECT form_name from $table where submit_time = %s LIMIT 1", $submitTime));
            $submit_status = $wpdb->get_results($wpdb->prepare("SELECT form_name, field_name, field_value 
                                                        from $table 
                                                        where submit_time = %s AND field_name='_ctc_status'", $submitTime));
            $status = ($_REQUEST['status'] == 'Todo') ? 'Done' : 'Todo';
            if (count($submit_status) > 0){
                $wpdb->update($table, array('field_value' => $status), 
                                        array('submit_time' => $submitTime, 'field_name' => '_ctc_status'));
            }else{
                $wpdb->insert($table, array('submit_time' => $submitTime, 'form_name' => $form, 
                                            'field_name' => '_ctc_status', 'field_value' => $status, 'field_order' => 1002));
            }
            die('success');
        }
    }

    // ajax contacts load for datatables in serverside mode - contacts page
    public function loadContacts(){
        require_once('CTC_ExportToJson.php');
        $exporter = new CTC_ExportToJson();
        $currSelection = sanitize_text_field($_REQUEST['form_name']);
        $dbRowCount = $exporter->getDBRowCount(stripslashes($currSelection));

        $options['maxRows'] = intval($_REQUEST['length']);
        $options['start'] = intval($_REQUEST['start']);
        $options['orderby'] = sanitize_text_field($_REQUEST['columns'][$_REQUEST['order'][0]['column']]['data']).' '.sanitize_text_field($_REQUEST['order'][0]['dir']);
        $options['draw'] = intval($_REQUEST['draw']);
        $options['totalRows'] = $dbRowCount;
        $options['datatables_serverside'] = true;

        if (isset($_REQUEST['minDateFilter']) && !empty($_REQUEST['minDateFilter']) && isset($_REQUEST['maxDateFilter']) && !empty($_REQUEST['maxDateFilter'])){
            $options['filter'] = 'submit_time>='.sanitize_text_field($_REQUEST['minDateFilter'])
                                .'&&submit_time<='.sanitize_text_field($_REQUEST['maxDateFilter']);
        }elseif (isset($_REQUEST['minDateFilter']) && !empty($_REQUEST['minDateFilter'])){
            $options['filter'] = 'submit_time>='.sanitize_text_field($_REQUEST['minDateFilter']);
        }elseif (isset($_REQUEST['maxDateFilter']) && !empty($_REQUEST['maxDateFilter'])){
            $options['filter'] = 'submit_time<='.sanitize_text_field($_REQUEST['maxDateFilter']);
        }
        if (!empty($_REQUEST['search']['value'])){ 
            $options['search'] = sanitize_text_field($_REQUEST['search']['value']);
            $dbRowCountFiltered = $exporter->exportCount(stripslashes($currSelection), $options);
            $options['filteredTotalRows'] = $dbRowCountFiltered;
        }else{
            $options['filteredTotalRows'] = $dbRowCount;
        }
        
        $options['limit'] = $options['start'].','.$options['maxRows'];
        $exporter->export(stripslashes($currSelection), $options);
        exit();
    }

}
