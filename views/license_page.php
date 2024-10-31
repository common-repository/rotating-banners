<?php defined( 'ABSPATH' ) or die( "This page intentionally left blank." ); ?>

<div class="wrap">

	<h2><?=_x("Rotating Banners License", 'license title', TDFM_rb);?></h2>
	<?=$this->message?>

	<div class="formdiv">
		<h3>
			<?=_x("License Key & Status", 'license key form', TDFM_rb)?>
		</h3>
		<form action="" method="post">
			<?php wp_nonce_field( basename( $this->file ), 'fmrb_nonce_lp' ); ?>
			<input type="hidden" name="action" value="license_key">

			<table class="form-table">
				<tbody>

				<tr valign="top">
					<th valign="top" scope="row"><?=_x("License Key", 'license key form', TDFM_rb);?></th>
					<td>
						<input type="text" name="fmrb_license_key" value="<?=$license_key?>" class="regular-text">
					</td>
				</tr>
				<tr valign="top">
					<th valign="top" scope="row"><?=_x("License Status", 'license key form', TDFM_rb);?></th>
					<td>
						<span style="color:<?=$view_license_color?>;"><?=$view_license_status?></span>
					</td>
				</tr>
				<tr valign="top">
					<th valign="top" scope="row"><?=_x("Max number of Rotating Banners", 'license key form', TDFM_rb);?></th>
					<td>
						<span style="color: initial;"><?=$view_license_nr?></span>
					</td>
				</tr>

				</tbody>
			</table>

			<p class="submit">
				<input type="submit" value="<?=_x("Save License Key", 'license key save button', TDFM_rb);?>" class="button" name="fmrb_license_key_save">
			</p>
		</form>
	</div>

	<div class="formdiv">
		<h3>
			<?=_x("Where can I get a License Key?", 'license key form', TDFM_rb)?>
		</h3>

		<?=_x(
"
<p>Enter your Email below and you will receive the License Key!</p>
<p>The License Key will be available only for your blog, please don't share it!</p>
"
				, 'license page into', TDFM_rb)?>

		<form method="post" accept-charset="UTF-8" action="https://www.aweber.com/scripts/addlead.pl"  >
			<div style="display: none;">
				<input type="hidden" name="meta_web_form_id" value="346140211" />
				<input type="hidden" name="meta_split_id" value="" />
				<input type="hidden" name="listname" value="awlist4081058" />
				<input type="hidden" name="redirect" value="" id="redirect_295d9b605a235ff0d27b3e22ceb8fd6e" />

				<input type="hidden" name="meta_adtracking" value="Receive_License_Key" />
				<input type="hidden" name="meta_message" value="1" />
				<input type="hidden" name="meta_required" value="email" />

				<input type="hidden" name="meta_tooltip" value="" />
			</div>

			<table class="form-table">
				<tbody>
				<tr valign="top">
					<th valign="top" scope="row"><label for="lkeyem">Email to receive License Key</label></th>
					<td>
						<input type="email" name="email" class="regular-text" id="lkeyem" value="" tabindex="500" required onfocus=" if (this.value == '') { this.value = ''; }" onblur="if (this.value == '') { this.value='';} " />
						<input type="submit" name="submit" value="Receive License Key" class="button" tabindex="501" alt="Submit Form" >
					</td>
				</tr>
				</tbody>
			</table>

		</form>
		<script type="text/javascript">document.getElementById('redirect_295d9b605a235ff0d27b3e22ceb8fd6e').value = document.location + "&aweber_return=1";</script>

		<p>
			If you need <strong>Multiple Groups</strong>, you need to upgrade to the <strong>Pro Version</strong> available here: <strong><a href="http://marketinghack.fr/rotating-banners-pro/" target="_blank">http://marketinghack.fr/rotating-banners-pro/</a></strong>
		</p>


	</div>

</div>
