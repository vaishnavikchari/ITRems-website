<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminUsers' ) ) :

final class MywpControllerModuleAdminUsers extends MywpControllerAbstractModule {

  static protected $id = 'admin_users';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['list_columns'] = array();

    $initial_data['per_page_num'] = '';
    $initial_data['hide_add_new'] = '';
    $initial_data['hide_search_box'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['list_columns'] = array();

    $default_data['per_page_num'] = 20;
    $default_data['hide_add_new'] = false;
    $default_data['hide_search_box'] = false;

    return $default_data;

  }

  public static function mywp_wp_loaded() {

    if( ! is_admin() ) {

      return false;

    }

    if( is_network_admin() ) {

      return false;

    }

    if( ! self::is_do_controller() ) {

      return false;

    }

    add_action( 'load-users.php' , array( __CLASS__ , 'load_users' ) , 1000 );

  }

  public static function load_users() {

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_add_new' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_search_box' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'change_column_width' ) );

    add_filter( 'users_per_page' , array( __CLASS__ , 'users_per_page' ) );

    add_filter( 'manage_users_columns' , array( __CLASS__ , 'manage_columns' ) );

    add_filter( 'manage_users_custom_column' , array( __CLASS__ , 'manage_column_body' ) , 10 , 3 );

    add_filter( 'manage_users_sortable_columns', array( __CLASS__ , 'manage_columns_sortable' ) );

  }

  public static function hide_add_new() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_add_new'] ) ) {

      return false;

    }

    echo '<style>';

    echo 'body.wp-admin .wrap h1 a { display: none; }';
    echo 'body.wp-admin .wrap .page-title-action { display: none; }';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_search_box() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_search_box'] ) ) {

      return false;

    }

    echo '<style>';

    echo 'body.wp-admin .search-box { display: none; }';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function change_column_width() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['list_columns'] ) ) {

      return false;

    }

    $columns = array();

    foreach( $setting_data['list_columns'] as $column_id => $column_setting ) {

      if( empty( $column_setting['width'] ) ) {

        continue;

      }

      $columns[ $column_id ] = $column_setting['width'];

    }

    if( empty( $columns ) ) {

      return false;

    }

    echo '<style>';

    foreach( $columns as $column_id => $width ) {

      echo 'body.wp-admin .wp-list-table.widefat thead th.column-' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';
      echo 'body.wp-admin .wp-list-table.widefat thead td.column-' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';

      echo 'body.wp-admin .wp-list-table.widefat thead th#' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';
      echo 'body.wp-admin .wp-list-table.widefat thead td#' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';

    }

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function users_per_page( $per_page ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $per_page;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['per_page_num'] ) ) {

      return $per_page;

    }

    $per_page = $setting_data['per_page_num'];

    self::after_do_function( __FUNCTION__ );

    return $per_page;

  }

  public static function manage_columns( $columns ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $columns;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['list_columns'] ) ) {

      return $columns;

    }

    $columns = array();

    foreach( $setting_data['list_columns'] as $column_id => $column_setting ) {

      $columns[ $column_id ] = $column_setting['title'];

    }

    self::after_do_function( __FUNCTION__ );

    return $columns;

  }

  public static function manage_column_body( $content , $column_id , $user_id ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $user_data = get_userdata( $user_id );

    if( $column_id === 'id' ) {

      $content = $user_id;

    } else {

      $user_meta = get_user_meta( $user_id , $column_id );

      if( ! empty( $user_meta[0] ) ) {

        if( is_object( $user_meta[0] ) or is_array( $user_meta[0] ) ) {

          $content = print_r( $user_meta[0] , true );

        } else {

          $content = $user_meta[0];

        }

      }

    }

    self::after_do_function( __FUNCTION__ );

    return $content;

  }

  public static function manage_columns_sortable( $sortables ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $sortables;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['list_columns'] ) ) {

      return $sortables;

    }

    $sortables = array();

    foreach( $setting_data['list_columns'] as $column_id => $column_setting ) {

      if( ! empty( $column_setting['sort'] ) ) {

        $sortables[ $column_id ] = $column_setting['orderby'];

      }

    }

    self::after_do_function( __FUNCTION__ );

    return $sortables;

  }

}

MywpControllerModuleAdminUsers::init();

endif;
