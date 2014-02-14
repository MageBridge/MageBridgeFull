<?php
require_once '../app/Mage.php';
Mage::app();

$collection = Mage::getModel('catalog/category')->getCollection()
    ->setStoreId($storeId)
    ->addUrlRewriteToResult()
    ->addAttributeToSelect('name')
    ->addAttributeToSelect('url_key')
    ->addAttributeToSelect('is_active')
    ->addAttributeToSelect('include_in_menu')
    ->addAttributeToSelect('image')
;

$collection->addAttributeToFilter('name', array('like' => array('%came%')));

foreach($collection as $category) {
    echo $category->getName()."\n";
}
