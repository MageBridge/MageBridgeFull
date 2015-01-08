<?php
/**
 * Joomla! module MageBridge: Slider
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Parameters
$width = (int)$params->get('awkward_width', 538);
$height = (int)$params->get('awkward_height', 370);
?>
<style text="text/css">
.showcase-load, .showcase-content-wrapper {width:<?php echo $width - 12; ?>px;}
.showcase-content-wrapper {height:<?php echo $height - 10; ?>px;}
</style>
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#showcase').awShowcase({
        content_width:          <?php echo $width; ?>,
        content_height:         <?php echo $height; ?>,
        fit_to_parent:          false,
        auto:                   true,
        interval:               <?php echo (int)$params->get('interval', '4000'); ?>,
        continuous:             false,
        loading:                true,
        tooltip_width:          200,
        tooltip_icon_width:     32,
        tooltip_icon_height:    32,
        tooltip_offsetx:        18,
        tooltip_offsety:        0,
        arrows:                 true,
        buttons:                true,
        btn_numbers:            true,
        keybord_keys:           true,
        mousetrace:             false, /* Trace x and y coordinates for the mouse */
        pauseonover:            true,
        stoponclick:            false,
        transition:             'hslide', /* hslide/vslide/fade */
        transition_delay:       0,
        transition_speed:       500,
        show_caption:           'onload', /* onload/onhover/show */
        thumbnails:             false,
        thumbnails_position:    'outside-last', /* outside-last/outside-first/inside-last/inside-first */
        thumbnails_direction:   'vertical', /* vertical/horizontal */
        thumbnails_slidex:      1, /* 0 = auto / 1 = slide one thumbnail / 2 = slide two thumbnails etc. */
        dynamic_height:         false, /* For dynamic height to work in webkit you need to set the width and height of images in the source. Usually works to only set the dimension of the first slide in the showcase. */
        speed_change:           true, /* Set to true to prevent users from swithing more then one slide at once. */
        viewline:               false, /* If set to true content_width, thumbnails, transition and dynamic_height will be disabled. As for dynamic height you need to set the width and height of images in the source. */
        custom_function:        null /* Define a custom function that runs on content change */
    });
});
</script>
<div class="magebridge-slider magebridge-module">
<?php if (!empty($products) && is_array($products)) { ?>
<div id="showcase" class="showcase">
    <?php foreach ($products as $product) { ?>
    <div class="showcase-slide">
        <div class="showcase-content">
            <div class="showcase-content-wrapper">
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

            <?php if ($params->get('show_thumb', 1)) : ?>
                <?php $thumb = $params->get('thumb', 'thumbnail'); ?>
                <p><a href="<?php echo $product['url']; ?>" title="<?php echo $product['label']; ?>"><img src="<?php 
                    echo $product[$thumb]; ?>" title="<?php echo $product['label']; ?>" alt="<?php 
                    echo $product['label']; ?>" /></a></p>
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
        </div>
    </div>
    <?php } ?>
</div>
<?php } else { ?>
    <?php if ($params->get('show_noitems',1)) : ?>
        <?php echo JText::_( 'MOD_MAGEBRIDGE_SLIDER_NO_PRODUCTS' ); ?>
    <?php endif; ?>
<?php } ?>
</div>
