<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpHelper' ) ) :

final class MywpHelper {

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

  public static function error_after_called_message( $action = false , $called = false ) {

    if( $action === false or $called === false ) {

      return false;

    }

    $action  = strip_tags( $action );

    self::error_message( sprintf( __( 'This function can be called after "%s" action.' , 'my-wp' ) , $action ) , $called );

  }

  public static function error_require_message( $required_val = false , $called = false ) {

    if( $required_val === false or $called === false ) {

      return false;

    }

    $required_val  = strip_tags( $required_val );

    self::error_message( sprintf( __( 'The %s is required.' , 'my-wp' ) , $required_val ) , $called );

  }

  public static function error_not_found_message( $not_found = false , $called = false ) {

    if( $not_found === false or $called === false ) {

      return false;

    }

    $not_found  = strip_tags( $not_found );

    self::error_message( sprintf( __( '%s is not found.' , 'my-wp' ) , $not_found ) , $called );

  }

  public static function error_message( $message = false , $called = false ) {

    if( $message === false or $called === false ) {

      return false;

    }

    $called  = strip_tags( $called );

    $error_text = sprintf( __( '%1$s: %2$s' , 'my-wp' ) , $called , $message );

    MywpApi::add_error( $error_text );

  }

  public static function get_define( $define_name = false ) {

    if( empty( $define_name ) ) {

      $called_text = sprintf( '%1$s::%2$s( %3$s )' , __CLASS__ , __FUNCTION__ , '$define_name' );

      MywpHelper::error_not_found_message( '$define_name' , $called_text );

      return false;

    }

    $define_name = strip_tags( $define_name );

    if( ! defined( $define_name ) ) {

      return false;

    }

    return constant( $define_name );

  }

  public static function is_doing( $doing_name = false ) {

    if( empty( $doing_name ) ) {

      $called_text = sprintf( '%1$s::%2$s( %3$s )' , __CLASS__ , __FUNCTION__ , '$doing_name' );

      MywpHelper::error_not_found_message( '$doing_name' , $called_text );

      return false;

    }

    $define_name = '';

    if( 'cron' === $doing_name ) {

      $define_name = 'DOING_CRON';

    } elseif( 'xmlrpc' === $doing_name ) {

      $define_name = 'XMLRPC_REQUEST';

    } elseif( 'rest' === $doing_name ) {

      $define_name = 'REST_REQUEST';

    } elseif( 'ajax' === $doing_name ) {

      $define_name = 'DOING_AJAX';

    }

    $doing = self::get_define( $define_name );

    return $doing;

  }

  public static function get_byte( $memory = false ) {

    $memory = intval( $memory );

    if( empty( $memory ) ) {

      return false;

    }

    if( $memory > TB_IN_BYTES ) {

      $memory = number_format( $memory / TB_IN_BYTES , 2 );

      $unit = __( 'TB' );

    } elseif( $memory > GB_IN_BYTES ) {

      $memory = number_format( $memory / GB_IN_BYTES , 2 );

      $unit = __( 'GB' );

    } elseif( $memory > MB_IN_BYTES ) {

      $memory = number_format( $memory / MB_IN_BYTES , 2 );

      $unit = __( 'MB' );

    } elseif( $memory > KB_IN_BYTES ) {

      $memory = number_format( $memory / KB_IN_BYTES , 2 );

      $unit = __( 'KB' );

    } else {

      $memory = number_format( $memory / KB_IN_BYTES , 2 );

      $unit = __( 'Bytes' );

    }

    return sprintf( '%s %s' , $memory , $unit );

  }

  public static function get_all_sites() {

    $args = array( 'number' => '' );

    return get_sites( $args );

  }

  public static function get_max_allowed_packet_size() {

    $max_allowed_packet_size = apply_filters( 'mywp_get_max_allowed_packet_size' , 1000000 );

    return $max_allowed_packet_size;

  }

}

endif;
