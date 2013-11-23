<?php
/**
 * Joomla! module MageBridge: Slider
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2012
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Parameters
$width = (int)$params->get('slidesjs_width', 580);
$height = (int)$params->get('slidesjs_height', 380);
?>
<style text="text/css">
#container { width:<?php echo $width; ?>px; height:<?php echo $height; ?>px; }
.slides_container { width:<?php echo $width - 10; ?>px; height:<?php echo $height - 50; ?>px; }
#slides { width:<?php echo $width; ?>px; height:<?php echo $height; ?>px; }
</style>
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery("#slides").slides({
        preload: true,
        preloadImage: '/media/mod_magebridge_slider/slidesjs/images/loading.gif',
        play: 5000,
        pause: 2500,
        hoverPause: <?php echo (boolean)$params->get('slidesjs_hoverpause', '1'); ?>
    });
});
</script>
<div id="container" class="magebridge-slider magebridge-module">
<div id="container-inner">
<div id="slides">
<?php if (!empty($products) && is_array($products)) { ?>
<div class="slides_container">
    <?php foreach ($products as $product) { ?>
        <div class="slide">
            <?php $image = $params->get('image', 'image'); ?>
            <a href="<?php echo $product['url']; ?>" title="<?php echo $product['label']; ?>"><img src="<?php 
                echo $product[$image]; ?>" title="<?php echo $product['label']; ?>" alt="<?php 
                echo $product['label']; ?>" align="left" /></a>

            <?php if ($params->get('show_title',1)) : ?>
                <a href="<?php echo $product['url']; ?>"><h3><?php echo $product['name']; ?></h3></a>
            <?php endif; ?>

            <?php if ($params->get('show_short_description',1)) : ?>
                <p><?php echo $product['short_description']; ?></p>
            <?php endif; ?>

            <?php if ($params->get('show_description',1)) : ?>
                <p><?php echo $product['description']; ?></p>
            <?php endif; ?>

            <?php if ($params->get('show_price', 1)) : ?>
                <?php if ($product['has_special_price'] && $params->get('special_price', 1) != 0): ?>
                    <?php if ($params->get('special_price') == 2): ?>
                    <p><span class="normal_price"><?php echo $product['price']; ?></span></p>
                    <p><span class="special_price"><?php echo $product['special_price']; ?></span></p>
                    <?php else: ?>
                    <p><span><?php echo $product['special_price']; ?></span></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p><span><?php echo $product['price']; ?></span></p>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($params->get('show_readmore',1) || $params->get('show_addtocart')) : ?>
                <ul>
                <?php if ($params->get('show_readmore',1)) : ?>
                    <li><a href="<?php echo $product['url']; ?>" title="<?php echo $product['readmore_label']; ?>"><?php echo $product['readmore_text']; ?></a></li>
                <?php endif; ?>
                <?php if ($params->get('show_addtocart',1)) : ?>
                    <li><a href="<?php echo $product['addtocart_url']; ?>" title="<?php echo $product['addtocart_label']; ?>"><?php echo $product['addtocart_text']; ?></a></li>
                <?php endif; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php } ?>
</div>
<a href="#" class="prev"><img src="/media/mod_magebridge_slider/slidesjs/images/arrow-prev.png" width="24" height="43" alt="Previous"></a>
<a href="#" class="next"><img src="/media/mod_magebridge_slider/slidesjs/images/arrow-next.png" width="24" height="43" alt="Next"></a>
<?php } else { ?>
    <?php if ($params->get('show_noitems',1)) : ?>
        <?php echo JText::_( 'MOD_MAGEBRIDGE_SLIDER_NO_PRODUCTS' ); ?>
    <?php endif; ?>
<?php } ?>
</div>
</div>
</div>
