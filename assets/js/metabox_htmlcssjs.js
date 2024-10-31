// HTML
var html = ace.edit("html");
html.setTheme("ace/theme/monokai");
html.getSession().setMode("ace/mode/html");
html.setShowPrintMargin(false);
html.setOption("enableEmmet", true);

var input_html = jQuery('textarea[name="html"]');
html.getSession().on("change", function () {
    input_html.val(html.getSession().getValue());
});

// CSS
var css = ace.edit("css");
css.setTheme("ace/theme/monokai");
css.getSession().setMode("ace/mode/css");
css.setShowPrintMargin(false);
css.setOption("enableEmmet", true);

var input_css = jQuery('textarea[name="css"]');
css.getSession().on("change", function () {
    input_css.val(css.getSession().getValue());
});

// JS
var js = ace.edit("js");
js.setTheme("ace/theme/monokai");
js.getSession().setMode("ace/mode/javascript");
js.setShowPrintMargin(false);
js.setOption("enableEmmet", true);

var input_js = jQuery('textarea[name="js"]');
js.getSession().on("change", function () {
    input_js.val(js.getSession().getValue());
});

jQuery('.fmrh_box').show();
jQuery('.fmrh_info_box').hide();

//TODO :: Probably you should put the above into a document.ready