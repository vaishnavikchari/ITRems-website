<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminPosts' ) ) :

final class MywpControllerModuleAdminPosts extends MywpControllerAbstractModule {

  static protected $id = 'admin_posts';

  static private $post_type = '';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['list_columns'] = array();

    $initial_data['bulk_post_updated_messages'] = array();

    $initial_data['per_page_num'] = '';
    $initial_data['hide_add_new'] = '';
    $initial_data['hide_search_box'] = '';
    $initial_data['auto_output_column_body'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['list_columns'] = array();

    $default_data['bulk_post_updated_messages'] = array();

    $default_data['per_page_num'] = '';
    $default_data['hide_add_new'] = false;
    $default_data['hide_search_box'] = false;
    $default_data['auto_output_column_body'] = false;

    return $default_data;

  }

  public static function get_bulk_update_messages_default() {

    $bulk_update_messages_default = array(
      'updated' => _n( '%s post updated.', '%s posts updated.', 0 ),
      'locked' => _n( '%s post not updated, somebody is editing it.', '%s posts not updated, somebody is editing them.', 0 ),
      'deleted' => _n( '%s post permanently deleted.', '%s posts permanently deleted.', 0 ),
      'trashed' => _n( '%s post moved to the Trash.', '%s posts moved to the Trash.', 0 ),
      'untrashed' => _n( '%s post restored from the Trash.', '%s posts restored from the Trash.', 0 ),
    );

    return $bulk_update_messages_default;

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

    add_action( 'load-edit.php' , array( __CLASS__ , 'load_edit' ) , 1000 );

  }

  public static function mywp_model_get_option_key( $option_key ) {

    if( empty( self::$post_type ) ) {

      return $option_key;

    }

    $option_key .= '_' . self::$post_type;

    return $option_key;

  }

  public static function mywp_ajax() {

    if( empty( $_POST['action'] ) or $_POST['action'] !== 'inline-save' ) {

      return false;

    }

    if( empty( $_POST['screen'] ) ) {

      return false;

    }

    if( empty( $_POST['post_type'] ) ) {

      return false;

    }

    self::$post_type = strip_tags( $_POST['post_type'] );

    add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    add_filter( 'manage_edit-' . self::$post_type . '_columns' , array( __CLASS__ , 'manage_columns' ) );

    add_action( 'manage_' . self::$post_type . '_posts_custom_column' , array( __CLASS__ , 'manage_column_body' ) , 10 , 2 );

    add_filter( 'manage_edit-' . self::$post_type . '_sortable_columns', array( __CLASS__ , 'manage_columns_sortable' ) );

  }

  public static function load_edit() {

    global $typenow;

    if( empty( $typenow ) ) {

      return false;

    }

    self::$post_type = $typenow;

    add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    add_filter( 'bulk_post_updated_messages' , array( __CLASS__ , 'change_bulk_post_updated_messages' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_add_new' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_search_box' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'change_column_width' ) );

    add_filter( 'request' , array( __CLASS__ , 'sortable_request' ) );

    add_filter( 'posts_orderby',  array( __CLASS__ , 'sortable_posts_orderby' ) );

    add_filter( "edit_{$typenow}_per_page" , array( __CLASS__ , 'edit_per_page' ) );

    add_filter( "manage_edit-{$typenow}_columns" , array( __CLASS__ , 'manage_columns' ) );

    add_action( "manage_{$typenow}_posts_custom_column" , array( __CLASS__ , 'manage_column_body' ) , 10 , 2 );

    add_filter( "manage_edit-{$typenow}_sortable_columns", array( __CLASS__ , 'manage_columns_sortable' ) );

  }

  public static function change_bulk_post_updated_messages( $bulk_post_updated_messages ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $bulk_post_updated_messages;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['bulk_post_updated_messages'] ) ) {

      return $bulk_post_updated_messages;

    }

    $bulk_post_updated_messages_default = self::get_bulk_update_messages_default();

    foreach( $bulk_post_updated_messages_default as $key => $v ) {

      if( ! empty( $setting_data['bulk_post_updated_messages'][ $key ] ) ) {

        $bulk_post_updated_messages[ self::$post_type ][ $key ] = $setting_data['bulk_post_updated_messages'][ $key ];

      }

    }

    self::after_do_function( __FUNCTION__ );

    return $bulk_post_updated_messages;

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

    echo 'body.wp-admin #posts-filter .search-box { display: none; }';

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

    if( $request['orderby'] === 'post-thumbnails') {

      $request['meta_key'] = '_thumbnail_id';
      $request['orderby'] = 'meta_value';

    } elseif( ! empty( $request['post_type'] ) ) {

      $posts_all_custom_fields = MywpPostType::get_post_type_posts_all_custom_fields( $request['post_type'] );

      if( isset( $posts_all_custom_fields[ $request['orderby'] ] ) ) {

        $request['meta_key'] = $request['orderby'];
        $request['orderby'] = 'meta_value';

      }

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

  public static function edit_per_page( $per_page ) {

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

    $setting_data = self::get_setting_data();

    if( ! $setting_data['auto_output_column_body'] ) {

      return false;

    }

    $post = get_post( $post_id );

    if( $column_id === 'id' ) {

      echo $post_id;

    } elseif( $column_id === 'slug' ) {

      echo sanitize_title( $post->post_name );

    } elseif( $column_id === 'parent' ) {

      echo $post->post_parent;

    } elseif( $column_id === 'post-formats' ) {

      echo get_post_format_string( get_post_format( $post_id ) );

    } elseif( $column_id === 'excerpt' ) {

      if( ! empty( $post->post_excerpt ) ) {

        if( function_exists( 'mb_substr' ) ) {

          echo mb_substr( strip_tags( $post->post_excerpt ) , 0 , 20 ) . '.';

        } else {

          echo substr( strip_tags( $post->post_excerpt ) , 0 , 20 ) . '.';

        }

      }

    } elseif( $column_id === 'menu_order' ) {

      echo $post->menu_order;

    } elseif( $column_id === 'post-thumbnails' ) {

      if( has_post_thumbnail( $post_id ) ) {

        $thumbnail_id = get_post_thumbnail_id( $post_id );

        $thumbnail = wp_get_attachment_image_src( $thumbnail_id , 'post-thumbnail' , true );

        printf( '<img src="%s" style="%s" /></a>' , esc_attr( $thumbnail[0] ) , esc_attr( 'max-width:100%;' ) );

      }

    } else {

      $post_type_taxonomies = MywpTaxonomy::get_taxonomies( array( 'object_type' => array( self::$post_type ) ) );

      if( ! empty( $post_type_taxonomies[ $column_id ] ) ) {

        $post_terms = wp_get_post_terms( $post_id , $column_id , array( 'fields' => 'all' ) );

        if( ! empty( $post_terms ) ) {

          foreach( $post_terms as $post_term ) {

            printf( '<span class="post-term post-term-%d">[%s]</span> ' , esc_attr( $post_term->term_id ) , $post_term->name );

          }

        }

      } else {

        $post_meta = MywpPostType::get_post_meta( $post_id , $column_id );

        if( ! empty( $post_meta ) ) {

          echo $post_meta;

        }

      }

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

MywpControllerModuleAdminPosts::init();

endif;
