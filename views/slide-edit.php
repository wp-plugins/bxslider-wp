<?php if(!defined('BXSLIDER_PATH')) die('Direct access denied.'); ?>

<div class="bxslider-slide">
	<div class="bxslider-header">
		<span class="bxslider-icon">
			<?php if ('custom'==$slide['type']) : ?>
			<i class="icon-font"></i>
			<?php elseif ('video'==$slide['type']) : ?>
			<i class="icon-film"></i>
			<?php else: ?>
			<i class="icon-picture"></i>
			<?php endif; ?>
		</span>
		<span class="bxslider-title">
			<?php echo $box_title; ?>
		</span>
		<span class="bxslider-controls">
			<span class="bxslider-drag" title="<?php _e('Drag', 'cycloneslider'); ?>"><?php _e('Drag', 'cycloneslider'); ?></span>
			<span class="bxslider-toggle" title="<?php _e('Toggle', 'cycloneslider'); ?>"><?php _e('Toggle', 'cycloneslider'); ?></span>
			<span class="bxslider-delete" title="<?php _e('Delete', 'cycloneslider'); ?>"><?php _e('Delete', 'cycloneslider'); ?></span>
		</span>
		<div class="clear"></div>
	</div>
	<div class="bxslider-body">
		<div class="bxslider-slide-type-bar">
			<select class="bxslider-slide-type-switcher" name="bxslider[slides][<?php echo $i; ?>][type]">
				<option value="image" <?php echo ('image'==$slide['type']) ? 'selected="selected"' : ''; ?>><?php _e('Image', 'cycloneslider'); ?></option>
				<option value="custom" <?php echo ('custom'==$slide['type']) ? 'selected="selected"' : ''; ?>><?php _e('Custom', 'cycloneslider'); ?></option>
			</select>	
		</div>
		
		<div class="clear"></div>
		<div class="bxslider-image">
			<div class="bxslider-image-preview">
				<div class="bxslider-image-thumb" <?php echo (empty($image_url)) ? 'style="display:none"' : '';?>>
					<?php if($image_url): ?>
					<img src="<?php echo esc_url($image_url); ?>" alt="thumb">
					<?php endif; ?>
				</div>
				<input class="bxslider-image-id" name="bxslider[slides][<?php echo $i; ?>][id]" type="hidden" value="<?php echo esc_attr($slide['id']); ?>" />
				<input class="button-secondary bxslider-media-gallery-show" type="button" value="<?php _e('Get Image', 'cycloneslider'); ?>" />
			</div>
			<div class="bxslider-image-settings">
				<p class="expandable-group-title first"><?php _e('Slide Properties:', 'cycloneslider'); ?></p>
				<div class="expandable-box last">
					<div class="expandable-header"><?php _e('Caption', 'cycloneslider'); ?></div>
					<div class="expandable-body">
						<div class="field last">
							<textarea class="widefat cycloneslider-slide-meta-description" name="bxslider[slides][<?php echo $i; ?>][caption]"><?php echo esc_html($slide['caption']); ?></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="bxslider-custom">
			<div class="field last">
				<p>Available in <a href="http://www.codefleet.net/bxslider-wp/">premium</a> version.</p>
			</div>
		</div>
		<div class="clear"></div>
	</div>
</div>