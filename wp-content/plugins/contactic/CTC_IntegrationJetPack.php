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

class CTC_IntegrationJetPack {

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
        add_action('grunion_pre_message_sent', array(&$this, 'saveFormData'), 10, 3);
    }

    /**
     * @param $post_id int
     * @param $all_values array
     * @param $extra_values array
     * @return object
     */
    public function saveFormData($post_id, $all_values, $extra_values) {
        try {
            $data = $this->convertData($post_id, $all_values);

            return $this->plugin->saveFormData($data);
        } catch (Exception $ex) {
            $this->plugin->getErrorLog()->logException($ex);
        }
        return true;
    }

    public function convertData($post_id, $all_values) {

        $title = 'JetPack Contact Form';
        if (isset($_POST['contact-form-id'])) {
            $title .= ' ' . sanitize_text_field($_POST['contact-form-id']);
        }
        else {
            $title .= ' ' . $post_id;
        }

        $all_values['post_id'] = $post_id;
        return (object)  array(
                'title' => $title,
                'posted_data' => $all_values,
                'uploaded_files' => null);
    }


}