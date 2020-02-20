<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleSiteGeneral' ) ) :

final class MywpControllerModuleSiteGeneral extends MywpControllerAbstractModule {

  static protected $network = true;

  static protected $id = 'site_general';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['disable_file_edit'] = '';
    $initial_data['disable_user_admin'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['disable_file_edit'] = false;
    $default_data['disable_user_admin'] = false;

    return $default_data;

  }

  protected static function after_init() {

    if( ! self::is_do_controller() ) {

      return false;

    }

    self::disable_file_edit();

  }

  private static function disable_file_edit() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['disable_file_edit'] ) ) {

      return false;

    }

    if( ! defined( 'DISALLOW_FILE_EDIT' ) ) {

      define( 'DISALLOW_FILE_EDIT' , true );

    }

    self::after_do_function( __FUNCTION__ );

  }

  public static function mywp_wp_loaded() {

    if( ! self::is_do_controller() ) {

      return false;

    }

    self::disable_user_admin();

  }

  public static function disable_user_admin() {

    if( is_network_admin() ) {

      return false;

    }

    if( ! is_admin() ) {

      return false;

    }

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['disable_user_admin'] ) ) {

      return false;

    }

    $current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    if( $current_url === user_admin_url() ) {

      wp_redirect( home_url() );
      exit;

    }

    self::after_do_function( __FUNCTION__ );

  }

}


MywpControllerModuleSiteGeneral::init();

endif;
