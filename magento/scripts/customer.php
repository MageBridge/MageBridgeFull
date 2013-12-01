<?php
require_once '../app/Mage.php';
Mage::app(1, 'website');

$customerId = 55;
$customer = Mage::getModel('customer/customer')->load($customerId);
Mage::dispatchEvent('customer_save_after', array('customer' => $customer));
echo "\n";
