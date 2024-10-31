<?php defined( 'ABSPATH' ) or die( "This page intentionally left blank." ); ?>
<div class="rb-container" data-group="<?=$group_slug?>" data-time="<?=time()?>">
	<?php if ( $method == 'simple' || empty($method) ) { ?>
		<?php echo $the_content ?>
	<?php } elseif ( $method == 'advanced' ) { ?>
		<style>
			<?=$css?>
		</style>
	<?=$html?>
		<script>
			<?=$js?>
		</script>
	<?php } ?>
</div>