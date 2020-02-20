<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugTranslations' ) ) :

final class MywpSettingScreenDebugTranslations extends MywpAbstractSettingModule {

  static protected $id = 'debug_translations';

  static protected $priority = 40;

  static private $menu = 'debug';

  protected static function after_init() {

    $id = 'network_' . self::$id;

    add_action( "mywp_setting_screen_content_{$id}" , array( __CLASS__ , 'mywp_current_setting_screen_content' ) );

  }

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'All Translations' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    if( is_multisite() ) {

      $setting_screens[ 'network_' . self::$id ] = array(
        'title' => __( 'All Translations' , 'my-wp' ),
        'menu' => 'network_' . self::$menu,
        'use_form' => false,
      );

    }

    return $setting_screens;

  }

  private static function get_translations() {

    global $l10n;

    return $l10n;

  }

  public static function mywp_current_setting_screen_content() {

    $all_translations = self::get_translations();

    if( empty( $all_translations ) ) {

      return false;

    }

    ?>
    <p><?php _e( 'Count' ); ?>: <?php echo count( $all_translations ); ?></p>
    <table class="form-table">
      <tbody>
        <?php foreach( $all_translations as $translation_domain => $translation_object ) : ?>
          <tr>
            <th>
              <?php echo $translation_domain; ?><br />
            </th>
            <td>
              <p><code><?php echo $translation_object->get_filename(); ?></code></p>
              <textarea readonly="readonly" class="large-text" style="height: 400px;"><?php print_r( $translation_object->entries ); ?></textarea>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugTranslations::init();

endif;
