<?php
/**
 * Magento Bridge
 *
 * @author Yireo
 * @package Magento Bridge
 * @copyright Copyright 2015
 * @license Yireo EULA (www.yireo.com)
 * @link http://www.yireo.com
 */
?>
<div class="add-to-cart">
<form method="post" action="<?php echo $addtocart_url; ?>">
    <label for="qty"><?php echo JText::_('Qty:'); ?></label>
    <input type="text" name="qty" id="qty" maxlength="12" value="1" title="<?php echo JText::_('Qty'); ?>" class="input-text qty" />
    <button type="button" title="<?php echo JText::_('Add to Cart'); ?>" class="button btn-cart" onclick="this.form.submit()"><span><span><?php echo JText::_('Add to Cart') ?></span></span></button>
</form>
</div>
