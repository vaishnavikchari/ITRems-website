jQuery(document).ready( function() {
	jQuery.ajax({
	    url: ajaxurl,
	    type : 'post',
        data: {
            action: 'get_the_page',
            ctc_referer: document.referrer,
            ctc_uri: location.pathname,
            ctc_title: document.getElementsByTagName("title")[0].innerHTML,
        }
	});
});