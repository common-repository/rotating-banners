
var fmrb_groups_page = fmrb_groups_page;//fix the editor telling invalid variable // autocomplete

// jquery ui sortable
jQuery(function($) {

    // create jquery ui sortable for all 3 lists(ol/ul)
    $( "#sortable1, #sortable2, #sortable3" ).sortable({
        placeholder: "ui-state-highlight ui-state-placeholder",
        connectWith: ".connectedSortable",
    }).disableSelection();

    // if more than 20, show select2 instead of sortable1
    if ( $('.sortable1_select').length ) {
        select2_needed();
    }

    // on #sortable3 update/change (drag&drop to delete box)
    $( "#sortable3").on( "sortupdate", function( event, ui ) {
        var id = $( "#sortable3").find('li').attr('rb-id');
        var text = $( "#sortable3").find('li').text();

        // if more than 20
        if ( $('.sortable1_select').length ) {
            // add it to the select2 list
            $('.sortable1_select').prepend('<option value="'+id+'">'+text+'</option>');
            $('.sortable1_select').val(null).trigger("change");//clear
        } else {
            var current_class = ( $( "#sortable3").find('li').hasClass('ui-state-default') ) ? 'ui-state-default' : 'ui-state-highlight';
            // add it again to first sortable
            $( "#sortable1").append('<li class="ui-sortable-handle '+current_class+'" rb-id="'+id+'">'+text+'</li>');//append into first sortable
            $( "#sortable1").sortable('refresh');//refresh
        }

        // remove
        $( "#sortable3").find('li').remove();
        // refresh sortable
        $( "#sortable3").sortable('refresh');

    } );

    // on #sortable2 update/change (this group's rotating banners)
    $( "#sortable2").on( "sortupdate", function( event, ui ) {

        // change the input (save group config)
        var group_config_json = [];
        $( "#sortable2 li" ).each( function() {
            group_config_json.push( parseInt( $(this).attr('rb-id') ) );
        });
        $('.group_config_ih').val(JSON.stringify(group_config_json));

        // change the text of Current value:
        var first_el_text = $('#sortable2 li:first').text();
        var $current_value = $('.current_value');
        if (first_el_text.length) {
            // you have at least 1 el
            $current_value.html(first_el_text + ' <span>' + fmrb_groups_page.translate.you_need_to_save_first + '</span>');
        } else {
            // you dont have any elements in second column
            $current_value.html(fmrb_groups_page.translate.no_rb_to_this_group);
        }
    } );
});

// confirm delete function
jQuery(function($) {
    $("form.group_settings_form input[name='delete_group']").on('click', function(e) {
        // prevent form submit
        var r = confirm( fmrb_groups_page.translate.delete_group_confirmation_title );
        if (r == true) {
            return true;
        } else {
            return false;
        }
    })
});

// copy
jQuery(function($) {
    $('.htt_2').on('click', function() {
        $('.htt_4').removeClass('copied');
        $('.htt_2').removeClass('copied').addClass('copied');
        $('.htt_6').html( "<br>" + fmrb_groups_page.translate.copied_to_clipboard + "<div>" + $(this).html() + "</div>" );
        copyToClipboard($(this));
        SelectText('htt_2');
    });
    $('.htt_4').on('click', function() {
        $('.htt_2').removeClass('copied');
        $('.htt_4').removeClass('copied').addClass('copied');
        $('.htt_6').html( "<br>" + fmrb_groups_page.translate.copied_to_clipboard + "<div>" + $(this).text() + "</div>" );
        copyToClipboard($(this));
        SelectText('htt_4');
    });
});

// Helper Btn: Toggle shortcode :: widget='true' ::
jQuery(function($) {
    var toggle = 0;
    var $el = $('.htt_24 span');
    $('.btnHelpToggleWidgetParam').on('click', function() {
        toggle = (toggle) ? 0 : 1; // toggle the toggle var.
        // Write or Remove the :: widget='true' :: from the shortcode buttons
        if (toggle == 1) {
            $el.text(" widget='true'");
        } else {
            $el.text("");
        }
        // Click again on the Copy btn, to Copy to clipboard the new shortcode and also to populate the 'ShortCode Copied to ClipBoard:' Info area.
        $('.htt_24.copied').click();
    });
});

function select2_needed() {
    var $ = jQuery;

    var $sortable1_select = $('.sortable1_select');

    // create select2
    $sortable1_select.select2({
        placeholder: fmrb_groups_page.translate.select2_placeholder,
    });
    // clear to show placeholder
    $sortable1_select.val(null).trigger("change");//clear

    // on select, move the element to second column
    $sortable1_select.on("select2:select", function (e) {

        var id = e.params.data.id;
        var text = e.params.data.text;

        // append
        $( "#sortable2").append('<li class="ui-state-default ui-sortable-handle" rb-id="'+id+'">'+text+'</li>');
        $( "#sortable2").trigger("sortupdate"); // trigger sortable update

        // remove this id from select
        $sortable1_select.find('option[value="'+id+'"]').remove();

        // clear to show placeholder
        $sortable1_select.val(null).trigger("change");//clear
    });

}

function copyToClipboard(element) {
    var $ = jQuery;
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
}

function SelectText(element) {
    var doc = document,
        text = doc.getElementById(element),
        range,
        selection;
    if (doc.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(text);
        range.select();
    } else if (window.getSelection) {
        selection = window.getSelection();
        range = document.createRange();
        range.selectNodeContents(text);
        selection.removeAllRanges();
        selection.addRange(range);
    }
}