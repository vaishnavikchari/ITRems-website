<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenLoginGeneral' ) ) :

final class MywpSettingScreenLoginGeneral extends MywpAbstractSettingModule {

  static protected $id = 'login_general';

  static private $menu = 'login';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'General' ),
      'menu' => self::$menu,
      'controller' => 'login_general',
      'use_advance' => true,
      'document_url' => self::get_document_url( 'document/login-general/' ),
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Login Form' , 'my-wp' ); ?></h3>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Logo Link URL' , 'my-wp' ); ?></th>
          <td>
            <input type="text" name="mywp[data][logo_link_url]" class="logo_link_url large-text" value="<?php echo esc_attr( $setting_data['logo_link_url'] ); ?>" placeholder="<?php echo esc_attr( 'http://example.com' ); ?>" />
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Logo Image URL' , 'my-wp' ); ?></th>
          <td>
            <input type="text" name="mywp[data][logo_image_path]" class="logo_image_path large-text" value="<?php echo esc_attr( $setting_data['logo_image_path'] ); ?>" placeholder="<?php echo esc_attr( 'http://example.com/logo.png' ); ?>" />
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Title' ); ?></th>
          <td>
            <input type="text" name="mywp[data][logo_title]" class="logo_title large-text" value="<?php echo esc_attr( $setting_data['logo_title'] ); ?>" placeholder="<?php echo esc_attr( get_option( 'blogname' ) ); ?>" />
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
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Custom' , 'my-wp' ); ?></h3>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Input CSS' , 'my-wp' ); ?></th>
          <td>
            <textarea type="text" name="mywp[data][input_css]" class="input_css large-text" placeholder="<?php echo esc_attr( 'body.login{ background-color: #fff; }' ); ?>"><?php echo esc_attr( $setting_data['input_css'] ); ?></textarea>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Include your CSS file' , 'my-wp' ); ?></th>
          <td>
            <input type="text" name="mywp[data][include_css_file]" class="include_css_file large-text" value="<?php echo esc_attr( $setting_data['include_css_file'] ); ?>" placeholder="<?php echo esc_attr( 'http://example.com/login.css' ); ?>" />
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Custom Footer Text' , 'my-wp' ); ?></th>
          <td>
            <?php wp_editor( $setting_data['custom_footer_text'] , 'custom_footer_text' , array( 'textarea_name' => 'mywp[data][custom_footer_text]' , 'textarea_rows' => 5 ) ); ?>
          </td>
        </tr>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_current_admin_print_footer_scripts() {

?>
<style>
.input_css {
  height: 300px;
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

    if( ! empty( $formatted_data['logo_link_url'] ) ) {

      $new_formatted_data['logo_link_url'] = wp_unslash( $formatted_data['logo_link_url'] );

    }

    if( ! empty( $formatted_data['logo_image_path'] ) ) {

      $new_formatted_data['logo_image_path'] = wp_unslash( $formatted_data['logo_image_path'] );

    }

    if( ! empty( $formatted_data['logo_title'] ) ) {

      $new_formatted_data['logo_title'] = wp_unslash( strip_tags( $formatted_data['logo_title'] ) );

    }

    if( ! empty( $formatted_data['input_css'] ) ) {

      $new_formatted_data['input_css'] = wp_unslash( strip_tags( $formatted_data['input_css'] ) );

    }

    if( ! empty( $formatted_data['include_css_file'] ) ) {

      $new_formatted_data['include_css_file'] = wp_unslash( $formatted_data['include_css_file'] );

    }

    if( ! empty( $formatted_data['custom_footer_text'] ) ) {

      $new_formatted_data['custom_footer_text'] = wp_unslash( $formatted_data['custom_footer_text'] );

    }

    return $new_formatted_data;

  }

}

MywpSettingScreenLoginGeneral::init();

endif;
