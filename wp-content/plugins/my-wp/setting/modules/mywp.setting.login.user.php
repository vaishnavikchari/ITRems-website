<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenLoginUser' ) ) :

final class MywpSettingScreenLoginUser extends MywpAbstractSettingModule {

  static protected $id = 'login_user';

  static protected $priority = 20;

  static private $menu = 'login';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'User' ),
      'menu' => self::$menu,
      'controller' => 'login_user',
      'document_url' => self::get_document_url( 'document/login-user/' ),
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Forced Redirect' , 'my-wp' ); ?></h3>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Logged in redirect URL' , 'my-wp' ); ?></th>
          <td>
            <input type="text" name="mywp[data][login_redirect_url]" class="login_redirect_url large-text" value="<?php echo esc_attr( $setting_data['login_redirect_url'] ); ?>" placeholder="<?php echo esc_attr( '[mywp_url admin="1"]' ); ?>" />
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Logged out redirect URL' , 'my-wp' ); ?></th>
          <td>
            <input type="text" name="mywp[data][logout_redirect_url]" class="logout_redirect_url large-text" value="<?php echo esc_attr( $setting_data['logout_redirect_url'] ); ?>" placeholder="<?php echo esc_attr( '[mywp_url login="1"]' ); ?>" />
          </td>
        </tr>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_current_setting_post_data_format_update( $formatted_data ) {

    $mywp_model = self::get_model();

    if( empty( $mywp_model ) ) {

      return $formatted_data;

    }

    $new_formatted_data = $mywp_model->get_initial_data();

    $new_formatted_data['advance'] = $formatted_data['advance'];

    if( ! empty( $formatted_data['login_redirect_url'] ) ) {

      $new_formatted_data['login_redirect_url'] = wp_unslash( $formatted_data['login_redirect_url'] );

    }

    if( ! empty( $formatted_data['logout_redirect_url'] ) ) {

      $new_formatted_data['logout_redirect_url'] = wp_unslash( $formatted_data['logout_redirect_url'] );

    }

    return $new_formatted_data;

  }

}

MywpSettingScreenLoginUser::init();

endif;
