<?php

/*
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

class CTC_ViewWebhooks extends CTC_View {

    public function loadStyles() {

	    // Enqueue and register some styles
        wp_register_style('contactic_css_bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
        wp_enqueue_style('contactic_css_bootstrap');
        wp_enqueue_style('contactic_css_styles', plugins_url('../assets/css/styles.css', __FILE__));
        wp_enqueue_style( 'load-fa', '//use.fontawesome.com/releases/v5.7.2/css/all.css');
        wp_register_style('contactic_fonts', '//fonts.googleapis.com/css?family=Poppins:300,400,500',null, null);
        wp_enqueue_style('contactic_fonts');
        wp_enqueue_style('load-fa');


	}

    /**
     * @param  $plugin ContacticPlugin
     * @return void
     */
    function display(&$plugin) {
        $this->pageHeader($plugin);
        $this->loadStyles();
        if ($this->outputHeader()) {
            $this->outputJavascript();
            $this->output($plugin);
        }
    }

    public function enqueueSettingsPageScripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script( 'contactic_js_bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js', array('jquery'), '20120206', true );
    }

    public function outputJavascript() { ?>

            <script type="text/javascript" language="JavaScript">
                jQuery(document).ready(function($) {
                    $('#new_slack').on('click', function(e) {
                        $('#new_slack_tab').removeClass('hidden');
                        $('#slack').removeClass('hidden');
                        $('#slack-tab').tab('show')
                        e.preventDefault();
                    });

                    $(".any_form").change(function() {
                        if(this.checked) {
                            $( ".other_form" ).prop( "checked", false );
                            $( ".other_form" ).attr( "disabled", true );
                        } else {
                            $( ".other_form" ).attr( "disabled", false );
                        }
                    });
                });
            </script>

        <?php
    }
    
    /**
     * @param $plugin ContacticPlugin
     */
    public function output($plugin) {

        $imageUrlDir = $plugin->getPluginDirUrl()."assets/img";

        $webhookConf = $plugin->getOption('WebhookConf', array(), true);
        if ($webhookConf  === false) $webhookConf = array();

        ?>

        <div id="ctc_options_tabs">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class='nav-link active' data-toggle="tab" role="tab" aria-selected="true" aria-controls="pipedrive" id="pipedrive-tab"  href="#pipedrive"><i class="fa fa-cog"></i> <?php _e('Configuration', 'contactic'); ?></a>
                </li>
                <?php foreach ($webhookConf as $confId => $conf ) { ?>
                <li class="nav-item">
                    <a class='nav-link' data-toggle="tab" role="tab" aria-selected="false" aria-controls="slack" id="<?php echo 'tab-'.$confId ?>" href="#<?php echo 'id-'.$confId ?>"><i class="fab fa-<?php echo $conf['webhook_type'] ?>"></i> <?php echo $conf['SlackBotName'] ?></a>
                </li>
                <?php } ?>
                <li id='new_slack_tab' class="nav-item hidden">
                    <a class='nav-link' data-toggle="tab" role="tab" aria-selected="false" aria-controls="slack" id="slack-tab" href="#slack"><i class="fab fa-slack"></i> <?php _e('Slack', 'contactic'); ?></a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" role="tabpanel" aria-labelledby="pipedrive-tab" id="pipedrive">

                    <form method="post" action="">
                        <?php
                        settings_fields(get_class($this).'-webhooks-group');
                        ?>
                        <p><?php _e('Contactic allows you to configure webhooks, making it possible to automatically push submitted forms to your prefered third-party online service.', 'contactic'); ?></p>

                        <!-- Slack -->
                        <div class="row mb-5">
                            <div class="col-3 offset-md-1">
                                <img class="img-fluid" height="50" src="<?php echo $imageUrlDir ?>/logo_slack.png" />
                            </div>
                            <div class="col-md-6 my-auto">
                                <p class="m-0">
                                    <?php _e('Add your <a href="https://www.slack.com" target="_blank">Slack</a> Webhook Url to get notified by a message in your preferred channel, when someone submits a form. Once your webhook url save you will be able to create slack bots.', 'contactic'); ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group row mb-5">
                            <label for="slack-webhook-url" class="col-md-2 offset-md-2 col-form-label"><?php _e('Slack Webhook Url', 'contactic'); ?></label>
                            <div class="col-md-5 ">
                                <input type="text" value="<?php echo $plugin->getOption('SlackWebhookUrl') ?>" name="SlackWebhookUrl" class="form-control" id="slack-webhook-url" placeholder="<?php _e('Your slack webhook Url', 'contactic'); ?>">
                                <?php if ($plugin->getOption('SlackWebhookUrl') != '') { ?>
                                    <a href="#" id="new_slack" class="btn btn-default"><i class="fa fa-plus"></i><?php _e('Create a new slack bot', 'contactic'); ?></a>
                                <?php } ?>
                            </div>
                        </div>


                        <!-- Pipedrive coming soon, Shhh it’s a secret
                        <div class="row mb-5">
                            <div class="col-3 offset-md-1">
                                <img class="img-fluid" height="50" src="<?php echo $imageUrlDir ?>/logo_pipedrive.png" />
                            </div>
                            <div class="col-md-6 my-auto">
                                <p class="m-0">
                                    <?php _e('Add your <a href="https://www.pipedrive.com" target="_blank">Pipedrive</a> API Key to start pushing your form submission as Contacts and Deals.
                                    Your API can be found in Pipedrive, under Settings > Profile > API. Once saved you will be able to configure a new webhook.', 'contactic'); ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group row mb-5">
                            <label for="pipedrive-api-key" class="col-md-2 offset-md-2 col-form-label"><?php _e('Pipedrive API Key', 'contactic'); ?></label>
                            <div class="col-md-5 ">
                                <input name="PipedriveApiKey" value="<?php echo $plugin->getOption('PipedriveApiKey') ?>"  type="text" class="form-control" id="pipedrive-api-key" placeholder="comming soon" disabled>
                            </div>
                        </div>
                        -->
                        <div class="form-group row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary"><?php _e('Save changes', 'contactic'); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade hidden" role="tabpanel" aria-labelledby="slack-tab" id="slack">
                    <?php $this->outputSlackConf($plugin, '', array()) ?>
                </div>
                <?php foreach ($webhookConf as $confId => $conf ) { ?>

                <div class="tab-pane fade" role="tabpanel" aria-labelledby="<?php echo 'tab-'.$confId ?>" id="<?php echo 'id-'.$confId ?>">
                   <?php $this->outputSlackConf($plugin, $confId, $conf) ?>
                </div>

                <?php } ?>
            </div>


        </div>

        <?php
        $this->outputFooter();
    }

    public function outputSlackConf($plugin, $confId, $conf) {

        $imageUrlDir = $plugin->getPluginDirUrl()."assets/img";

        $formsList = $plugin->getForms();

        ?>

        <!-- Slack Configuration template -->
        <div class="row mb-5">
            <div class="col-3 offset-md-1">
                <img class="img-fluid" src="<?php echo $imageUrlDir ?>/logo_slack.png" />
            </div>
            <div class="col-md-6 my-auto">
                <p class="m-0">
                    <?php _e('Configure your <a href="https://www.slack.com" target="_blank">Slack</a> Webhook and customize the message you want to receive in your channel.', 'contactic'); ?>
                </p>
            </div>
        </div>
        <form method="post" action="">
            <?php
            settings_fields(get_class($this).'-webhooks-group');
            ?>
            <div class="form-group row mb-5">
                <label for="slack-bot-name" class="col-md-2 offset-md-2 col-form-label"><?php _e('Slack Bot Name', 'contactic'); ?></label>
                <div class="col-md-5 ">
                    <input type="hidden" name="webhook_id" value="<?php echo $confId ?>">
                    <input type="hidden" name="webhook_type" value="<?php echo $value = isset($conf['webhook_type']) ?  $conf['webhook_type'] : 'slack'; ?>">
                    <input type="text" value="<?php echo $value = isset($conf['SlackBotName']) ? $conf['SlackBotName'] : 'Contactic-bot'; ?>" name="SlackBotName" class="form-control" id="slack-bot-name" placeholder="your slack bot name">
                </div>
            </div>
            <div class="form-group row mb-2">
                <label for="slack-icon-url" class="col-md-2 offset-md-2 col-form-label"><?php _e('Message', 'contactic'); ?></label>
                <div class="col-md-5">
                    <textarea class="form-control" id="SlackMessage" name="SlackMessage"  rows="3"><?php echo $value = isset($conf['SlackMessage']) ? $conf['SlackMessage'] : 'Someone submitted your form'; ?></textarea>
                </div>
            </div>

            <div class="form-group row mb-2">
                <label for="slack-icon-url" class="col-md-2 offset-md-2 col-form-label"><?php _e('Trigger message on form', 'contactic'); ?></label>
                <div class="col-md-5 pt-2">
                    <div class="form-check">
                        <input style="margin:2px 0 0 -20px" class="form-check-input any_form" name="formTrigger[]" type="checkbox" value="*" <?php if ($confId === '') echo 'checked '; if(isset($conf['formTrigger']) && in_array('*', $conf['formTrigger'])) echo 'checked'; ?>>
                        <label class="form-check-label">
                            <?php _e('Any form', 'contactic'); ?>
                        </label>
                    </div>
                    <?php foreach ($formsList as $formKey => $formValue) { ?>
                    <div class="form-check">
                        <input style="margin:2px 0 0 -20px" class="form-check-input other_form" type="checkbox" name="formTrigger[]" value="<?php echo $formValue; ?>" <?php if ($confId === '') echo 'disabled '; if(isset($conf['formTrigger']) && in_array($formValue, $conf['formTrigger'])) echo 'checked'; ?>>
                        <label class="form-check-label" for="defaultCheck1">
                            <?php echo $formValue; ?>
                        </label>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <div class="row  mb-2">
                <div class="col-md-2 offset-md-4">
                    <a href="#" data-toggle="collapse" data-target="#slack_more_opt" aria-expanded="true" aria-controls="collapseOne"><?php _e('More options', 'contactic'); ?></a>
                </div>
            </div>

            <div id="slack_more_opt" class="collapse hide">
                <div class="form-group row mb-5">
                    <label for="slack-channel" class="col-md-2 offset-md-2 col-form-label"><?php _e('Slack Channel', 'contactic'); ?></label>
                    <div class="col-md-5 ">
                        <input type="text" value="<?php echo $value = isset($conf['SlackChannel']) ? $conf['SlackChannel'] : ''; ?>" name="SlackChannel" class="form-control" id="slack-channel" placeholder="slack channel or user">
                        <small id="emailHelp" class="form-text text-muted"><?php _e('Overrides the default webhook channel. Can be #general or @johndoe', 'contactic'); ?></small>
                    </div>
                </div>
                <div class="form-group row mb-5">
                    <label for="slack-icon-url" class="col-md-2 offset-md-2 col-form-label">Icon Url</label>
                    <div class="col-md-5 ">
                        <input type="text" value="<?php echo $value = isset($conf['SlackIconUrl']) ? $conf['SlackIconUrl'] : 'https://contactic.io/img/favicon.png'; ?>" name="SlackIconUrl" class="form-control" id="slack-icon-url" placeholder="your icon url">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-success"><?php _e('Save', 'contactic'); ?></button>
                    <?php if ($confId !== '') { ?>
                    <a href="?page=ContacticPluginWebhooks&remove=<?php echo $confId; ?>" onclick="return confirm('<?php _e('Do you really want to delete webhook?', 'contactic'); ?>')" class="btn btn-danger"><i class="fa fa-trash"></i><?php _e('Delete this bot', 'contactic'); ?></a>
                    <?php } ?>
                </div>
            </div>
        </form>

        <?php


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
	    
	    <h1><?php _e('Webhooks', 'contactic'); ?></h1>

        <?php

            return true;

        }

    public function outputFooter() {
        ?>

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

}