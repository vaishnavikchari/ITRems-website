<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpIncompatible' ) ) :

final class MywpIncompatible {

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

    add_action( 'admin_notices' , array( __CLASS__ , 'admin_notices' ) );

    add_action( 'network_admin_notices' , array( __CLASS__ , 'admin_notices' ) );

  }

  public static function admin_notices() {

    if( is_multisite() ) {

      if( ! MywpApi::is_network_manager() ) {

        return false;

      }

    } else {

      if( ! MywpApi::is_manager() ) {

        return false;

      }

    }

    echo '<div class="error">';

    echo '<p>';

    printf( __( 'Sorry, My WP is <strong>Incompatible</strong> with your version of WordPress. Require version  %s.' , 'my-wp' ) , MYWP_REQUIRED_WP_VERSION );

    echo '</p>';

    echo '</div>';

  }

}

endif;
