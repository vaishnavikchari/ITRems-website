<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpSetting' ) ) {
  return false;
}

if( ! MywpApi::is_manager() ) {
  return false;
}

$current_meta_box_screen_id = MywpSettingMetaBox::get_current_meta_box_screen_id();

$current_meta_boxes = MywpSettingMetaBox::get_block_editor_meta_boxes();

$current_meta_box_setting_data = array();

foreach( $current_meta_boxes as $meta_box_id => $meta_box ) {

  $current_meta_box_setting_data[ $meta_box_id ] = array( 'action' => '' );

}

$setting_data = MywpSetting::get_setting_data( 'admin_post_edit' );

if( ! empty( $setting_data['block_editor_meta_boxes'] ) ) {

  foreach( $current_meta_boxes as $meta_box_id => $meta_box ) {

    if( ! empty( $setting_data['block_editor_meta_boxes'][ $meta_box_id]['action'] ) ) {

      $current_meta_box_setting_data[ $meta_box_id ]['action'] = $setting_data['block_editor_meta_boxes'][ $meta_box_id]['action'];

    }

  }

}

?>

<div id="setting-screen-management-meta-boxes">

  <ul id="meta-box-bulk-actions">
    <li><a href="javascript:void(0);" class="button button-secondary" id="meta-box-bulk-action-show">
      <?php _e( 'All Show' , 'my-wp' ); ?>
    </a></li>
    <li><a href="javascript:void(0);" class="button button-secondary" id="meta-box-bulk-action-hide">
      <?php _e( 'All Hide' , 'my-wp' ); ?>
    </a></li>
  </ul>

  <table class="form-table" id="meta-boxes-table">
    <thead>
      <tr>
        <th></th>
        <th><?php _e( 'Hide' ); ?></th>
      </tr>
    </thead>
    <tbody>

      <?php foreach( $current_meta_boxes as $meta_box_id => $meta_box ) : ?>

        <?php $action = false; ?>

        <?php if( ! empty( $current_meta_box_setting_data[ $meta_box_id ]['action'] ) ) : ?>

          <?php $action = $current_meta_box_setting_data[ $meta_box_id ]['action']; ?>

        <?php endif; ?>

        <tr class="meta-box-tr">
          <th><?php echo $meta_box['num']; ?>: <?php echo $meta_box['title']; ?></th>
          <td>
            <select name="mywp[data][block_editor_meta_boxes][<?php echo esc_attr( $meta_box_id ); ?>][action]" class="meta-box-action-select">
              <option value="" <?php selected( $action , '' ); ?>></option>
              <option value="hide" <?php selected( $action , 'hide' ); ?>><?php _e( 'Hide' ); ?></option>
            </select>
          </td>
        </tr>

      <?php endforeach; ?>

    </tbody>
  </table>

</div>
