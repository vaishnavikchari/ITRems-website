<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenFrontendAuthorArchive' ) ) :

final class MywpSettingScreenFrontendAuthorArchive extends MywpAbstractSettingModule {

  static protected $id = 'frontend_author_archive';

  static protected $priority = 20;

  static private $menu = 'frontend';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'Author Archive' , 'my-wp' ),
      'menu' => self::$menu,
      'controller' => 'frontend_author_archive',
      'document_url' => self::get_document_url( 'document/frontend-author-archive/' ),
    );

    return $setting_screens;

  }

  public static function mywp_current_setting_screen_content() {

    $setting_data = self::get_setting_data();

    $mywp_user = new MywpUser();

    $author_name = $mywp_user->get_name();

    $args = array( 'post_status' => 'publish' , 'post_type' => 'any' , 'order' => 'DESC' , 'orderby' => 'post_date' , 'numberposts' => 1 );

    $posts = $mywp_user->get_posts();

    $author_post = false;

    if( ! empty( $posts[0] ) ) {

      $author_post = $posts[0];

    }

    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Author Archive' , 'my-wp' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][disable_archive]" class="disable_archive" value="1" <?php checked( $setting_data['disable_archive'] , true ); ?> />
              <?php _e( 'Disable' , 'my-wp' ); ?>
            </label>
            <?php if( ! empty( $author_post ) ) : ?>
              <code><a target="_blank" href="<?php echo get_author_posts_url( $author_post->post_author ); ?>"><?php printf( __( 'Author: %s' ) , $author_name ); ?></a></code>
            <?php endif; ?>
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

    if( ! empty( $formatted_data['disable_archive'] ) ) {

      $new_formatted_data['disable_archive'] = true;

    }

    return $new_formatted_data;

  }

}

MywpSettingScreenFrontendAuthorArchive::init();

endif;
