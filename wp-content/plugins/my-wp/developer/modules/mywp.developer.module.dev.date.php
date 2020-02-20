<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleDevDate' ) ) :

final class MywpDeveloperModuleDevDate extends MywpDeveloperAbstractModule {

  static protected $id = 'dev_date';

  static protected $priority = 90;

  public static function mywp_debug_renders( $debug_renders ) {

    $debug_renders[ self::$id ] = array(
      'debug_type' => 'dev',
      'title' => __( 'Date and Time' , 'my-wp' ),
    );

    return $debug_renders;

  }

  protected static function get_debug_lists() {

    $debug_lists = array(
      'get_option( "date_format" )' => get_option( 'date_format' ),
      'get_option( "time_format" )' => get_option( 'time_format' ),
      'get_option( "timezone_string" )' => get_option( 'timezone_string' ),
      'get_option( "gmt_offset" )' => get_option( 'gmt_offset' ),

      'date_default_timezone_get()' => date_default_timezone_get(),
      'ini_get( "date.timezone" )' => ini_get( 'date.timezone' ),

      'time()' => time(),
      'date( "Y-m-d H:i:s" )' => date( 'Y-m-d H:i:s' ),
      'date( "Y-m-d H:i:s" , time() )' => date( 'Y-m-d H:i:s' , time() ),
      'date( "Y-m-d H:i:s" , time() + ( get_option( "gmt_offset" ) * HOUR_IN_SECONDS ) )' => date( 'Y-m-d H:i:s' , time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
      'gmdate( "Y-m-d H:i:s" )' => gmdate( 'Y-m-d H:i:s' ),
      'strtotime( "now" )' => strtotime( 'now' ),
      'date( "Y-m-d H:i:s" , strtotime( "now" ) )' => date( 'Y-m-d H:i:s' , strtotime( 'now' ) ),

      'current_time( "timestamp" )' => current_time( 'timestamp' ),
      'current_time( "timestamp" , true )' => current_time( 'timestamp' , true ),
      'date( "Y-m-d H:i:s" , current_time( "timestamp" ) )' => date( 'Y-m-d H:i:s' , current_time( 'timestamp' ) ),
      'current_time( "mysql" )' => current_time( 'mysql' ),
      'current_time( "mysql" , true )' => current_time( 'mysql' , true ),

      'mysql2date( "G" , current_time( "mysql" ) )' => mysql2date( 'G' , current_time( 'mysql' ) ),
      'mysql2date( "U" , current_time( "mysql" ) )' => mysql2date( 'U' , current_time( 'mysql' ) ),
      'mysql2date( get_option( "date_format" ) , current_time( "mysql" ) )' => mysql2date( get_option( 'date_format' ) , current_time( 'mysql' ) ),
      'mysql2date( get_option( "time_format" ) , current_time( "mysql" ) )' => mysql2date( get_option( 'time_format' ) , current_time( 'mysql' ) ),
      'mysql2date( "l, F j, Y" , current_time( "mysql" ) )' => mysql2date( "l, F j, Y" , current_time( 'mysql' ) ),
      'mysql2date( "l, F j, Y" , current_time( "mysql" ) , false )' => mysql2date( "l, F j, Y" , current_time( 'mysql' ) , false ),

      'date_i18n( "Y-m-d H:i:s" )' => date_i18n( 'Y-m-d H:i:s' ),
      'date_i18n( "Y-m-d H:i:s" , false )' => date_i18n( 'Y-m-d H:i:s' , false ),
      'date_i18n( "Y-m-d H:i:s" , false , true )' => date_i18n( 'Y-m-d H:i:s' , false , true ),

      'date_i18n( "Y-m-d H:i:s" , current_time( "timestamp" ) )' => date_i18n( 'Y-m-d H:i:s' , current_time( 'timestamp' ) ),
    );

    return $debug_lists;

  }

}

MywpDeveloperModuleDevDate::init();

endif;
