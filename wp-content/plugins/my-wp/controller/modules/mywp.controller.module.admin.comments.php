<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminComments' ) ) :

final class MywpControllerModuleAdminComments extends MywpControllerAbstractModule {

  static protected $id = 'admin_comments';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['list_columns'] = array();

    $initial_data['per_page_num'] = '';
    $initial_data['hide_search_box'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['list_columns'] = array();

    $default_data['per_page_num'] = 20;
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

    add_action( 'mywp_ajax' , array( __CLASS__ , 'mywp_ajax' ) , 1000 );

    add_action( 'load-edit-comments.php' , array( __CLASS__ , 'load_comments' ) , 1000 );

  }

  public static function mywp_ajax() {

    if( empty( $_POST['action'] ) ) {

      return false;

    }

    $action = strip_tags( $_POST['action'] );

    if( ! in_array( $action , array( 'edit-comment' , 'replyto-comment' ) ) ) {

      return false;

    }

    add_filter( 'manage_edit-comments_columns' , array( __CLASS__ , 'manage_columns' ) );

    add_filter( 'manage_comments_custom_column' , array( __CLASS__ , 'manage_column_body' ) , 10 , 2 );

    add_filter( 'manage_edit-comments_sortable_columns', array( __CLASS__ , 'manage_columns_sortable' ) );

  }


  public static function load_comments() {

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_search_box' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'change_column_width' ) );

    add_filter( 'comments_per_page' , array( __CLASS__ , 'comments_per_page' ) );

    add_filter( 'manage_edit-comments_columns' , array( __CLASS__ , 'manage_columns' ) );

    add_filter( 'manage_comments_custom_column' , array( __CLASS__ , 'manage_column_body' ) , 10 , 2 );

    add_filter( 'manage_edit-comments_sortable_columns', array( __CLASS__ , 'manage_columns_sortable' ) );

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

    echo 'body.wp-admin #comments-form .search-box { display: none; }';

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

  public static function comments_per_page( $per_page ) {

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

  public static function manage_column_body( $column_id , $comment_id ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $comment = get_comment( $comment_id );

    if( $column_id === 'id' ) {

      echo $comment_id;

    } elseif( $column_id === 'comment_author' ) {

      echo $comment->comment_author;

    } elseif( $column_id === 'comment_author_email' ) {

      echo $comment->comment_author_email;

    } elseif( $column_id === 'comment_author_url' ) {

      echo $comment->comment_author_url;

    }

    self::after_do_function( __FUNCTION__ );

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

MywpControllerModuleAdminComments::init();

endif;
