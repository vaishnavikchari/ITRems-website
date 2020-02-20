<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenDebugCrons' ) ) :

final class MywpSettingScreenDebugCrons extends MywpAbstractSettingModule {

  static protected $id = 'debug_crons';

  static protected $priority = 60;

  static private $menu = 'debug';

  public static function mywp_setting_screens( $setting_screens ) {

    $setting_screens[ self::$id ] = array(
      'title' => __( 'All Crons' , 'my-wp' ),
      'menu' => self::$menu,
      'use_form' => false,
    );

    return $setting_screens;

  }

  private static function get_crons() {

    $crons = _get_cron_array();

    return $crons;

  }

  public static function mywp_current_setting_screen_content() {

    $all_crons = self::get_crons();

    if( empty( $all_crons ) ) {

      return false;

    }

    ?>
    <p><?php _e( 'Count' ); ?>: <?php echo count( $all_crons ); ?></p>
    <table class="form-table">
      <tbody>
        <?php foreach( $all_crons as $timestamp => $cron ) : ?>
          <tr>
            <th>
              [<?php echo date( 'Y-m-d H:i:s' , $timestamp + ( get_option( "gmt_offset" ) * HOUR_IN_SECONDS ) ); ?>]<br />
              (<?php echo date( 'Y-m-d H:i:s' , $timestamp ); ?>)<br />
              <?php echo key( $cron ); ?>
            </th>
            <td>
              <textarea readonly="readonly" class="large-text" style="height: 160px;"><?php print_r( $cron ); ?></textarea>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

}

MywpSettingScreenDebugCrons::init();

endif;
