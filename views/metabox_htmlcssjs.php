<?php defined( 'ABSPATH' ) or die( "This page intentionally left blank." ); ?>

<div class="fmrh_box">
    <div>
        <p>
            <label for="textarea_html">
                <?=_x("HTML Here", 'metabox', TDFM_rb);?>
	            <span><?=_x("Please put you HTML or shortcode here. It can be anything from a simple text to a complete design that takes whole page.", 'metabox', TDFM_rb);?></span>
            </label>
        </p>
        <div id="html"><?=$html?></div>
    </div>

    <div>
        <p>
            <label for="textarea_css">
	            <?=_x("CSS Style Here", 'metabox', TDFM_rb);?>
	            <span><?=_x("Please put your CSS Style below according to your preferences. (like: font-family, font-size, font-weight, text color, background-color).", 'metabox', TDFM_rb);?></span>
            </label>
        </p>
        <div id="css"><?=$css?></div>
    </div>

    <div>
        <p>
            <label for="textarea_js">
	            <?=_x("JS Script here", 'metabox', TDFM_rb);?>
	            <span><?=_x("If you need JS for your HTML, put it here.", 'metabox', TDFM_rb);?></span>
            </label>
        </p>
        <div id="js"><?=$js?></div>
    </div>
</div>

<div class="fmrh_info_box">
	<h1><?=_x("Please wait...", 'metabox', TDFM_rb);?></h1>
</div>

<div class="hidden_inputs">
    <textarea name="html" id="input_html"><?=$html?></textarea>
    <textarea name="css" id="input_css"><?=$css?></textarea>
    <textarea name="js" id="input_js"><?=$js?></textarea>
</div>
