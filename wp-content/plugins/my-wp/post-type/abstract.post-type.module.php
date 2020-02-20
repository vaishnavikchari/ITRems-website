<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpPostTypeAbstractModule' ) ) :

abstract class MywpPostTypeAbstractModule {

  private static $instance;

  protected static $id;

  private function __construct() {}

  public static function get_instance() {

    $class = get_called_class();

    if ( !isset( self::$instance[ $class ] ) ) {

      self::$instance[ $class ] = new static();

    }

    return self::$instance[ $class ];

  }

  private function __clone() {}

  private function __wakeup() {}

  public static function init() {

    $class = get_called_class();

    if( empty( static::$id ) ) {

      $called_text = sprintf( 'class %s' , $class );

      MywpHelper::error_require_message( '"static protected $id"' , $called_text );

      return false;

    }

    add_filter( 'mywp_post_types' , array( $class , 'mywp_post_types' ) );

    add_filter( "mywp_post_type_get_post_{$class::$id}" , array( $class , 'current_mywp_post_type_get_post' ) );

    add_filter( "manage_{$class::$id}_posts_columns" , array( $class , 'current_manage_posts_columns' ) );

    add_action( "manage_{$class::$id}_posts_custom_column" , array( $class , 'current_manage_posts_custom_column' ) , 10 , 2 );

    add_filter( 'mywp_setting_post_types' , array( $class , 'mywp_setting_post_types' ) );

    add_filter( "edit_{$class::$id}_per_page" , array( $class , 'current_edit_per_page' ) , 11 );

  }

  public static function mywp_post_types( $post_types ) {

    $class = get_called_class();

    $post_types[ static::$id ] = static::get_regist_post_type_args();

    return $post_types;

  }

  protected static function get_regist_post_type_args() {

    return array();

  }

  public static function current_mywp_post_type_get_post( $post ) {

    return $post;

  }

  public static function current_manage_posts_columns( $posts_columns ) {

    return $posts_columns;

  }

  public static function current_manage_posts_custom_column( $column_name , $post_id ) {}

  public static function mywp_setting_post_types( $post_types ) {

    if( ! empty( $post_types[ static::$id ] ) ) {

      if( empty( $post_types[ static::$id ]->show_ui ) ) {

        unset( $post_types[ static::$id ] );

      }

    }

    return $post_types;

  }

  public static function current_edit_per_page( $per_page ) {

    $per_page = apply_filters( 'current_edit_per_page' , $per_page );

    return $per_page;

  }

}

endif;
