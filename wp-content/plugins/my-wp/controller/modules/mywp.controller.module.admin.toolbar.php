<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminToolbar' ) ) :

final class MywpControllerModuleAdminToolbar extends MywpControllerAbstractModule {

  static protected $id = 'admin_toolbar';

  static private $toolbar = false;

  static private $toolbar_items = false;

  static private $toolbar_items_added_classes = false;

  static private $child_items = array();

  static private $parent_items = array();

  static private $found_parent_item_ids = array();

  static private $current_url = false;

  static private $find_parent_id = array();

  protected static function after_init() {

    add_filter( 'mywp_controller_admin_toolbar_get_toolbar' , array( __CLASS__ , 'mywp_controller_admin_toolbar_get_toolbar' ) );

  }

  public static function mywp_controller_admin_toolbar_get_toolbar( $posts ) {

    global $wp_admin_bar;

    if( ! is_multisite() ) {

      return $posts;

    }

    if( empty( $posts ) ) {

      return $posts;

    }

    $found_my_sites_post_id = false;
    $found_my_sites_post_key = false;
    $found_my_sites_item_location = false;

    foreach( $posts as $key => $post ) {

      if( $post->item_default_id === 'my-sites' ) {

        $found_my_sites_post_id = $post->ID;
        $found_my_sites_post_key = $key;
        $found_my_sites_item_location = $post->item_location;

      }

    }

    if( ! empty( $found_my_sites_post_id ) ) {

      if( count( $wp_admin_bar->user->blogs ) > 1  ) {

        foreach( $wp_admin_bar->user->blogs as $blog ) {

          switch_to_blog( $blog->userblog_id );

          $menu_base_id  = sprintf( 'blog-%s' , $blog->userblog_id );

          $posts[ $menu_base_id ] = (object) array(
            'ID' => $menu_base_id,
            'item_parent' => $found_my_sites_post_id,
            'item_type' => 'default',
            'item_location' => $found_my_sites_item_location,
            'item_default_id' => $menu_base_id,
            'item_default_parent_id' => 'my-sites-list',
            'item_default_title' => sprintf( '<div class="blavatar"></div>%s' , $blog->blogname ),
            'item_link_title' => get_bloginfo( 'name' ),
            'item_link_url' => home_url(),
            'item_link_attr' => '',
            'item_capability' => 'read',
          );

          if ( current_user_can( 'read' ) ) {

            $menu_dashboard_id  = sprintf( '%s-d' , $menu_base_id );

            $posts[ $menu_dashboard_id ] = (object) array(
              'ID' => $menu_dashboard_id,
              'item_parent' => $menu_base_id,
              'item_type' => 'default',
              'item_location' => $found_my_sites_item_location,
              'item_default_id' => $menu_dashboard_id,
              'item_default_parent_id' => $menu_base_id,
              'item_default_title' => __( 'Dashboard' ),
              'item_link_title' => __( 'Dashboard' ),
              'item_link_url' => admin_url(),
              'item_link_attr' => '',
              'item_capability' => 'read',
            );

          }

          if ( current_user_can( 'manage_options' ) ) {

            $menu_setting_id  = sprintf( '%s-o' , $menu_base_id );

            $posts[ $menu_setting_id ] = (object) array(
              'ID' => $menu_setting_id,
              'item_parent' => $menu_base_id,
              'item_type' => 'link',
              'item_location' => $found_my_sites_item_location,
              'item_default_id' => '',
              'item_default_parent_id' => '',
              'item_default_title' => '',
              'item_link_title' => __( 'Settings' ),
              'item_link_url' => admin_url( '/options-general.php' ),
              'item_link_attr' => '',
              'item_capability' => 'manage_options',
            );

          }

          $menu_frontend_id  = sprintf( '%s-v' , $menu_base_id );

          $posts[ $menu_frontend_id ] = (object) array(
            'ID' => $menu_frontend_id,
            'item_parent' => $menu_base_id,
            'item_type' => 'default',
            'item_location' => $found_my_sites_item_location,
            'item_default_id' => $menu_frontend_id,
            'item_default_parent_id' => $menu_base_id,
            'item_default_title' => __( 'Visit Site' ),
            'item_link_title' => __( 'Visit Site' ),
            'item_link_url' => home_url( '/' ),
            'item_link_attr' => '',
            'item_capability' => '',
          );

          restore_current_blog();

        }

      } else {

        unset( $posts[ $found_my_sites_post_key ] );

      }

    }

    if( current_user_can( 'manage_network' ) ) {

      $found_network_admin_post_id = false;
      $found_network_admin_post_key = false;
      $found_network_admin_item_location = false;

      foreach( $posts as $key => $post ) {

        if( $post->item_default_id === 'network-admin' ) {

          $found_network_admin_post_id = $post->ID;
          $found_network_admin_post_key = $key;
          $found_network_admin_item_location = $post->item_location;

        }

      }

      if( ! empty( $found_network_admin_post_id ) ) {

        $posts[ $found_network_admin_post_key ]->item_type = 'link';
        $posts[ $found_network_admin_post_key ]->item_default_title = __( 'Network Admin' );
        $posts[ $found_network_admin_post_key ]->item_link_url = network_admin_url();
        $posts[ $found_network_admin_post_key ]->item_capability = 'manage_network';

        $posts['network-admin-d'] = (object) array(
          'ID' => 'network-admin-d',
          'item_parent' => $found_network_admin_post_id,
          'item_type' => 'default',
          'item_location' => $found_network_admin_item_location,
          'item_default_id' => 'network-admin-d',
          'item_default_parent_id' => 'network-admin',
          'item_default_title' => __( 'Dashboard' ),
          'item_link_title' => __( 'Dashboard' ),
          'item_link_url' => network_admin_url(),
          'item_link_attr' => '',
          'item_capability' => 'manage_network',
        );

        $posts['network-admin-s'] = (object) array(
          'ID' => 'network-admin-s',
          'item_parent' => $found_network_admin_post_id,
          'item_type' => 'default',
          'item_location' => $found_network_admin_item_location,
          'item_default_id' => 'network-admin-s',
          'item_default_parent_id' => 'network-admin',
          'item_default_title' => __( 'Sites' ),
          'item_link_title' => __( 'Sites' ),
          'item_link_url' => network_admin_url( 'sites.php' ),
          'item_link_attr' => '',
          'item_capability' => 'manage_sites',
        );

        $posts['network-admin-u'] = (object) array(
          'ID' => 'network-admin-u',
          'item_parent' => $found_network_admin_post_id,
          'item_type' => 'default',
          'item_location' => $found_network_admin_item_location,
          'item_default_id' => 'network-admin-u',
          'item_default_parent_id' => 'network-admin',
          'item_default_title' => __( 'Users' ),
          'item_link_title' => __( 'Users' ),
          'item_link_url' => network_admin_url( 'users.php' ),
          'item_link_attr' => '',
          'item_capability' => 'manage_network_users',
        );

        $posts['network-admin-t'] = (object) array(
          'ID' => 'network-admin-t',
          'item_parent' => $found_network_admin_post_id,
          'item_type' => 'default',
          'item_location' => $found_network_admin_item_location,
          'item_default_id' => 'network-admin-t',
          'item_default_parent_id' => 'network-admin',
          'item_default_title' => __( 'Themes' ),
          'item_link_title' => __( 'Themes' ),
          'item_link_url' => network_admin_url( 'themes.php' ),
          'item_link_attr' => '',
          'item_capability' => 'manage_network_themes',
        );

        $posts['network-admin-p'] = (object) array(
          'ID' => 'network-admin-p',
          'item_parent' => $found_network_admin_post_id,
          'item_type' => 'default',
          'item_location' => $found_network_admin_item_location,
          'item_default_id' => 'network-admin-p',
          'item_default_parent_id' => 'network-admin',
          'item_default_title' => __( 'Plugins' ),
          'item_link_title' => __( 'Plugins' ),
          'item_link_url' => network_admin_url( 'plugins.php' ),
          'item_link_attr' => '',
          'item_capability' => 'manage_network_plugins',
        );

        $posts['network-admin-o'] = (object) array(
          'ID' => 'network-admin-o',
          'item_parent' => $found_network_admin_post_id,
          'item_type' => 'default',
          'item_location' => $found_network_admin_item_location,
          'item_default_id' => 'network-admin-o',
          'item_default_parent_id' => 'network-admin',
          'item_default_title' => __( 'Settings' ),
          'item_link_title' => __( 'Settings' ),
          'item_link_url' => network_admin_url( 'settings.php' ),
          'item_link_attr' => '',
          'item_capability' => 'manage_network_options',
        );

        $posts['network-admin-o'] = (object) array(
          'ID' => 'network-admin-o',
          'item_parent' => $found_network_admin_post_id,
          'item_type' => 'default',
          'item_location' => $found_network_admin_item_location,
          'item_default_id' => 'network-admin-o',
          'item_default_parent_id' => 'network-admin',
          'item_default_title' => __( 'Settings' ),
          'item_link_title' => __( 'Settings' ),
          'item_link_url' => network_admin_url( 'settings.php' ),
          'item_link_attr' => '',
          'item_capability' => 'manage_network_options',
        );

        if( MywpApi::is_network_manager() ) {

          $posts['network-admin-mywp'] = (object) array(
            'ID' => 'network-admin-mywp',
            'item_parent' => $found_network_admin_post_id,
            'item_type' => 'link',
            'item_location' => $found_network_admin_item_location,
            'item_default_id' => '',
            'item_default_parent_id' => '',
            'item_default_title' => __( 'My WP' , 'my-wp' ),
            'item_link_title' => __( 'My WP' , 'my-wp' ),
            'item_link_url' => add_query_arg( array( 'page' => 'mywp_network' ) , network_admin_url( 'admin.php' ) ),
            'item_link_attr' => '',
            'item_capability' => MywpApi::get_network_manager_capability(),
          );

        }

      }

    }

    return $posts;

  }

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['custom_menu_ui'] = '';
    $initial_data['cache_timeout'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['custom_menu_ui'] = false;
    $default_data['cache_timeout'] = '60';

    return $default_data;

  }

  public static function mywp_wp_loaded() {

    if( ! is_admin() ) {

      return false;

    }

    if( is_network_admin() ) {

      return false;

    }

    if( ! self::is_do_controller() ) {

      return false;

    }

    add_action( 'wp_before_admin_bar_render' , array( __CLASS__ , 'remove_detault_menus' ) , 1000 );
    add_action( 'wp_before_admin_bar_render' , array( __CLASS__ , 'customize_admin_bar' ) , 1000 );
    add_action( 'wp_after_admin_bar_render' , array( __CLASS__ , 'wp_after_admin_bar_render' ) );

  }

  private static function get_toolbar() {

    global $wp_admin_bar;

    if( ! empty( self::$toolbar ) ) {

      return self::$toolbar;

    }

    $setting_data = self::get_setting_data();

    $timeout_min = 0;

    if( ! empty( $setting_data['cache_timeout'] ) ) {

      $timeout_min = intval( $setting_data['cache_timeout'] );

    }

    $mywp_transient = new MywpTransient( 'admin_toolbar_get_toolbar' , 'controller' );

    if( ! empty( $timeout_min ) ) {

      $transient_toolbar = $mywp_transient->get_data();

      if( ! empty( $transient_toolbar ) ) {

        self::$toolbar = $transient_toolbar;

        return self::$toolbar;

      }

    }

    $args = array(
      'post_status' => array( 'publish' ),
      'post_type' => 'mywp_admin_toolbar',
      'order' => 'ASC',
      'orderby' => 'menu_order',
      'posts_per_page' => -1,
      'tax_query' => array(
        array(
          'taxonomy' => 'mywp_term',
          'field' => 'slug',
          'terms' => 'default',
        ),
      ),
    );

    $args = apply_filters( 'mywp_controller_admin_toolbar_get_toolbar_args' , $args );

    $posts = MywpController::get_posts( $args , self::$id );

    $toolbar = apply_filters( 'mywp_controller_admin_toolbar_get_toolbar' , $posts );

    self::$toolbar = $toolbar;

    if( ! empty( $timeout_min ) && ! empty( $toolbar ) ) {

      $toolbar_strlen = strlen( maybe_serialize( self::$toolbar ) );

      if( $toolbar_strlen < MywpHelper::get_max_allowed_packet_size() ) {

        $mywp_transient->update_data( self::$toolbar , $timeout_min * MINUTE_IN_SECONDS );

      }

    }

    return $toolbar;

  }

  private static function get_toolbar_items() {

    if( ! empty( self::$toolbar_items ) ) {

      return self::$toolbar_items;

    }

    $toolbar = self::get_toolbar();

    if( empty( $toolbar ) ) {

      return false;

    }

    $toolbar_items = array();

    foreach( $toolbar as $key => $toolbar_item ) {

      if( $toolbar_item->item_type === 'default') {

        $toolbar_item = MywpAdminToolbar::default_item_convert( $toolbar_item );

        if( ! empty( $toolbar_item ) ) {

          $toolbar_items[] = $toolbar_item;

        }

      } else {

        $toolbar_items[] = $toolbar_item;

      }

    }

    $toolbar_items = apply_filters( 'mywp_controller_admin_toolbar_get_toolbar_item' , $toolbar_items );

    if( empty( $toolbar_items ) ) {

      return false;

    }

    ksort( $toolbar_items );

    self::$toolbar_items = $toolbar_items;

    return $toolbar_items;

  }

  private static function get_toolbar_items_added_classes(){

    if( ! empty( self::$toolbar_items_added_classes ) ) {

      return self::$toolbar_items_added_classes;

    }

    $toolbar_items = self::get_toolbar_items();

    if( empty( $toolbar_items ) ) {

      return false;

    }

    foreach( $toolbar_items as $key => $toolbar_item ) {

      if( ! is_object( $toolbar_item ) ) {

        unset( $toolbar_item[ $key ] );

        continue;

      }

      if( empty( $toolbar_item->item_li_class ) ) {

        $toolbar_items[ $key ]->item_li_class = '';

      }

      if( empty( $toolbar_item->item_link_class ) ) {

        $toolbar_items[ $key ]->item_link_class = '';

      }

      if( empty( $toolbar_item->item_link_url ) ) {

        $toolbar_items[ $key ]->item_link_url = '';

      }

      if( empty( $toolbar_item->item_link_url_parse ) ) {

        $toolbar_items[ $key ]->item_link_url_parse = array();

      }

      if( empty( $toolbar_item->item_link_url_parse_query ) ) {

        $toolbar_items[ $key ]->item_link_url_parse_query = array();

      }

      $toolbar_item->item_link_url = do_shortcode( $toolbar_item->item_link_url );

      $toolbar_items[ $key ]->item_link_url = $toolbar_item->item_link_url;

      if( ! empty( $toolbar_item->item_link_url ) ) {

        $item_link_url_parse = parse_url( $toolbar_item->item_link_url );

        if( isset( $item_link_url_parse['fragment'] ) ) {

          unset( $item_link_url_parse['fragment'] );

        }

        if( empty( $item_link_url_parse['query'] ) ) {

          $item_link_url_parse['query'] = '';

        }

        $toolbar_items[ $key ]->item_link_url_parse = $item_link_url_parse;

        if( ! empty( $item_link_url_parse['query'] ) ) {

          wp_parse_str( $item_link_url_parse['query'] , $item_link_url_parse_query );

          ksort( $item_link_url_parse_query );

          $toolbar_items[ $key ]->item_link_url_parse_query = $item_link_url_parse_query;

        }

      }

    }

    foreach( $toolbar_items as $key => $toolbar_item ) {

      self::$child_items[ $toolbar_item->item_parent ][] = $toolbar_item;

    }

    $tmp = $toolbar_items;

    foreach( $toolbar_items as $key => $toolbar_item ) {

      if( empty( $toolbar_item->item_parent ) ) {

        continue;

      }

      foreach( $tmp as $tmp_key => $tmp_toolbar_item ) {

        if( (string) $tmp_toolbar_item->ID === (string) $toolbar_item->item_parent ) {

          self::$parent_items[ $toolbar_item->ID ][] = $tmp_toolbar_item;

          break;

        }

      }

    }

    unset( $tmp );

    $first = true;

    foreach( $toolbar_items as $key => $toolbar_item ) {

    }

    $current_url = self::get_current_url();

    $current_url_parse = parse_url( $current_url );
    $current_url_query = array();

    if( isset( $current_url_parse['fragment'] ) ) {

      unset( $current_url_parse['fragment'] );

    }

    if( ! empty( $current_url_parse['query'] ) ) {

      wp_parse_str( $current_url_parse['query'] , $current_url_query );

      ksort( $current_url_query );

    } else {

      $current_url_parse['query'] = '';

    }

    $found_current_item_ids = array();

    foreach( $toolbar_items as $key => $toolbar_item ) {

      if( empty( $toolbar_item->item_link_url_parse['host'] ) or empty( $toolbar_item->item_link_url_parse['path'] ) ) {

        continue;

      }

      if(
        $current_url_parse['scheme'] === $toolbar_item->item_link_url_parse['scheme'] &&
        $current_url_parse['host'] === $toolbar_item->item_link_url_parse['host'] &&
        $current_url_parse['path'] === $toolbar_item->item_link_url_parse['path'] &&
        $current_url_query === $toolbar_item->item_link_url_parse_query
        ) {

        $found_current_item_ids[] = $toolbar_item->ID;

      }

    }

    if( empty( $found_current_item_ids ) ) {

      $identification_query = array();

      if( ! empty( $current_url_query['page'] ) ) {

        $identification_query['page'] = $current_url_query['page'];

      }

      if( ! empty( $current_url_query['post_type'] ) ) {

        if( $current_url_query['post_type'] !== 'post' ) {

          $identification_query['post_type'] = $current_url_query['post_type'];

        }

      }

      if( ! empty( $current_url_query['taxonomy'] ) ) {

        $identification_query['taxonomy'] = $current_url_query['taxonomy'];

      }

      foreach( $toolbar_items as $key => $toolbar_item ) {

        if( empty( $toolbar_item->item_link_url_parse['host'] ) or empty( $toolbar_item->item_link_url_parse['path'] ) ) {

          continue;

        }

        if(
          $current_url_parse['scheme'] === $toolbar_item->item_link_url_parse['scheme'] &&
          $current_url_parse['host'] === $toolbar_item->item_link_url_parse['host'] &&
          $current_url_parse['path'] === $toolbar_item->item_link_url_parse['path'] &&
          http_build_query( $identification_query ) === $toolbar_item->item_link_url_parse['query']
        ) {

          $found_current_item_ids[] = $toolbar_item->ID;

        }

      }

    }

    $found_current_item_ids = apply_filters( 'mywp_controller_admin_toolbar_get_toolbar_item_added_classes_found_current_item_ids' , $found_current_item_ids , $toolbar_items , $current_url , $current_url_parse , $current_url_query );

    if( ! empty( $found_current_item_ids ) ) {

      $found_current_item_ids = array_map( 'strip_tags' , $found_current_item_ids );

    }

    if( ! empty( $found_current_item_ids ) ) {

      foreach( $toolbar_items as $key => $toolbar_item ) {

        if( in_array( $toolbar_item->ID , $found_current_item_ids ) ) {

          $toolbar_items[ $key ]->item_li_class .= ' current';
          $toolbar_items[ $key ]->item_link_class .= ' current';

        }

      }

    }

    $toolbar_items = apply_filters( 'mywp_controller_admin_toolbar_get_toolbar_item_added_classes' , $toolbar_items );

    self::$toolbar_items_added_classes = $toolbar_items;

    return self::$toolbar_items_added_classes;

  }

  private static function get_find_menu_items_to_parent_id( $parent_id = 0 ) {

    $toolbar_items_added_classes = self::get_toolbar_items_added_classes();

    if( empty( $toolbar_items_added_classes ) ) {

      return false;

    }

    if( ! empty( $parent_id ) ) {

      if( is_numeric( $parent_id ) ) {

        $parent_id = intval( $parent_id );

      } else {

        $parent_id = strip_tags( $parent_id );

      }

    }

    if( ! empty( self::$find_parent_id[ $parent_id ] ) ) {

      return self::$find_parent_id[ $parent_id ];

    }

    $find_items = array();

    foreach( $toolbar_items_added_classes as $item ) {

      $item_parent = $item->item_parent;

      if( ! empty( $item_parent ) ) {

        if( is_numeric( $item_parent ) ) {

          $item_parent = intval( $item_parent );

        } else {

          $item_parent = strip_tags( $item_parent );

        }

      }

      if( $item_parent !== $parent_id ) {

        continue;

      }

      $find_items[] = $item;

    }

    if( empty( $find_items ) ) {

      return false;

    }

    self::$find_parent_id[ $parent_id ] = $find_items;

    return $find_items;

  }

  private static function get_current_url() {

    if( ! empty( self::$current_url ) ) {

      return self::$current_url;

    }

    $current_url = urldecode( do_shortcode( '[mywp_url current="1"]' ) );

    self::$current_url = $current_url;

    return $current_url;

  }

  public static function remove_detault_menus() {

    global $wp_admin_bar;

    if( empty( $wp_admin_bar ) ) {

      return false;

    }

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $parent_items = self::get_find_menu_items_to_parent_id();

    if( empty( $parent_items ) ) {

      return false;

    }

    $admin_bar_all_nodes = $wp_admin_bar->get_nodes();

    foreach( $admin_bar_all_nodes as $node ) {

      if( $node->id === 'top-secondary' ) {

        continue;

      }

      $wp_admin_bar->remove_menu( $node->id );

    }

    self::after_do_function( __FUNCTION__ );

  }

  public static function customize_admin_bar() {

    global $wp_admin_bar;

    if( empty( $wp_admin_bar ) ) {

      return false;

    }

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $parent_items = self::get_find_menu_items_to_parent_id();

    if( empty( $parent_items ) ) {

      return false;

    }

    foreach( $parent_items as $item ) {

      self::add_toolbar_item( $item );

    }

    self::after_do_function( __FUNCTION__ );

  }

  private static function add_toolbar_item( $item ) {

    global $wp_admin_bar;

    if( empty( $item ) or empty( $item->item_type ) or empty( $item->ID ) ) {

      return false;

    }

    $toolbar_items_added_classes = self::get_toolbar_items_added_classes();

    $item = apply_filters( 'mywp_controller_admin_toolbar_add_toolbar_item' , $item );

    if( ! empty( $item->item_capability ) ) {

      if( ! current_user_can( $item->item_capability ) ) {

        return false;

      }

    }

    $item_id = $item->ID;

    if( is_numeric( $item_id ) ) {

      $item_id = intval( $item_id );

    } else {

      $item_id = strip_tags( $item_id );

    }

    $item_type = $item->item_type;
    $node_parent = $item->item_parent;
    $item_location = $item->item_location;

    $item_meta = array();

    if( ! empty( $item->item_meta ) ) {

      $item_meta = $item->item_meta;

    }

    if( ! isset( $item_meta['class'] ) ) {

      $item_meta['class'] = false;

    }

    if( ! empty( $item->item_link_attr ) ) {

      $link_attr = strip_tags( $item->item_link_attr );

      $link_attr_arr = explode( ' ' , $link_attr );

      if( ! empty( $link_attr_arr ) ) {

        foreach( $link_attr_arr as $link_attr_field ) {

          if( empty( $link_attr_field ) ) {

            continue;

          }

          if( strpos( $link_attr_field , '=' ) === false ) {

            continue;

          }

          $link_attrs = explode( '=' , $link_attr_field );

          if( empty( $link_attrs[0] ) or empty( $link_attrs[1] ) ) {

            continue;

          }

          $link_attr_key = $link_attrs[0];

          $link_attr_val = str_replace( '"' , '' , $link_attrs[1] );

          $item_meta[ $link_attr_key ] = $link_attr_val;

        }

      }

    }

    $item->item_link_title = do_shortcode( $item->item_link_title );

    $li_class = '';

    if( ! empty( $item->item_li_class ) ) {

      $li_class = $item->item_li_class;

    }

    $li_id = '';

    if( ! empty( $item->item_li_id ) ) {

      $li_id = $item->item_li_id;

    }

    if( (string) $node_parent === '0' ) {

      $node_parent = '';

    }

    if( $item_location === 'right' ) {

      if( (string) $node_parent === '' ) {

        $node_parent = 'top-secondary';

      }

    }

    $node_id = $item->ID;

    if( ! empty( $item->item_default_id ) ) {

      $node_id = $item->item_default_id;

      foreach( $toolbar_items_added_classes as $toolbar_items_added_classes_item ) {

        if( $toolbar_items_added_classes_item->ID === $item->item_parent ) {

          $node_parent = $toolbar_items_added_classes_item->item_default_id;

          break;

        }

      }

    }

    $node_group = false;

    if( $item_type === 'group' ) {

      $node_group = 1;

    } elseif( $item_type === 'custom' ) {

      $item_meta['html'] = do_shortcode( $item->item_custom_html );

    }

    if( ! empty( $item->item_icon_class ) ) {

      $title = sprintf( '<span class="%s"></span>' , esc_attr( $item->item_icon_class ) ) . $item->item_link_title;

    } else {

      $title = $item->item_link_title;

    }

    if( empty( $node_group ) ) {

      $add_menu = array( 'id' => $node_id , 'title' => $title , 'parent' => $node_parent , 'href' => $item->item_link_url , 'meta' => $item_meta );

      $wp_admin_bar->add_menu( $add_menu );

    } else {

      if( strpos( $item_meta['class'] , 'ab-sub-secondary' ) === false ) {

        $item_meta['class'] .= ' ab-sub-secondary';

      }

      $add_menu = array( 'id' => $node_id , 'parent' => $node_parent , 'meta' => $item_meta );

      $wp_admin_bar->add_group( $add_menu );

    }

    $child_items = self::get_find_menu_items_to_parent_id( $item_id );

    if( ! empty( $child_items ) ) {

      foreach( $child_items as $child_item ) {

        self::add_toolbar_item( $child_item );

      }

    }

  }

  public static function wp_after_admin_bar_render() {

    global $wp_scripts;

    $toolbar = self::get_toolbar();

    if( empty( $toolbar ) ) {

      return false;

    }

    $wp_styles = wp_styles();

    printf( '<link rel="stylesheet" id="mywp_admin_toolbar-css"  href="%sadmin-toolbar.css?ver=%s" type="text/css" media="all" />' , esc_url( MywpApi::get_plugin_url( 'css' ) ) , $wp_styles->default_version );

    printf( '<script type="text/javascript" src="%sadmin-toolbar.js?ver=%s"></script>' , esc_url( MywpApi::get_plugin_url( 'js' ) ) , $wp_styles->default_version );

    $setting_data = self::get_setting_data();

    if( ! empty( $setting_data['custom_menu_ui'] ) ) {

      printf( '<link rel="stylesheet" id="mywp_admin_toolbar-custom-ui-css"  href="%sadmin-toolbar-custom-ui.css?ver=%s" type="text/css" media="all" />' , esc_url( MywpApi::get_plugin_url( 'css' ) ) , MYWP_VERSION );

      printf( '<script type="text/javascript" src="%sadmin-toolbar-custom-ui.js?ver=%s"></script>' , esc_url( MywpApi::get_plugin_url( 'js' ) ) , MYWP_VERSION );

    }

  }

}

MywpControllerModuleAdminToolbar::init();

endif;
