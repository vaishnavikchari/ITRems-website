jQuery(document).ready(function($){

  $('#adminmenu li').off('click');

  $('#adminmenu > li > a').on('click', function() {

    var $body = $('body');
    var $menu_item_link = $(this);
    var $menu_item = $menu_item_link.parent();
    var $submenu_items = $menu_item.find('ul.wp-submenu');

    if( $body.hasClass('folded') ) {

      $body.removeClass('folded');

    }

    if( $submenu_items.length > 0 ) {

      $menu_item.toggleClass('selected');

      return false;

    }

    return true;

  });

  $('#sidebar-custom-menu-ui-mask').on('click', function() {

    $('#wpwrap').removeClass('wp-responsive-open');

  });

});
