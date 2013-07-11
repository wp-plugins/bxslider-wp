<?php if(!defined('BXSLIDER_PATH')) die('Direct access denied.'); ?>

<ul class="bxslider" <?php bxslider_options( $slider_id ); ?>>
    <?php foreach($slides  as $slide): ?>
        <li><img src="<?php echo esc_url( $slide['image_url'] ); ?>" title="<?php echo esc_attr( $slide['caption'] ); ?>" /></li>
    <?php endforeach; ?>
</ul>