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

require_once('CTC_ShortCodeLoader.php');
require_once('CTC_DereferenceShortcodeVars.php');

class CTC_ShortcodeExportUrl extends CTC_ShortCodeLoader {

    /**
     * @param $atts array of short code attributes
     * @param $content string not used
     * @return string export link
     */
    public function handleShortcode($atts, $content = null) {
        $atts = $this->decodeAttributes($atts);
        $atts = $this->dereferenceShortCodeVars($atts); // special case for this short code
        $params = array();
        $params[] = $this->getAdminUrlPrefix('admin-ajax.php');
        $params[] = 'action=cfdb-export';

        $special = array('urlonly', 'linktext', 'role');
        foreach ($atts as $key => $value) {
            if (!in_array($key, $special)) {
                $params[] = sprintf('&%s=%s', urlencode($key), urlencode($value));
            } else if ($key == 'role') {
                require_once('ContacticPlugin.php');
                $plugin = new ContacticPlugin();
                $isAuth = $plugin->isUserRoleEqualOrBetterThan($value);
                if (!$isAuth) {
                    // Not authorized. Print no link.
                    return '';
                }
            }
        }
        $url = implode($params);

        if (isset($atts['urlonly']) && $atts['urlonly'] == 'true') {
            return $url;
        }

        $linkText = __('Export', 'contactic');
        if (isset($atts['linktext'])) {
            $linkText = $atts['linktext'];
        }

        return sprintf('<a href="%s">%s</a>', $url, $linkText);
    }

    // https://wordpress.org/support/topic/using-a-variable-from-url-in-shortcode?replies=8#post-7940089
    public function dereferenceShortCodeVars($atts) {
        $deref = new CTC_DereferenceShortcodeVars;
        if (is_array($atts)) {
            foreach ($atts as $key => $value) {
                $atts[$key] = $deref->convert($value);
            }
        }
        return $atts;
    }


    // TODO: this method is duplicated from CFDB7Plugin.php
    public function getAdminUrlPrefix($path) {
        $url = admin_url($path);
        if (strpos($url, '?') === false) {
            return $url . '?';
        } else {
            return $url . '&';
        }
    }

}
