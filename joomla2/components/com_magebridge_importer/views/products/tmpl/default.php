<?php 
/*
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

defined('_JEXEC') or die('Restricted access');
?>
<table width="100%" cellpadding="5">
    <thead>
        <tr>
            <th>
                <?php echo JText::_('COM_MAGEBRIDGE_IMPORTER_MODEL_PRODUCT_FIELD_NAME'); ?>
            </th>
            <th width="200">
                <?php echo JText::_('COM_MAGEBRIDGE_IMPORTER_MODEL_PRODUCT_FIELD_SKU'); ?>
            </th>
            <th width="160">
                <?php echo JText::_('COM_MAGEBRIDGE_IMPORTER_MODEL_PRODUCT_FIELD_STATUS'); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($this->items as $item) : ?>
        <tr>
            <td>
                <?php if(!in_array($item->status, array('approved', 'submitted'))) : ?>
                <a href="<?php echo $item->edit_link; ?>" title="<?php echo JText::_('COM_MAGEBRIDGE_VIEW_STORE_ACTION_EDIT'); ?>"><?php echo $item->name; ?></a>
                <?php else: ?>
                <?php echo $item->name; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php echo $item->sku; ?>
            </td>
            <td>
                <?php echo JText::_('COM_MAGEBRIDGE_IMPORTER_MODEL_STATUS_'.$item->status); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
