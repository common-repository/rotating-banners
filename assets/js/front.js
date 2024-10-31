
function rotating_banners_dynamic_method_decide_if_do_ajax($) {
    var fmrb_front_ajax_url = fmrb_front.admin_url;
    delete fmrb_front.admin_url;

    $( ".rb-container" ).each( function() {

        // if time from server + 60 seconds    <    current time, that means that is a cached page, do ajax
        if ( ( $( this ).data('time') + 30 ) < ( Date.now() / 1000 | 0 ) ) {

            var $element = $( this );

            var data = fmrb_front;
            data.group = $element.data('group');

            $.ajax({
                url: fmrb_front_ajax_url,
                data: data,
                type: 'POST',
                success: function( d ) {
                    if (typeof d.return !== 'undefined') {
                        $element.replaceWith(d.return);
                    }
                }
            });
        }

    });
}
rotating_banners_dynamic_method_decide_if_do_ajax(jQuery);