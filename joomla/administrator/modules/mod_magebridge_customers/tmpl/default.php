<?php
/**
 * Joomla! module Magento Bridge: Latest Customers
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2016
 * @license GNU Public License
 * @link https://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Get some variables
$bridge = MageBridgeModelBridge::getInstance();
?>
<table class="adminlist table table-striped" width="100%">
	<thead>
		<tr>
			<th class="title" width="30">
				<?php echo '#' ?>
			</th>
			<th class="title">
				<?php echo JText::_('MOD_MAGEBRIDGE_CUSTOMERS_NAME'); ?>
			</th>
			<th class="title">
				<?php echo JText::_('MOD_MAGEBRIDGE_CUSTOMERS_EMAIL'); ?>
			</th>
			<th class="title" width="130">
				<?php echo JText::_('MOD_MAGEBRIDGE_CUSTOMERS_DATE'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php $k = 0; ?>
	<?php if(!empty($customers)) : ?>
	<?php foreach($customers as $customer) : ?>
		<?php $url = $bridge->getMagentoAdminUrl('customer/edit/id/'.$customer['entity_id']); ?>
		<tr>
			<td>
				<a href="<?php echo $url; ?>" target="_new"><?php echo $customer['entity_id']; ?></a>
			</td>
			<td>
				<?php
				$name = '';
				if(isset($customer['firstname'])) $name .= $customer['firstname'];
				if(isset($customer['middlename'])) $name .= ' '.$customer['middlename'];
				if(isset($customer['lastname'])) $name .= ' '.$customer['lastname'];
				?>
				<a href="<?php echo $url; ?>" target="_new"><?php echo $name; ?></a>
			</td>
			<td>
				<?php echo $customer['email']; ?>
			</td>
			<td>
				<?php echo $customer['created_at']; ?>
			</td>
		</tr>
		<?php $k++; if($k == $count) break; ?>
	<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="4"><?php echo JText::_('MOD_MAGEBRIDGE_CUSTOMERS_NO_CUSTOMERS_FOUND'); ?></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
