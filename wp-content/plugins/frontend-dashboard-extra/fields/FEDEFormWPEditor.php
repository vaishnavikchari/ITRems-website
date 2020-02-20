<?php

if ( ! defined('ABSPATH')) {
    exit;
}


/**
 * @param $options
 *
 * @return string
 */
function fed_e_form_wpeditor($options)
{
    $name     = fed_get_data('input_meta', $options);
    $value    = fed_get_data('user_value', $options, '', false);
    $class    = 'form-control '.fed_get_data('class_name', $options);
    $id       = isset($options['id_name']) && $options['id_name'] != '' ? 'id="'.esc_attr($options['id_name']).'"' : null;
    $extended = isset($options['extended']) ? (is_string($options['extended']) ? unserialize($options['extended']) : $options['extended']) : array();

    $media_buttons = fed_get_data('settings.media_buttons', $extended);
    $quicktags     = fed_get_data('settings.quicktags', $extended, false);
    $textarea_rows = fed_get_data('settings.textarea_rows', $extended, 10);
    $editor_height = fed_get_data('settings.editor_height', $extended, 30);

    return '<label id="'.$id.'">'.fed_get_wp_editor($value,
            $name, array(
                'textarea_name' => $name,
                'media_buttons' => $media_buttons,
                'textarea_rows' => $textarea_rows,
                'editor_height' => $editor_height,
                'editor_class'  => $class,
                'quicktags'     => $quicktags,
            )).'</label>';
}

add_filter('fed_default_extended_fields', 'fed_e_default_extended_fields');
add_filter('fed_process_form_fields', 'fed_e_process_form_fields', 10, 4);

/**
 * @param $default
 * @param $row
 * @param $action
 * @param $update
 *
 * @return array
 */
function fed_e_process_form_fields($default, $row, $action, $update)
{
    if ($row['input_type'] === 'wp_editor') {
        if ($update === 'yes') {
            $extended = array(
                'extended' => serialize(array(
                    'settings' => array(
                        'textarea_name'    => fed_get_data('input_meta', $row),
                        'media_buttons'    => fed_get_data('extended.settings.media_buttons', $row),
                        'textarea_rows'    => fed_get_data('extended.settings.textarea_rows', $row, 10),
                        'editor_class'     => fed_get_data('class_name', $row),
                        'editor_height'    => fed_get_data('extended.settings.editor_height', $row, 5),
                        'quicktags'        => fed_get_data('extended.settings.quicktags', $row),
                    ),
                )),
            );

            return array_merge($default, $extended);
        } else {
            if (is_string($row['extended'])) {
                $default['extended'] = unserialize($row['extended']);

                return $default;
            }
            if (is_array($row['extended'])) {
                $default['extended'] = $row['extended'];

                return $default;
            }
        }
    }

    return $default;
}

/**
 * @param $fields
 *
 * @return array
 */
function fed_e_default_extended_fields($fields)
{
    $array = array(
        'settings' => array(),
    );

    return array_merge($fields, $array);
}