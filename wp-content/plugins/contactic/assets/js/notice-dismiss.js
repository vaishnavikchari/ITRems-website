jQuery(document).on('click', '.contactic-notice .notice-dismiss', function() {

    jQuery.ajax({
        url: ajaxurl,
        data: {
            action: 'dismiss_contactic_notice',
            id: jQuery(this).parent().data('id')
        }
    })

})