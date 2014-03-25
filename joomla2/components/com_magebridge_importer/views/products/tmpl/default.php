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
                <?php echo JText::_('Name'); ?>
            </th>
            <th width="200">
                <?php echo JText::_('SKU'); ?>
            </th>
            <th width="160">
                <?php echo JText::_('Status'); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($this->items as $item) : ?>
        <tr>
            <td>
                <a href="<?php echo $item->edit_link; ?>" title="<?php echo JText::_('COM_MAGEBRIDGE_VIEW_STORE_ACTION_EDIT'); ?>"><?php echo $item->name; ?></a>
            </td>
            <td>
                <?php echo $item->sku; ?>
            </td>
            <td>
                <?php echo $item->status; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
