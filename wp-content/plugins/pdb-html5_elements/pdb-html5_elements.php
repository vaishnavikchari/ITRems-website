<?php
/*
 * Plugin Name: Participants Database HTML5 Form Elements
 * Version: 1.7.1
 * Description: Adds HTML5 special input types
 * Author: Roland Barker, xnau webdesign
 * Plugin URI:  https://xnau.com/shop/html5-form-elements/
 * Support URI:  https://xnau.com/shop/html5-form-elements/
 * License: GPL2
 * Text Domain: pdb-html5
 */
if ( class_exists( 'Participants_Db' ) ) {
  pdb_html5_initialize();
} else {
  add_action( 'participants-database_activated', 'pdb_html5_initialize' );
}

function pdb_html5_initialize() {
  require_once plugin_dir_path(__FILE__) . '/PDb_HTML5_Elements.php';
  require_once plugin_dir_path(__FILE__) . '/PDb_HTML5_Element.php';
  new PDb_HTML5_Elements(__FILE__);
}

