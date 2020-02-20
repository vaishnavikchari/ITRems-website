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

function session_error_handling_function($code, $msg, $file, $line) {
    // no active session handler :(
    global $session_handler_error;
    $session_handler_error = true;
}

function CTC_Plugin_init($file) {

    require_once('ContacticPlugin.php');
    $aPlugin = new ContacticPlugin();

    // Install the plugin
    // NOTE: this file gets run each time you *activate* the plugin.
    // So in WP when you "install" the plugin, all that does it dump its files in the plugin-templates directory
    // but it does not call any of its code.
    // So here, the plugin tracks whether or not it has run its install operation, and we ensure it is run only once
    // on the first activation
    if (!$aPlugin->isInstalled()) {
        $aPlugin->install();
    }
    else {
        // Perform any version-upgrade activities prior to activation (e.g. database changes)
        $aPlugin->upgrade();
    }

    // Add callbacks to hooks
    $aPlugin->addActionsAndFilters();

    // save referer in session
    global $session_handler_error;
    $session_handler_error = false;
    if (!session_id()) {
        set_error_handler('session_error_handling_function');
        session_start();
        restore_error_handler();
    }
    
    if (!$file) {
        $file = __FILE__;
    }
    // Register the Plugin Activation Hook
    register_activation_hook($file, array(&$aPlugin, 'activate'));


    // Register the Plugin Deactivation Hook
    register_deactivation_hook($file, array(&$aPlugin, 'deactivate'));
}
