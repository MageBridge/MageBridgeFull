<?php 
/*
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

defined('_JEXEC') or die('Restricted access');
?>
<th width="200" class="title">
    <?php echo JHTML::_('grid.sort',  'Label', 's.label', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
<th width="160" class="title">
    <?php echo JHTML::_('grid.sort',  'Product SKU', 's.sku', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
<th width="160" nowrap="nowrap">
    <?php echo JHTML::_('grid.sort',  'Connector Name', 's.connector', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
<th width="200" nowrap="nowrap">
    <?php echo JHTML::_('grid.sort',  'Connector Value', 's.connector_value', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
