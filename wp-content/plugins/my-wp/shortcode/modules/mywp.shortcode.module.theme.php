<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpShortcodeAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpShortcodeModuleTheme' ) ) :

final class MywpShortcodeModuleTheme extends MywpShortcodeAbstractModule {

  protected static $id = 'mywp_theme';

  public static function do_shortcode( $atts , $content = false , $tag ) {

    if( empty( $atts['field'] ) ) {

      MywpHelper::error_not_found_message( '$atts["field"]' , sprintf( '[%s] shortcode' , self::$id ) );

      return $content;

    }

    $field = strip_tags( $atts['field'] );

    $theme_id = false;

    if( ! empty( $atts['parent'] ) ) {

      $theme_id = get_template();

    } else {

      $theme_id = get_stylesheet();

    }

    if( empty( $theme_id ) ) {

      MywpHelper::error_not_found_message( '$atts["theme"]' , sprintf( '[%s] shortcode' , self::$id ) );

      return $content;

    }

    $theme = wp_get_theme( $theme_id );

    if( $field === 'url' ) {

      if( ! empty( $atts['parent'] ) ) {

        $content = get_template_directory_uri();

      } else {

        $content = get_stylesheet_directory_uri();

      }

    } elseif( $field === 'path' ) {

      if( ! empty( $atts['parent'] ) ) {

        $content = get_template_directory();

      } else {

        $content = get_stylesheet_directory();

      }

    } elseif( $field === 'name' ) {

      $content = $theme->get( 'Name' );

    } else {

      MywpHelper::error_not_found_message( '$atts["field"]' , sprintf( '[%s] shortcode' , self::$id ) );

      return $content;

    }

    $content = apply_filters( 'mywp_shortcode_theme' , $content , $atts );

    return $content;

  }

}

MywpShortcodeModuleTheme::init();

endif;
