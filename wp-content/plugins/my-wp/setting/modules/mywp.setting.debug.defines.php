<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugDefines' ) ) :

final class MywpSettingScreenDebugDefines extends MywpAbstractSettingModule {

  static protected $id = 'debug_defines';

  static protected $priority = 30;

  static private $menu = 'debug';

  protected static function after_init() {

    $id = 'network_' . self::$id;

    add_action( "mywp_setting_screen_content_{$id}" , array( __CLASS__ , 'mywp_current_setting_screen_content' ) );

  }

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'All Defines' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    if( is_multisite() ) {

      $setting_screens[ 'network_' . self::$id ] = array(
        'title' => __( 'All Defines' , 'my-wp' ),
        'menu' => 'network_' . self::$menu,
        'use_form' => false,
      );

    }

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $all_defines = get_defined_constants( true );

    if( empty( $all_defines['user'] ) ) {

      return false;

    }

    $removes = array(
      'DB_NAME',
      'DB_USER',
      'DB_PASSWORD',
      'DB_HOST',
      'AUTH_KEY',
      'SECURE_AUTH_KEY',
      'LOGGED_IN_KEY',
      'NONCE_KEY',
      'AUTH_SALT',
      'SECURE_AUTH_SALT',
      'LOGGED_IN_SALT',
      'NONCE_SALT',
      'COOKIEHASH',
      'USER_COOKIE',
      'PASS_COOKIE',
      'AUTH_COOKIE',
      'SECURE_AUTH_COOKIE',
      'LOGGED_IN_COOKIE',
      'FTP_PASS',
    );

    foreach( $removes as $define_name ) {

      if( isset( $all_defines['user'][ $define_name ] ) ) {

        $all_defines['user'][ $define_name ] = '********** (secret) **********';

      }

    }

    ?>
    <pre><?php print_r( $all_defines['user'] ); ?></pre>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugDefines::init();

endif;
