<?php defined( 'ABSPATH' ) or die( "This page intentionally left blank." ); ?>

<div class="wrap group">

	<h2><?=_x("Group Rotating Banners", 'groups', TDFM_rb);?></h2>

	<?=$this->message?>

	<div>

		<div class="formdiv timer">
			<h3>
				<?=sprintf(
						_x("Modify the time banners change. Current value: <span>every %s %s</span>", 'groups', TDFM_rb),
						$formInputTime, $selectEveryArr[$formSelectTime]
				)?>
			</h3>
			<h4>
				<?=sprintf(
					_x("Next change will be in: <span>%s seconds</span> (%s - server time)", 'groups', TDFM_rb),
					$this->data->selected_group['ts_end'] - time(), date('l jS \of F Y h:i:s A', $this->data->selected_group['ts_end'])
				)?>
			</h4>
			<form action="" method="post">
				<?php wp_nonce_field( basename( $this->file ), 'fmrb_nonce_sp' ); ?>
				<input type="hidden" name="changetimeform" />

				<span><?=_x("Banners change every: ", 'groups', TDFM_rb);?></span>

				<input type="number" min="0" name="changetime" size="10" value="<?=$formInputTime?>" />

				<select name="selecttime">
					<?php foreach ($selectEveryArr as $k => $v) { ?>
						<? ($k==$formSelectTime) ? $sel1='selected="selected"' : $sel1=''; ?>
						<option value="<?=$k?>" <?=$sel1?> ><?=$v?></option>
					<?php } ?>
				</select>

				<input type="submit" class="button button-primary" value="<?=_x("Save Time Config", 'groups save button', TDFM_rb)?>" />
			</form>
			<span class="help"><?=_x("<strong>Note:</strong> It will change the current timer, starting from now!", 'groups', TDFM_rb);?></span>
		</div>

		<div class="formdiv ascs">
			<h3>
				<?=sprintf(
					_x("Change current banner. Current value: <span class='current_value'>%s</span>", 'groups', TDFM_rb),
					$currentHeaderName
				)?>
			</h3>
			<form action="" method="post">
				<?php wp_nonce_field( basename( $this->file ), 'fmrb_nonce_sp' ); ?>
				<input type="hidden" name="save_group_config" />

				<div class="container_change_v2">

					<input type="hidden" name="group_config" class="group_config_ih" value="<?=json_encode($this->data->selected_group['ids'])?>">

					<div class="first_col">
						<p class="arg_col_title"><?=_x("Available Rotating Banners", 'groups', TDFM_rb)?></p>
						
						<?php if ( count($all_missing_rotating_banners) > 20 && 1 ) { ?>

							<div class="sortable1_select_container">
								<h3><?=_x("Select Rotating Banner to add", 'groups', TDFM_rb)?></h3>

								<select class="sortable1_select" style="">
									<?php foreach ( $all_missing_rotating_banners as $rb ) { ?>
										<option value="<?=$rb->ID?>"><?=$rb->post_title?></option>
									<?php } ?>
								</select>
							</div>

						<?php } else { ?>
							<ol id="sortable1" class="connectedSortable">
								<?php foreach ( $all_missing_rotating_banners as $rb ) { ?>
									<li rb-id="<?=$rb->ID?>" class="ui-state-default"><?=$rb->post_title?></li>
								<?php } ?>
							</ol>
						<?php } ?>
						
						<p><a class="button button-secondary" href="<?=admin_url('post-new.php?post_type=fmrb')?>"><?=_x("Add new Rotating Banner", 'groups under Available Rotating Banners', TDFM_rb)?></a></p>
					</div>

					<div>
						<p class="arg_col_title"><?=_x("This group's Rotating Banners", 'groups', TDFM_rb)?></p>
						<ol id="sortable2" class="connectedSortable">
							<?php foreach ( $groups_rbs as $rb_id => $rb_title ) { ?>
								<li rb-id="<?=$rb_id?>" class="ui-state-highlight"><?=$rb_title?></li>
							<?php } ?>
						</ol>

						<div class="drop_here_to_remove">
							<div>
								<ul id="sortable3" class="connectedSortable"></ul>
							</div>
							<p><?=_x("Drag & Drop here to remove", 'groups', TDFM_rb)?></p>
						</div>

					</div>

					<div class="save_btn">
						<div>
							<input type="submit" class="button button-primary button-large" value="<?=_x("Save Group Config", 'groups save button', TDFM_rb)?>" />
						</div>
					</div>


				</div>

			</form>

		</div>

		<div class="formdiv shortcodeSettings">
			<h3>
				<?=_x("Shortcode Settings (Helper Buttons)", 'groups form title', TDFM_rb)?>
			</h3>
			<h4>
				<?=_x("In order for the Shortcode to work (display) correctly inside a Widget, you need to append/add a new parameter/attribute to it :: widget='true' ::.<br>Click the button to Toggle (add/remove) this parameter, then you can Click to Copy the shortcode on one of the 2 buttons down below.", 'group btn hepler', TDFM_rb)?>
			</h4>
			<input type="button" value="<?=_x("Click here to Toggle the Shortcode :: widget='true' :: parameter", 'group btn helper', TDFM_rb)?>" class="button button-primary button-large btnHelpToggleWidgetParam">
		</div>

		<div class="how_to_text">
			<div>
				<p class="htt_1"><?=_x("Put this shortcode in your theme/widget or anywhere you want your Rotating Banners/Headers to appear", 'group how-to-text', TDFM_rb)?></p>
				<p class="htt_2 htt_24" id="htt_2">&lt;?php echo do_shortcode( "[rotating_banners<span></span>]" ); ?></p>
				<br>
				<p class="htt_3"><?=_x("Also, you can use the shortcode anywhere in your texts (like post content, widgets, etc)", 'group how-to-text', TDFM_rb)?></p>
				<p class="htt_4 htt_24" id="htt_4">[rotating_banners<span></span>]</p>
				<br>
				<p class="htt_5"><?=_x("Click on the  desired code to copy on clipboard", 'group how-to-text', TDFM_rb)?></p>
				<p class="htt_6"></p>
			</div>
		</div>

	</div>

</div>
