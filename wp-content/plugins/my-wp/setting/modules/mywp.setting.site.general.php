<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenSiteGeneral' ) ) :

final class MywpSettingScreenSiteGeneral extends MywpAbstractSettingModule {

  static protected $id = 'site_general';

  static protected $priority = 90;

  static private $menu = 'site';

  public static function mywp_setting_screens( $setting_screens ) {

    if( is_multisite() ) {

      $setting_screens[ self::$id ] = array(
        'title' => __( 'Site General' , 'my-wp' ),
        'menu' => 'network',
        'controller' => 'site_general',
      );

    } else {

      $setting_screens[ self::$id ] = array(
        'title' => __( 'Site General' , 'my-wp' ),
        'menu' => self::$menu,
        'controller' => 'site_general',
      );

    }

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Disable File Edit' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][disable_file_edit]" class="disable_file_edit" value="1" <?php checked( $setting_data['disable_file_edit'] , true ); ?> />
              <?php _e( 'Disable' , 'my-wp' ); ?>
            </label>
          </td>
        </tr>
        <?php if( is_multisite() ) : ?>
          <tr>
            <th><?php _e( 'Disable User Admin' , 'my-wp' ); ?></th>
            <td>
              <label>
                <input type="checkbox" name="mywp[data][disable_user_admin]" class="disable_user_admin" value="1" <?php checked( $setting_data['disable_user_admin'] , true ); ?> />
                <?php _e( 'Disable' , 'my-wp' ); ?>
              </label>
              <a href="<?php echo esc_url( user_admin_url() ); ?>" target="_blank"><?php echo user_admin_url(); ?></a>
            </td>
          </tr>
        <?php endif; ?>
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

    if( ! empty( $formatted_data['disable_file_edit'] ) ) {

      $new_formatted_data['disable_file_edit'] = true;

    }

    if( ! empty( $formatted_data['disable_user_admin'] ) ) {

      $new_formatted_data['disable_user_admin'] = true;

    }

    return $new_formatted_data;

  }

}

MywpSettingScreenSiteGeneral::init();

endif;
