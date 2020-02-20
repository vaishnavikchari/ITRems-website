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

class CTC_IntegrationFormidableForms {
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
        // https://formidablepro.com/knowledgebase/frm_after_create_entry/
        add_action('frm_after_create_entry', array(&$this, 'saveFormData'), 30, 2);
    }

    /**
     * @param int $entry_id
     * @param int $form_id
     * @return bool
     */
    public function saveFormData($entry_id, $form_id) {
        global $wpdb;

        // Get form title
        $sql = "SELECT name FROM {$wpdb->prefix}frm_forms WHERE id = %d";
        $sql = $wpdb->prepare($sql, $form_id);
        $title = $wpdb->get_var($sql);

        if (!$title) {
            return true;
        }

        // Get submission values
        $sql = "SELECT f.name AS 'key', m.meta_value AS 'value' FROM {$wpdb->prefix}frm_item_metas m, {$wpdb->prefix}frm_fields f WHERE m.field_id = f.id AND m.item_id = %d";
        $sql = $wpdb->prepare($sql, $entry_id);
        $results = $wpdb->get_results($sql, ARRAY_A);
        if (!$results) {
            return true;
        }

        $postedData = array();
        foreach ($results as $result) {
            $key = $result['key'];
            $value = $result['value'];
            if (is_serialized($value)) {
                $value = unserialize($value);
                if (is_array($value)) {
                    $value = implode(',', $value);
                } else {
                    $value = (string)$value; // shouldn't get here
                }
            }
            $postedData[$key] = $value;
        }

        // Save submission
        $data = (object)array(
                'title' => $title,
                'posted_data' => $postedData,
                'uploaded_files' => array()); // todo
        $this->plugin->saveFormData($data);

        return true;
    }


}
