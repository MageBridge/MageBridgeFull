<?php 
/*
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2013
 * @license GNU Public License
 * @link http://www.yireo.com
 */

defined('_JEXEC') or die('Restricted access');
?>
<th width="200" class="title">
    <?php echo JHTML::_('grid.sort',  'Source', 'source', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
<th width="200" class="title">
    <?php echo JHTML::_('grid.sort',  'Destination', 'destination', $this->lists['order_Dir'], $this->lists['order'] ); ?>
</th>
