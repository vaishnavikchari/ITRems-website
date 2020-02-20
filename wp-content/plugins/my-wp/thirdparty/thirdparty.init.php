<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpThirdpartyInit' ) ) :

final class MywpThirdpartyInit {

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

    add_action( 'mywp_plugins_loaded' , array( __CLASS__ , 'plugins_loaded_include_modules' ) , 20 );
    add_action( 'mywp_after_setup_theme' , array( __CLASS__ , 'after_setup_theme_include_modules' ) , 20 );

    add_action( 'mywp_after_setup_theme' , array( __CLASS__ , 'mywp_after_setup_theme' ) , 100 );

  }

  public static function plugins_loaded_include_modules() {

    $dir = MYWP_PLUGIN_PATH . 'thirdparty/modules/';

    $includes = array(
      'acf'         => $dir . 'advanced-custom-fields.php',
      'woocommerce' => $dir . 'woocommerce.php',
    );

    $includes = apply_filters( 'mywp_thirdparty_plugins_loaded_include_modules' , $includes );

    MywpApi::require_files( $includes );

  }

  public static function after_setup_theme_include_modules() {

    $includes = array();

    $includes = apply_filters( 'mywp_thirdparty_after_setup_theme_include_modules' , $includes );

    MywpApi::require_files( $includes );

  }

  public static function mywp_after_setup_theme() {

    if( ! MywpDeveloper::is_debug() ) {

      return false;

    }

    $thirdparties = MywpThirdparty::get_thirdparties();

    if( empty( $thirdparties ) ) {

      return false;

    }

    foreach( $thirdparties as $plugin_id => $plugin_setting ) {

      MywpThirdparty::set_plugin( $plugin_id , $plugin_setting );

    }

  }

}

endif;
