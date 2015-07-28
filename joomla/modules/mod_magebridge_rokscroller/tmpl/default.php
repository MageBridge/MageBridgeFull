<?php // no direct access
/**
* @package RokIntroScroller
* @copyright Copyright (C) 2007 RocketWerx. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/
defined('_JEXEC') or die('Restricted access'); 
$doc = &JFactory::getDocument();
$doc->addScript(JURI::base() . 'modules/mod_magebridge_rokscroller/rokvm_scroller.js');
JHTML::_('behavior.mootools');

$direction = $params->get('direction', 'horizontal');
?>
<script type="text/javascript">
window.addEvent((window.safari) ? 'load' : 'domready', function() {
	var rvs = new RokVirtuemartScroller('<?php echo $direction; ?>-rokvmscroller', {
		'direction': '<?php echo $direction; ?>',
		'height': <?php echo $params->get('height', 300); ?>,
		'arrows': {
			'effect': <?php echo ($params->get("arrows_effect", 1)  == 0)? "false" : "true"; ?>,
			'color': '<?php echo $params->get("arrows_color", 'auto'); ?>',
			'align': 'top'
		},
		'scroll': {
			'duration': <?php echo $params->get('duration', 800); ?>,
			'amount': <?php echo $params->get('amount', 200); ?>,
			'transition': Fx.Transitions.<?php echo $params->get('fxeffect', 'Quad.easeOut'); ?>
		},
		'autoplay': {
			'enabled': <?php echo ($params->get("autoscroll", 1)  == 0)? "false" : "true"; ?>,
			'delay': <?php echo $params->get('scrolldelay', 2); ?>
		}
	});
});
</script>
<div class="<?php echo $direction; ?>-scroller-bottom">
	<div class="<?php echo $direction; ?>-scroller-bottom1">
		<div class="<?php echo $direction; ?>-scroller-bottom2">
			<div class="<?php echo $direction; ?>-scroller-top">
				<div class="<?php echo $direction; ?>-scroller-top1">
					<div class="<?php echo $direction; ?>-scroller-top2">
						<!-- Content START -->
							<div id="<?php echo $direction; ?>-rokvmscroller" class="<?php echo $params->get('moduleclass_sfx'); ?>">
							<?php foreach ($products as $product) :  ?>
							<?php 
							$product['addtocart_url'] = MageBridgeUrlHelper::route('checkout/cart/add/product/'.$product['product_id'].'/');
							?>
							<div><div class="scroll-item1"><div class="scroll-item2"><div class="scroll-item3">
								<span class="product-name"><?php echo $product['name']; ?></span><br/>
								<a href="<?php echo $product['url']; ?>" title="<?php echo $product['label']; ?>"><img src="<?php 
									echo $product['thumbnail']; ?>" title="<?php echo $product['label']; ?>" alt="<?php 
									echo $product['label']; ?>" /></a><br/>
								<span class="productPrice"><?php echo $product['price']; ?></span><br/>
								<button onClick="window.location = '<?php echo $product['addtocart_url']; ?>';" class="button"><span>Add
to cart</span></button>
							</div></div></div></div>
							<?php endforeach; ?>
							<!-- Content END -->		


							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

