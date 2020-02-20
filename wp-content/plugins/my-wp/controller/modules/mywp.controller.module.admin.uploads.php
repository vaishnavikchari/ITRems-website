<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminUploads' ) ) :

final class MywpControllerModuleAdminUploads extends MywpControllerAbstractModule {

  static protected $id = 'admin_uploads';

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

    add_action( 'load-upload.php' , array( __CLASS__ , 'load_uploads' ) , 1000 );

  }

  public static function load_uploads() {

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_add_new' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_search_box' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'change_column_width' ) );

    add_filter( 'request' , array( __CLASS__ , 'sortable_request' ) );

    add_filter( 'posts_orderby',  array( __CLASS__ , 'sortable_posts_orderby' ) );

    add_filter( 'upload_per_page' , array( __CLASS__ , 'upload_per_page' ) );

    add_filter( 'manage_media_columns' , array( __CLASS__ , 'manage_columns' ) );

    add_filter( 'manage_media_custom_column' , array( __CLASS__ , 'manage_column_body' ) , 10 , 2 );

    add_filter( 'manage_upload_sortable_columns', array( __CLASS__ , 'manage_columns_sortable' ) );

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

    echo 'body.wp-admin #media-search-input { display: none; }';

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

  public static function sortable_request( $request ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $request;

    }

    if( empty( $request['orderby'] ) ) {

      return $request;

    }

    if( $request['orderby'] === 'image_alt') {

      $request['meta_key'] = '_wp_attachment_image_alt';
      $request['orderby'] = 'meta_value';

    }

    self::after_do_function( __FUNCTION__ );

    return $request;

  }

  public static function sortable_posts_orderby( $orderby_statement ) {

    global $wpdb;
    global $wp_query;

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $orderby_statement;

    }

    if( strpos( $orderby_statement , 'post_date' ) !== false ) {

      $orderby = $wp_query->get( 'orderby' );

      if( empty( $orderby ) ) {

        return $orderby_statement;

      }

      if( $orderby === 'post_excerpt' ) {

        $order = $wp_query->get( 'order' );

        $orderby_statement = sprintf( '%1$s.%2$s %3$s' , $wpdb->posts , $orderby , $order );

      }

    }

    self::after_do_function( __FUNCTION__ );

    return $orderby_statement;

  }

  public static function upload_per_page( $per_page ) {

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

  public static function manage_column_body( $column_id , $post_id ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $post = get_post( $post_id );

    if( $column_id === 'id' ) {

      echo $post_id;

    } elseif( $column_id === 'media_title' ) {

      echo _draft_or_post_title( $post_id );

    } elseif( $column_id === 'image_alt' ) {

      $image_alt = get_post_meta( $post_id , '_wp_attachment_image_alt' , true );

      echo wp_strip_all_tags( stripslashes( $image_alt ) );

    } elseif( $column_id === 'post_excerpt' ) {

      if( ! empty( $post->post_excerpt ) ) {

        if( function_exists( 'mb_substr' ) ) {

          echo mb_substr( strip_tags( $post->post_excerpt ) , 0 , 20 ) . '.';

        } else {

          echo substr( strip_tags( $post->post_excerpt ) , 0 , 20 ) . '.';

        }

      }

    } elseif( $column_id === 'post_content' ) {

      if( ! empty( $post->post_content ) ) {

        if( function_exists( 'mb_substr' ) ) {

          echo mb_substr( strip_tags( $post->post_content ) , 0 , 20 ) . '.';

        } else {

          echo substr( strip_tags( $post->post_content ) , 0 , 20 ) . '.';

        }

      }

    } elseif( $column_id === 'file_url' ) {

      printf( '<input type="text" readonly="readonly" value="%s" class="large-text" />' , esc_url( wp_get_attachment_url( $post_id ) ) );

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

MywpControllerModuleAdminUploads::init();

endif;
