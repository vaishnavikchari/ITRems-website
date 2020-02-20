<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminPostEdit' ) ) :

final class MywpControllerModuleAdminPostEdit extends MywpControllerAbstractModule {

  static protected $id = 'admin_post_edit';

  static private $post_type = '';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['block_editor_meta_boxes'] = array();

    $initial_data['meta_boxes'] = array();

    $initial_data['use_classic_editor'] = '';

    $initial_data['post_updated_messages'] = array();

    $initial_data['hide_add_new'] = '';
    $initial_data['hide_title'] = '';
    $initial_data['change_title_placeholder'] = '';
    $initial_data['auto_change_title'] = '';
    $initial_data['hide_permalink'] = '';
    $initial_data['hide_change_permalink'] = '';
    $initial_data['hide_content'] = '';
    $initial_data['prevent_meta_box'] = '';
    $initial_data['forced_editor'] = '';

    $initial_data['hide_publish_metabox_draft'] = '';
    $initial_data['hide_publish_metabox_preview'] = '';
    $initial_data['hide_publish_metabox_change_post_status'] = '';
    $initial_data['hide_publish_metabox_change_publish_status'] = '';
    $initial_data['hide_publish_metabox_change_publish_on'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $initial_data['block_editor_meta_boxes'] = array();

    $default_data['meta_boxes'] = array();

    $default_data['use_classic_editor'] = false;

    $default_data['post_updated_messages'] = array();

    $default_data['hide_add_new'] = false;
    $default_data['hide_title'] = false;
    $default_data['change_title_placeholder'] = '';
    $default_data['auto_change_title'] = false;
    $default_data['hide_permalink'] = false;
    $default_data['hide_change_permalink'] = false;
    $default_data['hide_content'] = false;
    $default_data['prevent_meta_box'] = false;
    $default_data['forced_editor'] = false;

    $default_data['hide_publish_metabox_draft'] = false;
    $default_data['hide_publish_metabox_preview'] = false;
    $default_data['hide_publish_metabox_change_post_status'] = false;
    $default_data['hide_publish_metabox_change_publish_status'] = false;
    $default_data['hide_publish_metabox_change_publish_on'] = false;

    return $default_data;

  }

  public static function get_update_messages_default() {

    $update_messages_default = array(
      1 => array(
        'title' => __( 'Post updated' , 'my-wp' ),
        'message' => __( 'Post updated.' ),
      ),
      2 => array(
        'title' => __( 'Custom field updated' , 'my-wp' ),
        'message' => __( 'Custom field updated.' ),
      ),
      3 => array(
        'title' => __( 'Custom field deleted' , 'my-wp' ),
        'message' => __( 'Custom field updated.' ),
      ),
      4 => array(
        'title' => __( 'Post updated' , 'my-wp' ),
        'message' => __( 'Post updated.' ),
      ),
      5 => array(
        'title' => __( 'Revision' ),
        'message' => sprintf( __( 'Post restored to revision from %s.' ), __( 'Post Title' , 'my-wp' ) ),
      ),
      6 => array(
        'title' => __( 'Post published' , 'my-wp' ),
        'message' => __( 'Post published.' ),
      ),
      7 => array(
        'title' => __( 'Post saved' , 'my-wp' ),
        'message' => __( 'Post saved.' ),
      ),
      8 => array(
        'title' => __( 'Post submitted' , 'my-wp' ),
        'message' => __( 'Post submitted.' ),
      ),
      9 => array(
        'title' => __( 'Post Scheduled' , 'my-wp' ),
        'message' => sprintf( __( 'Post scheduled for: %s.' ), '<strong>' . __( 'Schedule Date' , 'my-wp' ) . '</strong>' ) . __( 'Schedule Link' , 'my-wp' ),
      ),
      10 => array(
        'title' => __( 'Post draft updated' , 'my-wp' ),
        'message' => __( 'Post draft updated.' ) . __( 'Preview Post Link' , 'my-wp' ),
      ),
    );

    return $update_messages_default;

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

    add_action( 'load-post.php' , array( __CLASS__ , 'load_post' ) , 1000 );
    add_action( 'load-post-new.php' , array( __CLASS__ , 'load_post' ) , 1000 );

  }

  public static function mywp_model_get_option_key( $option_key ) {

    if( empty( self::$post_type ) ) {

      return $option_key;

    }

    $option_key .= '_' . self::$post_type;

    return $option_key;

  }

  public static function load_post() {

    global $typenow;

    if( empty( $typenow ) ) {

      return false;

    }

    self::$post_type = $typenow;

    add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    add_filter( 'post_updated_messages' , array( __CLASS__ , 'change_post_updated_messages' ) );

    add_filter( 'use_block_editor_for_post_type' , array( __CLASS__ , 'change_editor' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_add_new' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_title' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_permalink' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_change_permalink' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_content' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_publish_metabox_draft' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_publish_metabox_preview' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_publish_metabox_change_post_status' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_publish_metabox_change_publish_status' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_publish_metabox_change_publish_on' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'forced_editor_buttons' ) );

    add_action( 'admin_head' , array( __CLASS__ , 'hide_meta_boxes' ) );

    add_action( 'admin_head' , array( __CLASS__ , 'hide_block_editor_meta_boxes' ) );

    add_action( 'in_admin_header' , array( __CLASS__ , 'remove_meta_boxes' ) );

    add_action( 'in_admin_header' , array( __CLASS__ , 'change_title_meta_boxes' ) );

    add_filter( 'wp_default_editor' , array( __CLASS__ , 'forced_editor' ) );

    add_filter( 'enter_title_here' , array( __CLASS__ , 'change_title_placeholder' ) );

    add_action( 'admin_print_footer_scripts' , array( __CLASS__ , 'prevent_meta_boxes' ) );

    add_action( 'admin_print_footer_scripts' , array( __CLASS__ , 'auto_change_title' ) );

  }

  public static function change_post_updated_messages( $post_updated_messages ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $post_updated_messages;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['post_updated_messages'] ) ) {

      return $post_updated_messages;

    }

    $post_updated_messages_default = self::get_update_messages_default();

    foreach( $post_updated_messages_default as $key => $v ) {

      if( ! empty( $setting_data['post_updated_messages'][ $key ] ) ) {

        $post_updated_messages[ self::$post_type ][ $key ] = $setting_data['post_updated_messages'][ $key ];

      }

    }

    self::after_do_function( __FUNCTION__ );

    return $post_updated_messages;

  }

  public static function change_editor( $is_use_block_editor ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    if( empty( $is_use_block_editor ) ) {

      return $is_use_block_editor;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['use_classic_editor'] ) ) {

      return $is_use_block_editor;

    }

    $is_use_block_editor =  false;

    self::after_do_function( __FUNCTION__ );

    return $is_use_block_editor;

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

    echo '.wrap h1 > a { display: none; }';
    echo '.wrap > a.page-title-action { display: none; }';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_title() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_title'] ) ) {

      return false;

    }

    echo '<style>';

    echo '#post-body-content #titlewrap { display: none; }';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_permalink() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_permalink'] ) ) {

      return false;

    }

    echo '<style>';

    echo '#post-body-content #titlediv .inside { display: none; }';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_change_permalink() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_change_permalink'] ) ) {

      return false;

    }

    echo '<style>';

    echo '#post-body-content #change-permalinks { display: none; }';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_content() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_content'] ) ) {

      return false;

    }

    echo '<style>';

    echo '#post-body-content #postdivrich { display: none; }';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_publish_metabox_draft() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_publish_metabox_draft'] ) ) {

      return false;

    }

    echo '<style>';

    echo '#minor-publishing-actions #save-action { display: none; }';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_publish_metabox_preview() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_publish_metabox_preview'] ) ) {

      return false;

    }

    echo '<style>';

    echo '#minor-publishing-actions #preview-action { display: none; }';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_publish_metabox_change_post_status() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_publish_metabox_change_post_status'] ) ) {

      return false;

    }

    echo '<style>';

    echo '#misc-publishing-actions .misc-pub-section.misc-pub-post-status { display: none; }';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_publish_metabox_change_publish_status() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_publish_metabox_change_publish_status'] ) ) {

      return false;

    }

    echo '<style>';

    echo '#misc-publishing-actions .misc-pub-section.misc-pub-visibility { display: none; }';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_publish_metabox_change_publish_on() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_publish_metabox_change_publish_on'] ) ) {

      return false;

    }

    echo '<style>';

    echo '#misc-publishing-actions .misc-pub-section.misc-pub-curtime { display: none; }';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function forced_editor_buttons() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['forced_editor'] ) ) {

      return false;

    }

    echo '<style>';

    echo '#wp-content-editor-tools .wp-editor-tabs { display: none; }';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_meta_boxes() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['meta_boxes'] ) ) {

      return false;

    }

    $hide_meta_boxes = array();

    foreach( $setting_data['meta_boxes'] as $meta_box_id => $meta_box_setting ) {

      if( $meta_box_setting['action'] !== 'hide' ) {

        continue;

      }

      $hide_meta_boxes[] = $meta_box_id;

    }

    if( empty( $hide_meta_boxes ) ) {

      return false;

    }

    echo '<style>';

    foreach( $hide_meta_boxes as $meta_box_id ) {

      printf( '.postbox#%s { height: 0; overflow: hidden; margin: 0; box-shadow: none; border: 0 none; }' , $meta_box_id );

    }

    echo '</style>';

    echo '<script>jQuery(document).ready(function($){';

    foreach( $hide_meta_boxes as $meta_box_id ) {

      printf( '$("#screen-options-wrap .metabox-prefs label[for=%s-hide]").css("display", "none");' , $meta_box_id );

    }

    echo '});</script>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_block_editor_meta_boxes() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['block_editor_meta_boxes'] ) ) {

      return false;

    }

    $current_meta_boxes = MywpSettingMetaBox::get_block_editor_meta_boxes();

    if( empty( $current_meta_boxes ) ) {

      return false;

    }

    $hide_meta_boxes_num = array();

    foreach( $setting_data['block_editor_meta_boxes'] as $block_editor_meta_box_id => $block_editor_meta_box_setting ) {

      if( $block_editor_meta_box_setting['action'] !== 'hide' ) {

        continue;

      }

      if( empty( $current_meta_boxes[ $block_editor_meta_box_id ]['num'] ) ) {

        continue;

      }

      $hide_meta_boxes_num[] = $current_meta_boxes[ $block_editor_meta_box_id ]['num'];

    }

    if( empty( $hide_meta_boxes_num ) ) {

      return false;

    }

    echo '<style>';

    foreach( $hide_meta_boxes_num as $hide_meta_boxes_num ) {

      printf( '.components-panel .components-panel__body:nth-child(%d) { display: none; }' , $hide_meta_boxes_num );

    }

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function remove_meta_boxes() {

    global $wp_meta_boxes;

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['meta_boxes'] ) ) {

      return false;

    }

    $remove_meta_boxes = array();

    foreach( $setting_data['meta_boxes'] as $meta_box_id => $meta_box_setting ) {

      if( $meta_box_setting['action'] !== 'remove' ) {

        continue;

      }

      $remove_meta_boxes[] = $meta_box_id;

    }

    if( empty( $remove_meta_boxes ) ) {

      return false;

    }

    if( empty( $wp_meta_boxes[ self::$post_type ] ) ) {

      return false;

    }

    $current_meta_boxes = $wp_meta_boxes[ self::$post_type ];

    foreach( $current_meta_boxes as $context => $priority_meta_boxes ) {

      if( empty( $priority_meta_boxes ) or ! is_array( $priority_meta_boxes ) ) {

        continue;

      }

      foreach( $priority_meta_boxes as $priority => $meta_boxes ) {

        if( empty( $meta_boxes ) or ! is_array( $meta_boxes ) ) {

          continue;

        }

        foreach( $meta_boxes as $meta_box_id => $meta_box ) {

          if( empty( $meta_box ) or ! is_array( $meta_box ) ) {

            continue;

          }

          if( ! in_array( $meta_box_id , $remove_meta_boxes ) ) {

            continue;

          }

          remove_meta_box( $meta_box_id , self::$post_type , $context );

        }

      }

    }

    self::after_do_function( __FUNCTION__ );

  }

  public static function change_title_meta_boxes() {

    global $wp_meta_boxes;

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['meta_boxes'] ) ) {

      return false;

    }

    $change_title_meta_boxes = array();

    foreach( $setting_data['meta_boxes'] as $meta_box_id => $meta_box_setting ) {

      if( ! empty( $meta_box_setting['action'] ) ) {

        continue;

      }

      if( empty( $meta_box_setting['title'] ) ) {

        continue;

      }

      $change_title_meta_boxes[ $meta_box_id ] = $meta_box_setting['title'];

    }

    if( empty( $change_title_meta_boxes ) ) {

      return false;

    }

    if( empty( $wp_meta_boxes[ self::$post_type ] ) ) {

      return false;

    }

    $current_meta_boxes = $wp_meta_boxes[ self::$post_type ];

    foreach( $current_meta_boxes as $context => $priority_meta_boxes ) {

      if( empty( $priority_meta_boxes ) or ! is_array( $priority_meta_boxes ) ) {

        continue;

      }

      foreach( $priority_meta_boxes as $priority => $meta_boxes ) {

        if( empty( $meta_boxes ) or ! is_array( $meta_boxes ) ) {

          continue;

        }

        foreach( $meta_boxes as $meta_box_id => $meta_box ) {

          if( empty( $meta_box ) or ! is_array( $meta_box ) ) {

            continue;

          }

          if( empty( $change_title_meta_boxes[ $meta_box_id ] ) ) {

            continue;

          }

          $wp_meta_boxes[ self::$post_type ][ $context ][ $priority ][ $meta_box_id ]['title'] = do_shortcode( $change_title_meta_boxes[ $meta_box_id ] );

        }

      }

    }

    self::after_do_function( __FUNCTION__ );

  }

  public static function forced_editor( $wp_default_editor ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $wp_default_editor;

    }

    $setting_data = self::get_setting_data();

    if( ! empty( $setting_data['forced_editor'] ) or in_array( $setting_data['forced_editor'] , array( 'text' , 'tinymce' ) ) ) {

      $wp_default_editor = $setting_data['forced_editor'];

    }

    self::after_do_function( __FUNCTION__ );

    return $wp_default_editor;

  }

  public static function change_title_placeholder( $title_placeholder ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $title_placeholder;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['change_title_placeholder'] ) ) {

      return $title_placeholder;

    }

    $title_placeholder = $setting_data['change_title_placeholder'];

    self::after_do_function( __FUNCTION__ );

    return $title_placeholder;

  }

  public static function prevent_meta_boxes() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['prevent_meta_box'] ) ) {

      return false;

    }

    echo '<script>jQuery(document).ready(function($){';

    echo '$(".meta-box-sortables").sortable("disable");';

    echo '});</script>';

    echo '<style>';

    echo '.js .postbox .hndle { cursor: pointer; }';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function auto_change_title() {

    global $post;

    if( empty( $post ) or empty( $post->ID ) ) {

      return false;

    }

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['auto_change_title'] ) ) {

      return false;

    }

    echo '<script>jQuery(document).ready(function($){';

    echo '$("#titlediv #titlewrap #title").val( ' . $post->ID . ');';

    echo '});</script>';

    self::after_do_function( __FUNCTION__ );

  }

}

MywpControllerModuleAdminPostEdit::init();

endif;
