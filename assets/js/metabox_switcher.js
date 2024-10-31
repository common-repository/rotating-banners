
var fmrb_metabox_switcher = fmrb_metabox_switcher;//fix the editor telling invalid variable // autocomplete

if (fmrb_metabox_switcher.method == 'simple') {
    jQuery('body.post-type-fmrb .wrap form#post #post-body #post-body-content #postdivrich').show();//show the simple
} else {
    jQuery('body.post-type-fmrb .wrap form#post #post-body .postbox-container#postbox-container-2').show();//show the advanced
}

jQuery(function($) {
    $('.fmrb_mtm_sw').on('click', function() {
        var el_simple = $('.fmrb_mtm_sw.simple');
        var el_advanced = $('.fmrb_mtm_sw.advanced');

        if ($(this).hasClass('simple')) {
            // show/hide the parts of page
            $('body.post-type-fmrb .wrap form#post #post-body .postbox-container#postbox-container-2').hide();//hide the advanced
            $('body.post-type-fmrb .wrap form#post #post-body #post-body-content #postdivrich').show();//show the simple
            // check/uncheck the hidden radio
            $('input[type="radio"][name="fmrb_mtb_switcher"][value="simple"]').prop( "checked", true);
            $('input[type="radio"][name="fmrb_mtb_switcher"][value="advanced"]').prop( "checked", false);
            // change the Disable state of the buttons
            el_simple
                .prop( "disabled", true)
                .val(fmrb_metabox_switcher.simple_active);
            el_advanced
                .prop( "disabled", false )
                .val(fmrb_metabox_switcher.switch_to_advanced);
        }

        if ($(this).hasClass('advanced')) {
            // show/hide the parts of page
            $('body.post-type-fmrb .wrap form#post #post-body #post-body-content #postdivrich').hide();//hide the simple
            $('body.post-type-fmrb .wrap form#post #post-body .postbox-container#postbox-container-2').show();//show the advanced
            // check/uncheck the hidden radio
            $('input[type="radio"][name="fmrb_mtb_switcher"][value="advanced"]').prop( "checked", true);
            $('input[type="radio"][name="fmrb_mtb_switcher"][value="simple"]').prop( "checked", false);
            // change the Disable state of the buttons
            el_advanced
                .prop( "disabled", true )
                .val(fmrb_metabox_switcher.advanced_active);
            el_simple
                .prop( "disabled", false )
                .val(fmrb_metabox_switcher.switch_to_simple);
        }
    })
});




