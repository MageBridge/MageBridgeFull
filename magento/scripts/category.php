<?php
require_once 'app/Mage.php';
Mage::app();

$root = Mage::getModel('catalog/category')->load(3);
print_r($root->debug());
echo "\n";
