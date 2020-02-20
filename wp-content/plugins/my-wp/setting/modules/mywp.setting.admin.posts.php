<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenAdminPosts' ) ) :

final class MywpSettingScreenAdminPosts extends MywpAbstractSettingColumnsModule {

  static protected $id = 'admin_posts';

  static protected $priority = 50;

  static private $menu = 'admin';

  static private $post_type = '';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'All Posts' ),
      'menu' => self::$menu,
      'controller' => 'admin_posts',
      'use_advance' => true,
      'document_url' => self::get_document_url( 'document/admin-posts/' ),
    );

    return $setting_screens;

  }

  public static function mywp_current_load_setting_screen() {

    $current_setting_post_type_name = MywpSettingPostType::get_current_post_type_id();

    if( ! empty( $current_setting_post_type_name ) ) {

      self::$post_type = $current_setting_post_type_name;

      add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    }

  }

  public static function mywp_model_get_option_key( $option_key ) {

    if( empty( self::$post_type ) ) {

      return $option_key;

    }

    $option_key .= '_' . self::$post_type;

    return $option_key;

  }

  public static function ajax_add_column() {

    $action_name = MywpSetting::get_ajax_action_name( self::$id , 'add_column' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    if( empty( $_POST['add_column_id'] ) ) {

      return false;

    }

    $add_column_id = strip_tags( $_POST['add_column_id'] );

    if( empty( $_POST['setting_post_type'] ) ) {

      return false;

    }

    MywpSettingPostType::set_current_post_type_id( $_POST['setting_post_type'] );

    $current_setting_post_type_name = MywpSettingPostType::get_current_post_type_id();

    static::set_list_column_id();

    $available_list_columns = self::get_available_list_columns();

    if( empty( $available_list_columns ) ) {

      return false;

    }

    $found_column = false;

    foreach( $available_list_columns as $group => $list_columns ) {

      if( empty( $list_columns['columns'] ) ) {

        continue;

      }

      foreach( $list_columns['columns'] as $column_id => $list_column ) {

        if( $column_id === $add_column_id ) {

          $found_column = $list_column;

          break;

        }

      }

    }

    if( empty( $found_column ) ) {

      return false;

    }

    $result_html = '';

    ob_start();

    self::print_item( $found_column );

    $result_html .= ob_get_contents();

    ob_end_clean();

    wp_send_json_success( array( 'result_html' => $result_html ) );

  }

  public static function mywp_current_admin_print_footer_scripts() {

?>
<script>
jQuery(document).ready(function($){

  $('#setting-screen-setting-list-columns #setting-list-columns-available-add-column').on('click', function() {

    var $available_column = $(this).parent();

    var $spinner = $available_column.find('.spinner');

    var select_column_key = $available_column.find('#setting-list-columns-available-select-column').val();

    if( select_column_key == null || select_column_key == '' ) {

      return false;

    }

    var $available_columnm_key = $available_column.find('.available-column.column-key-' + select_column_key);

    if( ! $available_columnm_key.size() ) {

      return false;

    }

    var add_column_id = $available_columnm_key.find('.id').val();

    if( add_column_id == null || add_column_id == '' ) {

      return false;

    }

    var $already_column = false;

    $(document).find('#setting-screen-setting-list-columns #setting-list-columns-setting-columns-items .list-columns-item').each( function( index , el ) {

      var list_column_item_id = $(el).find('.list-column-item-id').val();

      if( list_column_item_id == add_column_id ) {

        $already_column = $(el);

        $(el).addClass('already');

        setTimeout( function() {

          $(el).removeClass('already');

        }, 2000);

        return false;

      }

    });

    if( $already_column != false ) {

      alert( mywp_admin_setting.column_already_added );

      var scroll_position = $already_column.offset().top;

      scroll_position = ( scroll_position - 80 );

      $( 'html,body' ).animate({
        scrollTop: scroll_position
      });

      return false;

    }

    PostData = {
      action: '<?php echo MywpSetting::get_ajax_action_name( self::$id , 'add_column' ); ?>',
      <?php echo MywpSetting::get_ajax_action_name( self::$id , 'add_column' ); ?>: '<?php echo wp_create_nonce( MywpSetting::get_ajax_action_name( self::$id , 'add_column' ) ); ?>',
      setting_post_type: '<?php echo esc_attr( MywpSettingPostType::get_current_post_type_id() ); ?>',
      add_column_id: add_column_id
      <?php do_action( 'mywp_setting_admin_posts_available_column_add_post_data_JS' ); ?>
    };

    $spinner.css('visibility', 'visible');

    $.ajax({
      type: 'post',
      url: ajaxurl,
      data: PostData
    }).done( function( xhr ) {

      if( typeof xhr !== 'object' || xhr.success === undefined ) {

        alert( '<?php _e( 'An error has occurred. Please reload the page and try again.' ); ?>' );

        $spinner.css('visibility', 'hidden');

        return false;

      }

      if( xhr.data.result_html === undefined ) {

        alert( '<?php _e( 'An error has occurred. Please reload the page and try again.' ); ?>' );

        $spinner.css('visibility', 'hidden');

        return false;

      }

      $(document).find('#setting-list-columns-setting-columns-items').append( xhr.data.result_html );

      $(document).find('.list-columns-sortable-items').sortable({
        connectWith: '.list-columns-sortable-items'
      });

      $available_column.find('.spinner').css('visibility', 'hidden');

      var scroll_position = $(document).find('#setting-list-columns-setting-columns-items .list-columns-item:last').offset().top;

      scroll_position = ( scroll_position - 40 );

      $( 'html,body' ).animate({
        scrollTop: scroll_position
      });

    }).fail( function( xhr ) {

      alert( '<?php _e( 'An error has occurred. Please reload the page and try again.' ); ?>' );

      $spinner.css('visibility', 'hidden');

      return false;

    });

  });

});
</script>
<?php

  }

  public static function mywp_current_setting_screen_header() {

    $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

    $current_setting_post_type_id = MywpSettingPostType::get_current_post_type_id();
    $current_setting_post_type = MywpSettingPostType::get_current_post_type();

    if( empty( $current_setting_post_type ) ) {

      MywpHelper::error_not_found_message( '$current_setting_post_type' , $called_text );

      return false;

    }

    MywpApi::include_file( MYWP_PLUGIN_PATH . 'views/elements/setting-screen-select-post-type.php' );

  }

  public static function mywp_current_setting_screen_advance_content() {

    $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

    $setting_data = self::get_setting_data();

    $current_setting_post_type_id = MywpSettingPostType::get_current_post_type_id();
    $current_setting_post_type = MywpSettingPostType::get_current_post_type();

    if( empty( $current_setting_post_type ) ) {

      MywpHelper::error_not_found_message( '$current_setting_post_type' , $called_text );

      return false;

    }

    $bulk_update_messages_default = MywpControllerModuleAdminPosts::get_bulk_update_messages_default();

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Bulk Updated Messages' , 'my-wp' ); ?></h3>
    <table class="form-table">
      <tbody>
        <?php foreach( $bulk_update_messages_default as $bulk_update_message_key => $bulk_update_message ) : ?>
          <?php $val = ''; ?>
          <?php if( ! empty( $setting_data['bulk_post_updated_messages'][ $bulk_update_message_key ] ) ) : ?>
            <?php $val = $setting_data['bulk_post_updated_messages'][ $bulk_update_message_key ]; ?>
          <?php endif; ?>
          <tr>
            <th><?php echo $bulk_update_message_key; ?></th>
            <td>
              <label>
                <input type="text" name="mywp[data][bulk_post_updated_messages][<?php echo esc_attr( $bulk_update_message_key ); ?>]" class="<?php echo esc_attr( $bulk_update_message_key ); ?> large-text" value="<?php echo esc_attr( $val ); ?>" placeholder="<?php echo esc_attr( $bulk_update_message ); ?>" />
              </label>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <p>&nbsp;<p>

    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Other' , 'my-wp' ); ?></h3>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Number of items per page:' ); ?></th>
          <td>
            <label>
              <input type="number" name="mywp[data][per_page_num]" class="per_page_num small-text" value="<?php echo esc_attr( $setting_data['per_page_num'] ); ?>" placeholder="20" />
            </label>
          </td>
        </tr>
        <tr>
          <th><?php echo $current_setting_post_type->labels->add_new; ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_add_new]" class="hide_add_new" value="1" <?php checked( $setting_data['hide_add_new'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th><?php echo $current_setting_post_type->labels->search_items; ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_search_box]" class="hide_search_box" value="1" <?php checked( $setting_data['hide_search_box'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Automatic output of column contents' , 'my-wp' ); ?></th>
          <td>
            <select name="mywp[data][auto_output_column_body]" class="auto_output_column_body">
              <option value="" <?php selected( false , $setting_data['auto_output_column_body'] ); ?>><?php echo esc_attr( __( 'Not automatic output' , 'my-wp' ) ); ?></option>
              <option value="1" <?php selected( true , $setting_data['auto_output_column_body'] ); ?>><?php echo esc_attr( __( 'Automatic output' , 'my-wp' ) ); ?></option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

  protected static function set_list_column_id() {

    self::$list_column_id = MywpSettingPostType::get_current_post_type_id();

  }

  protected static function get_list_link() {

    $called_text = sprintf( '%s::%s()' , __CLASS__ , __FUNCTION__ );

    $current_setting_post_type_id = MywpSettingPostType::get_current_post_type_id();

    if( empty( $current_setting_post_type_id ) ) {

      MywpHelper::error_require_message( '$current_setting_post_type_id' , $called_text );

      return false;

    }

    $list_link = MywpSettingPostType::get_list_link( $current_setting_post_type_id );

    return $list_link;

  }

  protected static function get_core_list_columns() {

    $core_list_columns = array(
      'cb' => array(
        'id' => 'cb',
        'type' => 'core',
        'sort' => false,
        'orderby' => '',
        'default_title' => '<input type="checkbox" />',
        'title' => '<input type="checkbox" />',
        'width' => '2.2em',
      ),
      'title' => array(
        'id' => 'title',
        'type' => 'core',
        'sort' => true,
        'orderby' => 'title',
        'default_title' => __( 'Title' ),
        'title' => __( 'Title' ),
        'width' => '',
      ),
      'author' => array(
        'id' => 'author',
        'type' => 'core',
        'sort' => false,
        'orderby' => 'author',
        'default_title' => __( 'Author' ),
        'title' => __( 'Author' ),
        'width' => '10%',
      ),
      'comments' => array(
        'id' => 'comments',
        'type' => 'core',
        'sort' => true,
        'orderby' => 'comment_count',
        'default_title' => '<span class="vers comment-grey-bubble" title="' . esc_attr__( 'Comments' ) . '"><span class="screen-reader-text">' . esc_attr__( 'Comments' ) . '</span></span>',
        'title' => '<span class="vers comment-grey-bubble" title="' . esc_attr__( 'Comments' ) . '"><span class="screen-reader-text">' . esc_attr__( 'Comments' ) . '</span></span>',
        'width' => '5.5em',
      ),
      'date' => array(
        'id' => 'date',
        'type' => 'core',
        'sort' => true,
        'orderby' => 'date',
        'default_title' => __( 'Date' ),
        'title' => __( 'Date' ),
        'width' => '10%',
      ),
      'slug' => array(
        'id' => 'slug',
        'type' => 'core',
        'sort' => false,
        'orderby' => 'name',
        'default_title' => __( 'Slug' ),
        'title' => __( 'Slug' ),
        'width' => '25%',
      ),
      'excerpt' => array(
        'id' => 'excerpt',
        'type' => 'core',
        'sort' => false,
        'orderby' => 'post_excerpt',
        'default_title' => __( 'Excerpt' ),
        'title' => __( 'Excerpt' ),
        'width' => '100%',
      ),
      'id' => array(
        'id' => 'id',
        'type' => 'core',
        'sort' => false,
        'orderby' => 'ID',
        'default_title' => __( 'ID' , 'my-wp' ),
        'title' => __( 'ID' , 'my-wp' ),
        'width' => '',
      ),
      'parent' => array(
        'id' => 'parent',
        'type' => 'core',
        'sort' => false,
        'orderby' => 'parent',
        'default_title' => __( 'Parent' ),
        'title' => __( 'Parent' ),
        'width' => '10%',
      ),
      'menu_order' => array(
        'id' => 'menu_order',
        'type' => 'core',
        'sort' => true,
        'orderby' => 'menu_order',
        'default_title' => __( 'Order' ),
        'title' => __( 'Order' ),
        'width' => '',
      ),
    );

    if( current_theme_supports( 'post-thumbnails' ) ) {

      $core_list_columns['post-thumbnails'] = array(
        'id' => 'post-thumbnails',
        'type' => 'core',
        'sort' => false,
        'orderby' => 'post-thumbnails',
        'default_title' => __( 'Featured Image' ),
        'title' => __( 'Featured Image' ),
        'width' => '',
      );

    }

    return $core_list_columns;

  }

  public static function current_available_list_columns( $available_list_columns ) {

    $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$available_list_columns' );

    if( empty( $available_list_columns['core']['columns'] ) ) {

      MywpHelper::error_require_message( '$available_list_columns["core"]["columns"]' , $called_text );

      return false;

    }

    $current_setting_post_type_id = MywpSettingPostType::get_current_post_type_id();

    if( empty( $current_setting_post_type_id ) ) {

      MywpHelper::error_require_message( '$current_setting_post_type_id' , $called_text );

      return false;

    }

    $available_list_columns['taxonomies'] = array(
      'title' => __( 'Taxonomies' , 'my-wp' ),
      'columns' => array(),
    );

    $taxonomies = get_taxonomies( array() , 'objects' );

    if( ! empty( $taxonomies ) ) {

      foreach( $taxonomies as $taxonomy ) {

        if( ! in_array( $current_setting_post_type_id , $taxonomy->object_type ) ) {

          continue;

        }

        $available_list_column = array(
          'id' => $taxonomy->name,
          'type' => 'core',
          'sort' => false,
          'orderby' => '',
          'default_title' => $taxonomy->label,
          'title' => $taxonomy->label,
          'width' => '15%',
        );

        $available_list_columns['taxonomies']['columns'][ $taxonomy->name ] = $available_list_column;

      }

    }

    $available_list_columns['other'] = array(
      'title' => __( 'Other' , 'my-wp' ),
      'columns' => array(),
    );

    $core_list_columns = self::get_core_list_columns();

    $default_list_columns = self::get_default_list_columns();

    foreach( $default_list_columns['columns'] as $column_id => $column_title ) {

      if( isset( $core_list_columns[ $column_id ] ) ) {

        continue;

      }

      if( isset( $taxonomies[ $column_id ] ) ) {

        continue;

      }

      $available_list_column = array(
        'id' => $column_id,
        'type' => 'other',
        'sort' => false,
        'orderby' => '',
        'default_title' => $column_title,
        'title' => $column_title,
        'width' => '',
      );

      if( isset( $default_list_columns['sortables'][ $column_id ] ) ) {

        $available_list_column['sort'] = true;

      }

      $available_list_columns['other']['columns'][ $column_id ] = $available_list_column;

    }

    $available_list_columns['custom_fields'] = array(
      'title' => __( 'Custom Fields' ),
      'columns' => array(),
    );

    $all_custom_fields = MywpPostType::get_post_type_posts_all_custom_fields( $current_setting_post_type_id );

    if( ! empty( $all_custom_fields ) ) {

      foreach( $all_custom_fields as $custom_field_name => $v ) {

        $available_list_column = array(
          'id' => $custom_field_name,
          'type' => 'custom_fields',
          'sort' => true,
          'orderby' => $custom_field_name,
          'default_title' => $custom_field_name,
          'title' => $custom_field_name,
          'width' => '',
        );

        $available_list_columns['custom_fields']['columns'][ $custom_field_name ] = $available_list_column;

      }

    }

    return $available_list_columns;

  }

  public static function mywp_current_setting_screen_remove_form() {

    $current_setting_post_type_id = MywpSettingPostType::get_current_post_type_id();

    if( empty( $current_setting_post_type_id ) ) {

      return false;

    }

    ?>

    <input type="hidden" name="mywp[data][post_type]" value="<?php echo esc_attr( $current_setting_post_type_id ); ?>" />

    <?php

  }

  public static function mywp_current_setting_post_data_format_update( $formatted_data ) {

    $mywp_model = self::get_model();

    if( empty( $mywp_model ) ) {

      return $formatted_data;

    }

    $new_formatted_data = $mywp_model->get_initial_data();

    $new_formatted_data['advance'] = $formatted_data['advance'];

    if( ! empty( $formatted_data['post_type'] ) ) {

      $new_formatted_data['post_type'] = strip_tags( $formatted_data['post_type'] );

    }

    if( ! empty( $formatted_data['list_columns'] ) ) {

      foreach( $formatted_data['list_columns'] as $list_column_id => $list_column_setting ) {

        $list_column_id = strip_tags( $list_column_id );

        $new_list_column_setting = array(
          'id' => $list_column_id,
          'sort' => '',
          'orderby' => '',
          'title' => '',
          'width' => '',
        );

        if( ! empty( $list_column_setting['sort'] ) ) {

          $new_list_column_setting['sort'] = true;

        }

        if( ! empty( $list_column_setting['title'] ) ) {

          $new_list_column_setting['title'] = wp_unslash( $list_column_setting['title'] );

        }

        if( ! empty( $list_column_setting['orderby'] ) ) {

          $new_list_column_setting['orderby'] = wp_unslash( $list_column_setting['orderby'] );

        }

        if( ! empty( $list_column_setting['width'] ) ) {

          $new_list_column_setting['width'] = strip_tags( $list_column_setting['width'] );

        }

        $new_formatted_data['list_columns'][ $list_column_id ] = $new_list_column_setting;

      }

    }

    $bulk_update_messages_default = MywpControllerModuleAdminPosts::get_bulk_update_messages_default();

    foreach( $bulk_update_messages_default as $key => $v ) {

      if( ! empty( $formatted_data['bulk_post_updated_messages'][ $key ] ) ) {

        $new_formatted_data['bulk_post_updated_messages'][ $key ] = strip_tags( $formatted_data['bulk_post_updated_messages'][ $key ] );

      }

    }

    if( ! empty( $formatted_data['per_page_num'] ) ) {

      $new_formatted_data['per_page_num'] = intval( $formatted_data['per_page_num'] );

    }

    if( ! empty( $formatted_data['hide_add_new'] ) ) {

      $new_formatted_data['hide_add_new'] = true;

    }

    if( ! empty( $formatted_data['hide_search_box'] ) ) {

      $new_formatted_data['hide_search_box'] = true;

    }

    if( ! empty( $formatted_data['auto_output_column_body'] ) ) {

      $new_formatted_data['auto_output_column_body'] = true;

    }

    return $new_formatted_data;

  }

  public static function mywp_current_setting_post_data_format_remove( $formatted_data ) {

    if( ! empty( $formatted_data['post_type'] ) ) {

      $formatted_data['post_type'] = strip_tags( $formatted_data['post_type'] );

    }

    return $formatted_data;

  }

  public static function mywp_current_setting_post_data_validate_update( $validated_data ) {

    $mywp_notice = new MywpNotice();

    if( empty( $validated_data['post_type'] ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %s is not found data.' ) , 'post_type' ) );

    }

    return $validated_data;

  }

  public static function mywp_current_setting_post_data_validate_remove( $validated_data ) {

    $mywp_notice = new MywpNotice();

    if( empty( $validated_data['post_type'] ) ) {

      $mywp_notice->add_notice_error( sprintf( __( 'The %s is not found data.' ) , 'post_type' ) );

    }

    return $validated_data;

  }

  public static function mywp_current_setting_before_post_data_action_update( $validated_data ) {

    if( ! empty( $validated_data['post_type'] ) ) {

      self::$post_type = $validated_data['post_type'];

      add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

    }

  }

  public static function mywp_current_setting_before_post_data_action_remove( $validated_data ) {

    $called_text = sprintf( '%s::%s( %s )' , get_called_class() , __FUNCTION__ , '$validated_data' );

    if( ! empty( $validated_data['post_type'] ) ) {

      self::$post_type = $validated_data['post_type'];

      add_filter( 'mywp_model_get_option_key_mywp_' . self::$id , array( __CLASS__ , 'mywp_model_get_option_key' ) );

      MywpSettingPostType::set_current_post_type_id( self::$post_type );

      $current_setting_post_type_name = MywpSettingPostType::get_current_post_type_id();

      static::set_list_column_id();

      self::delete_current_list_columns();

    }

  }

}

MywpSettingScreenAdminPosts::init();

endif;
