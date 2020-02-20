<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenFrontendGeneral' ) ) :

final class MywpSettingScreenFrontendGeneral extends MywpAbstractSettingModule {

  static protected $id = 'frontend_general';

  static private $menu = 'frontend';

  protected static function after_init() {

    $screen_id = self::$id;

    add_action( "mywp_setting_screen_content_{$screen_id}" , array( __CLASS__ , 'mywp_setting_screen_content_20' ) , 20 );

  }

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'General' ),
      'menu' => self::$menu,
      'controller' => 'frontend_general',
      'use_advance' => true,
      'document_url' => self::get_document_url( 'document/frontend-general/' ),
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    $admin_bar_vars = array(
      'hide' => __( 'Hide' ),
      'show' => __( 'Always Show' , 'my-wp' ),
    );

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'General' ); ?></h3>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Toolbar' , 'my-wp' ); ?></th>
          <td>
            <select name="mywp[data][admin_bar]" class="admin_bar">
              <option value="">----</option>
              <?php foreach( $admin_bar_vars as $key => $val ) : ?>
                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key , $setting_data['admin_bar'] ); ?>><?php echo esc_attr( $val ); ?></option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_setting_screen_content_20() {

    $setting_data = self::get_setting_data();

    $api_root = get_rest_url();

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Header Meta' , 'my-wp' ); ?></h3>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Hide WP Generator Tag' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_wp_generator]" class="hide_wp_generator" value="1" <?php checked( $setting_data['hide_wp_generator'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
            <p><code>
              <?php echo esc_html( get_the_generator( apply_filters( 'wp_generator_type', 'xhtml' ) ) ); ?>
            </code></p>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Hide Manifest Link Tag' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_wlwmanifest_link]" class="hide_wlwmanifest_link" value="1" <?php checked( $setting_data['hide_wlwmanifest_link'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
            <p><code>
              <?php echo esc_html( '<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="' . includes_url( 'wlwmanifest.xml' ) . '" />' ); ?>
            </code></p>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Hide RSD Link Tag' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_rsd_link]" class="hide_rsd_link" value="1" <?php checked( $setting_data['hide_rsd_link'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
            <p><code>
              <?php echo esc_html( '<link rel="EditURI" type="application/rsd+xml" title="RSD" href="' . esc_url( site_url( 'xmlrpc.php?rsd', 'rpc' ) ) . '" />' ); ?>
            </code></p>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Hide Feed Links Tag' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_feed_links]" class="hide_feed_links" value="1" <?php checked( $setting_data['hide_feed_links'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
            <p><code>
              <?php echo esc_html( '<link rel="alternate" type="' . feed_content_type() . '" title="[Feed Title]" href="[Feed URL]"/>' ); ?>
            </code></p>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Hide Feed Links Extra Tag' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_feed_links_extra]" class="hide_feed_links_extra" value="1" <?php checked( $setting_data['hide_feed_links_extra'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
            <p><code>
              <?php echo esc_html( '<link rel="alternate" type="' . feed_content_type() . '" title="[Feed Content Type Title" href="Feed Content Type URL" />' ); ?>
            </code></p>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Hide Rest link in Header' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_rest_link_header]" class="hide_rest_link_header" value="1" <?php checked( $setting_data['hide_rest_link_header'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
            <p><code>
              <?php if( ! empty( $api_root ) ) : ?>
                <?php echo esc_html( 'Link: <' . esc_url_raw( $api_root ) . '>; rel="https://api.w.org/"' ); ?>
              <?php endif; ?>
            </code></p>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Hide Shortlink in Header' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_shortlink_header]" class="hide_shortlink_header" value="1" <?php checked( $setting_data['hide_shortlink_header'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
            <p><code>
              <?php if( ! empty( $api_root ) ) : ?>
                <?php echo esc_html( 'Link: <' . home_url( '/' ) . '>?p=***; rel=shortlink' ); ?>
              <?php endif; ?>
            </code></p>
          </td>
        </tr>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_current_setting_screen_advance_content() {

    $setting_data = self::get_setting_data();

    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Include your CSS file' , 'my-wp' ); ?></th>
          <td>
            <input type="text" name="mywp[data][include_css_file]" class="include_css_file large-text" value="<?php echo esc_attr( $setting_data['include_css_file'] ); ?>" placeholder="<?php echo esc_attr( 'http://example.com/frontend.css' ); ?>" />
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Include your JS file' , 'my-wp' ); ?></th>
          <td>
            <input type="text" name="mywp[data][include_js_file]" class="include_js_file large-text" value="<?php echo esc_attr( $setting_data['include_js_file'] ); ?>" placeholder="<?php echo esc_attr( 'http://example.com/frontend.js' ); ?>" />
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Custom Header Meta' , 'my-wp' ); ?></th>
          <td>
            <textarea type="text" name="mywp[data][custom_header_meta]" class="custom_header_meta large-text"><?php echo esc_textarea( $setting_data['custom_header_meta'] ); ?></textarea>
          </td>
        </tr>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_current_admin_print_styles() {

    ?>
<style>
.custom_header_meta {
  height: 200px;
}
</style>
    <?php

  }

  public static function mywp_current_setting_post_data_format_update( $formatted_data ) {

    $mywp_model = self::get_model();

    if( empty( $mywp_model ) ) {

      return $formatted_data;

    }

    $new_formatted_data = $mywp_model->get_initial_data();

    $new_formatted_data['advance'] = $formatted_data['advance'];

    if( ! empty( $formatted_data['admin_bar'] ) ) {

      $new_formatted_data['admin_bar'] = strip_tags( $formatted_data['admin_bar'] );

    }

    if( ! empty( $formatted_data['hide_wp_generator'] ) ) {

      $new_formatted_data['hide_wp_generator'] = true;

    }

    if( ! empty( $formatted_data['hide_wlwmanifest_link'] ) ) {

      $new_formatted_data['hide_wlwmanifest_link'] = true;

    }

    if( ! empty( $formatted_data['hide_rsd_link'] ) ) {

      $new_formatted_data['hide_rsd_link'] = true;

    }

    if( ! empty( $formatted_data['hide_feed_links'] ) ) {

      $new_formatted_data['hide_feed_links'] = true;

    }

    if( ! empty( $formatted_data['hide_feed_links_extra'] ) ) {

      $new_formatted_data['hide_feed_links_extra'] = true;

    }

    if( ! empty( $formatted_data['hide_rest_link_header'] ) ) {

      $new_formatted_data['hide_rest_link_header'] = true;

    }

    if( ! empty( $formatted_data['hide_shortlink_header'] ) ) {

      $new_formatted_data['hide_shortlink_header'] = true;

    }

    if( ! empty( $formatted_data['include_css_file'] ) ) {

      $new_formatted_data['include_css_file'] = wp_unslash( $formatted_data['include_css_file'] );

    }

    if( ! empty( $formatted_data['include_js_file'] ) ) {

      $new_formatted_data['include_js_file'] = wp_unslash( $formatted_data['include_js_file'] );

    }

    if( ! empty( $formatted_data['custom_header_meta'] ) ) {

      $new_formatted_data['custom_header_meta'] = wp_unslash( $formatted_data['custom_header_meta'] );

    }

    return $new_formatted_data;

  }

}

MywpSettingScreenFrontendGeneral::init();

endif;
