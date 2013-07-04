<?php if(!defined('BXSLIDER_PATH')) die('Direct access denied.'); ?>

<div class="bxslider-sortables" data-post-id="<?php echo $post->ID; ?>">
	<?php echo $slides; ?>
</div><!-- end .bxslider-sortable -->

<input type="button" value="<?php _e('Add Slide', 'bxslider'); ?>" class="bxslider-add-slide button-secondary" />