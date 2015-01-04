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
<th class="title">
    <?php echo JHTML::_('grid.sort',  'Description', 'description', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
<th width="160" class="title">
    <?php echo JHTML::_('grid.sort',  'Joomla! usergroup', 'joomla_group', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
<th width="160" class="title">
    <?php echo JHTML::_('grid.sort',  'Magento customergroup', 'magento_group', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
