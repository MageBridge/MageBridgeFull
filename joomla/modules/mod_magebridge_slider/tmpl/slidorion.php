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
$width = (int)$params->get('slidorion_width', 628);
$height = (int)$params->get('slidorion_height', 420);
$accordionwidth = (int)$params->get('slidorion_accordionwidth', 240);
?>
<style text="text/css">
#slidorion{ width:<?php echo $width; ?>px; }
#accordion { width:<?php echo $accordionwidth; ?>px; }
#slider { width:<?php echo (int)$width - $accordionwidth - 2; ?>px; }
#slidorion, #slider, #accordion { height:<?php echo $height; ?>px; }
#accordion > .link-content { height: <?php echo (int)$height - (41 * count($products)); ?>px; }
</style>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#slidorion').slidorion({
		speed: <?php echo (int)$params->get('speed', '1000'); ?>,
		interval: <?php echo (int)$params->get('interval', '4000'); ?>,
		effect: '<?php echo $params->get('sliderion_effect', 'slideLeft'); ?>',
		autoPlay: '<?php echo $params->get('sliderion_autoplay', '1'); ?>',
		hoverPause: '<?php echo $params->get('sliderion_hoverpause', '1'); ?>'
	});
});
</script>
<div id="slidorion" class="magebridge-slidorion magebridge-module">
<?php if (!empty($products) && is_array($products)) { ?>
	<div id="slider">
	<?php foreach ($products as $product) { ?>
		<div class="slide">
			<?php $image = $params->get('image', 'image'); ?>
			<a href="<?php echo $product['url']; ?>" title="<?php echo $product['label']; ?>"><img src="<?php 
				echo $product[$image]; ?>" title="<?php echo $product['label']; ?>" alt="<?php 
				echo $product['label']; ?>" /></a>
		</div>
	<?php } ?>
	</div>

	<div id="accordion">
	<?php foreach ($products as $product) : ?>
		<div class="link-header"><?php echo $product['name']; ?></div>
		<div class="link-content">
			<!-- content -->
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
	<?php endforeach; ?>
	</div>

<?php } else { ?>
	<?php if ($params->get('show_noitems',1)) : ?>
		<?php echo JText::_( 'MOD_MAGEBRIDGE_SLIDER_NO_PRODUCTS' ); ?>
	<?php endif; ?>
<?php } ?>
</div>
