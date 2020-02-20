<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpSetup' ) ) :

final class MywpSetup {

  private static $instance;

  private function __construct() {}

  public static function get_instance() {

    if ( !isset( self::$instance ) ) {

      self::$instance = new self();

    }

    return self::$instance;

  }

  private function __clone() {}

  private function __wakeup() {}

  public static function init() {

    //add_action( 'init' , array( __CLASS__ , 'wp_init' ) );

  }

  public static function wp_init() {

    //load_plugin_textdomain( 'my-wp' , false , MYWP_PLUGIN_DIRNAME . '/languages' );

  }

}

MywpSetup::init();

endif;
