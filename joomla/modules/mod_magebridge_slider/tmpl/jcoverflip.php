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
?>
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#flip').jcoverflip({
        current: <?php echo (int)count($products) / 2; ?>,
        time: <?php echo (int)$params->get('speed', '1000'); ?>
    });
});
</script>
<div class="magebridge-jcoverflip magebridge-module">
<div id="wrapper">
<?php if (!empty($products) && is_array($products)) { ?>
    <ul id="flip">
    <?php foreach ($products as $product) { ?>
        <li>
            <?php if ($params->get('show_title',1)) : ?>
                <span class="title"><a href="<?php echo $product['url']; ?>"><?php echo $product['name']; ?></a></span>
            <?php endif; ?>

            <?php $image = $params->get('image', 'image'); ?>
            <img src="<?php 
                echo $product[$image]; ?>" title="<?php echo $product['label']; ?>" alt="<?php 
                echo $product['label']; ?>" />
        </li>
    <?php } ?>
    </ul>
<?php } else { ?>
    <?php if ($params->get('show_noitems',1)) : ?>
        <?php echo JText::_( 'MOD_MAGEBRIDGE_SLIDER_NO_PRODUCTS' ); ?>
    <?php endif; ?>
<?php } ?>
</div>
</div>
