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
		jQuery('.magebridge-carousel .carousel').carousel();
	});
</script>
<div class="magebridge-carousel magebridge-module">
	<div id="magebridge-carousel" class="carousel slide" data-ride="carousel">
		<?php if (!empty($products) && is_array($products)) : ?>
			<ol class="carousel-indicators">
				<?php $i = 1; ?>
				<?php foreach ($products as $product): ?>
					<li data-target="#magebridge-carousel" data-slide-to="<?php echo $i; ?>"></li>
					<?php $i++; ?>
				<?php endforeach; ?>
			</ol>
			<div class="carousel-inner" role="listbox">
				<?php $i = 1; ?>
				<?php foreach ($products as $product): ?>
					<?php $itemClass = array('item'); ?>
					<?php if($i == 1) $itemClass[] = 'active'; ?>
					<div class="<?php echo implode(' ', $itemClass); ?>">
						<?php $image = $params->get('image', 'image'); ?>
						<a href="<?php echo $product['url']; ?>">
							<img src="<?php echo $product[$image]; ?>" title="<?php echo $product['label']; ?>" alt="<?php echo $product['label']; ?>" />
						</a>

						<?php if ($params->get('show_title',1)) : ?>
							<div class="carousel-caption">
								<span class="title">
									<a href="<?php echo $product['url']; ?>">
										<?php echo $product['name']; ?>
										<?php if ($params->get('show_price',1)) : ?>
											&nbsp; (<?php echo $product['price']; ?>)
										<?php endif; ?>
									</a>
								</span>
							</div>
						<?php endif; ?>
					</div>
					<?php $i++; ?>
				<?php endforeach; ?>
			</div>

			<a class="left carousel-control" href="#magebridge-carousel" role="button" data-slide="prev">
				<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</a>

			<a class="right carousel-control" href="#magebridge-carousel" role="button" data-slide="next">
				<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
		<?php else: ?>
			<?php if ($params->get('show_noitems',1)) : ?>
				<?php echo JText::_( 'MOD_MAGEBRIDGE_SLIDER_NO_PRODUCTS' ); ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>