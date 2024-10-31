<?php defined( 'ABSPATH' ) or die( "This page intentionally left blank." ); ?>

<p>
	<input class="button button-primary button-large fmrb_mtm_sw simple" value="<?=$method['input']['text_simple']?>" <?=$method['input']['simple']?>>
</p>

<p>
	<input class="button button-primary button-large fmrb_mtm_sw advanced" value="<?=$method['input']['text_advanced']?>" <?=$method['input']['advanced']?>>
</p>

<div style="display: none;">
	<input type="radio" name="fmrb_mtb_switcher" value="simple" <?=$method['radio']['simple']?>>
	<input type="radio" name="fmrb_mtb_switcher" value="advanced" <?=$method['radio']['advanced']?>>
</div>

