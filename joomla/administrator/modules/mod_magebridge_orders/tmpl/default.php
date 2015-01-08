<?php
/**
 * Joomla! module Magento Bridge: Latest Orders
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Get some variables
$bridge = MageBridgeModelBridge::getInstance();
?>
<table class="adminlist table table-striped" width="100%">
    <thead>
        <tr>
            <th class="title">
                <?php echo JText::_('MOD_MAGEBRIDGE_ORDERS_ORDER_ID'); ?>
            </th>
            <th class="title">
                <?php echo JText::_('MOD_MAGEBRIDGE_ORDERS_NAME'); ?>
            </th>
            <th class="title">
                <?php echo JText::_('MOD_MAGEBRIDGE_ORDERS_DATE'); ?>
            </th>
            <th class="title">
                <?php echo JText::_('MOD_MAGEBRIDGE_ORDERS_AMOUNT'); ?>
            </th>
        </tr>
    </thead>
    <tbody>
    <?php $k = 0; ?>
    <?php if(!empty($orders)) : ?>
    <?php foreach($orders as $order) : ?>
        <?php $url = $bridge->getMagentoAdminUrl('sales_order/view/order_id/'.$order['entity_id']); ?>
        <tr>
            <td width="110">
                <a href="<?php echo $url; ?>" target="_new"># <?php echo $order['increment_id']; ?></a>
            </td>
            <td>
                <?php
                $name = '';
                if(isset($order['customer_firstname'])) $name .= $order['customer_firstname'];
                if(isset($order['customer_middlename'])) $name .= ' '.$order['customer_middlename'];
                if(isset($order['customer_lastname'])) $name .= ' '.$order['customer_lastname'];
                if(isset($order['customer_email'])) $name .= ' ('.$order['customer_email'].')';
                echo $name;
                ?>
            </td>
            <td width="150">
                <?php echo $order['created_at']; ?>
            </td>
            <td width="110">
                <?php echo $order['base_grand_total_formatted']; ?>
            </td>
        </tr>
        <?php $k++; if($k == $count) break; ?>
    <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4"><?php echo JText::_('MOD_MAGEBRIDGE_ORDERS_NO_ORDERS_FOUND'); ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
