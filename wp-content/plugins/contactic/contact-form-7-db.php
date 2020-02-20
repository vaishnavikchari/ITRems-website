<?php
/**
 * Plugin Name: Contactic
 * Plugin URI: https://contactic.io/
 * Version: 1.4.0
 * Author: Contactic
 * Author URI: https://contactic.io/
 * Text Domain: contact-form-7-to-database-plus
 * License: GPL3
 * Description: Save form submissions to the database from severals contact form plugins and themes. Includes exports, shortcodes, tracking and stats. | <a href="admin.php?page=ContacticPluginSubmissions">Data</a> | <a href="admin.php?page=ContacticPluginShortCodeBuilder">Shortcodes</a> | <a href="admin.php?page=ContacticPluginSettings">Settings</a> | <a href="https://contactic.io/docs/">Docs</a>
 * Text Domain: contactic
 * Domain Path: /languages
 */


$CTC_Plugin_minimalRequiredPhpVersion = '5.4';

/**
 * echo error message indicating wrong minimum PHP version required
 */
function CTC_Plugin_noticePhpVersionWrong() {
    global $CTC_Plugin_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "Contactic" requires a newer version of PHP to be running.',  'contactic').
            '<br/>' . __('Minimal version of PHP required: ', 'contactic') . '<strong>' . $CTC_Plugin_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'contactic') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function CTC_Plugin_PhpVersionCheck() {
    global $CTC_Plugin_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $CTC_Plugin_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'CTC_Plugin_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      https://codex.wordpress.org/I18n_for_WordPress_Developers
 *      https://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function CTC_Plugin_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('contactic', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// Initialize i18n
add_action('plugins_loaded','CTC_Plugin_i18n_init');

// Run the version check.
// If it is successful, continue with initialization for this plugin
if (CTC_Plugin_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('CTC_Plugin_init.php');
    CTC_Plugin_init(__FILE__);
}

function CTC_add_plugin_meta_links($meta_fields, $file) {
  if ( plugin_basename(__FILE__) == $file ) {
    $plugin_url = "https://wordpress.org/support/plugin/contactic/";
    $meta_fields[] = "<a href='" . $plugin_url . "' target='blank'>" . _('Support Forum') . "</a>";
    $meta_fields[] = "Please help us ❤ → <a href='https://wordpress.org/support/plugin/contactic/reviews/?filter=5#new-post' target='blank' title='" . _('Rate') . "'>
            <i class='wdi-rate-stars'>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "</i></a>";

    $stars_color = "#ffb900";

    echo "<style>"
      . ".wdi-rate-stars{display:inline-block;color:" . $stars_color . ";position:relative;top:3px;}"
      . ".wdi-rate-stars svg{fill:" . $stars_color . ";}"
      . ".wdi-rate-stars svg:hover{fill:" . $stars_color . "}"
      . ".wdi-rate-stars svg:hover ~ svg{fill:none;}"
      . "</style>";
  }

  return $meta_fields;
}

add_filter("plugin_row_meta", 'CTC_add_plugin_meta_links', 10, 2);
