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

class CTC_IntegrationFSCF {

    /**
     * @var ContacticPlugin
     */
    var $plugin;

    /**
     * @param $plugin ContacticPlugin
     */
    function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function registerHooks() {
        add_action('fsctf_mail_sent', array(&$this->plugin, 'saveFormData'));
        add_action('fsctf_menu_links', array(&$this, 'fscfMenuLinks'));
    }

    /**
     * Function courtesy of Mike Challis, author of Fast Secure Contact Form.
     * Displays Admin Panel links in FSCF plugin menu
     * @return void
     */
    public function fscfMenuLinks() {
        $displayName = $this->plugin->getPluginDisplayName();
        echo '
        <p>
      ' . $displayName .
                ' | <a href="admin.php?page=' . $this->plugin->getSlug('submissions') . '">' .
                __('Database', 'contactic') .
                '</a>  | <a href="admin.php?page=CF7DBPluginSettings">' .
                __('Database Options', 'contactic') .
                '</a>  | <a href="admin.php?page=' . $this->plugin->getSlug('shortcodes') . '">' .
                __('Build Shortcode', 'contactic') .
                '</a> | <a href="https://contactic.io/docs/">' .
                __('Reference', 'contactic') . '</a>
       </p>
      ';
    }

}