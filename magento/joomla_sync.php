<?php
/*
 * Yireo script to sync entries between ChronoForm & Magento
 *
 * @author Jisse Reitsma (Yireo)
 * @copyright Copyright (c) 2014 Yireo (http://www.yireo.com/)
 * @license Open Source License
 */

// Joomla! settings
$joomla_db = 'hifi_hifiathome';
$joomla_username = 'hifi_hifiathome';
$joomla_password = 'Hwk37Jt4&yw%bGw21M';
$joomla_table = 'JBLA_magebridge_importer_products';
$joomla_table_values = 'JBLA_magebridge_importer_product_values';
$joomla_image_folder = dirname(dirname(__FILE__)).'/';

// Skip fields
$skip_fields = array(
    'id',
    'status',
    'created',
    'created_by',
    'modified',
    'modified_by',
    'params',
);

// Default fields
$default_fields = array(
    'tax_class_id' => 1,
    'price' => 1,
);

// Other options
$website_id = 1;
$store_id = 1;

// Connect to Joomla!
$link = mysql_connect('localhost', $joomla_username, $joomla_password);
if (!$link) die("Could not connect: " . mysql_error()."\n");
$db = mysql_select_db($joomla_db, $link);

// Fetch all pending entries
$rows = array();
$query = 'SELECT * FROM `'.$joomla_table.'` WHERE `status`="approved"';
$result = mysql_query($query) or die('Query failed: '.mysql_error()."\n");
if (mysql_num_rows($result) == 0) die("No approved products to be submitted\n");

// Loop through the rows
while ($row = mysql_fetch_assoc($result)) {

    // Fetch values of this row
    $query = 'SELECT * FROM `'.$joomla_table_values.'` WHERE `product_id`='.$row['id'];
    $result = mysql_query($query) or die('Query failed: '.mysql_error()."\n");
    if (mysql_num_rows($result) > 0) {
        while ($valueRow = mysql_fetch_assoc($result)) {
            $name = $valueRow['name'];
            $value = $valueRow['value'];
            if(!empty($value)) {
                $tmpValue = @unserialize($value);
                if(!empty($tmpValue)) $value = $tmpValue;
            }

            if(!empty($name)) {
                $row[$name] = $value; 
            }
        }
    }

    $rows[] = $row;
}

if(empty($rows)) {
    die("No pending entries\n");
}

// Initialize Magento
require_once 'app/Mage.php';
Mage::app();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

// Create products
foreach($rows as $row) {

    // Initialize the product
    $product = Mage::getModel('catalog/product');
    
    // Load the previous product based upon the SKU
    $productId = Mage::getModel('catalog/product')->getIdBySku($row['sku']);
    if($productId > 0) $product->load($productId);

    // Determine the attributeset
    if(!empty($row['attributeset_id'])) {
        $attribute_set_id = (int)$row['attributeset_id'];
    } else {
        $attribute_set_id = $product->getDefaultAttributeSetId();
    }

    // If this is a new product, initialize it
    if($product->getId() < 1) {

        if(!empty($row['product_type'])) {
            $typeId = $row['product_type'];
        } else {
            $typeId = Mage_Catalog_Model_Product_Type::TYPE_SIMPLE;
        }

        $product->setData($product->getData())
            ->setId(null)
            ->setCreatedAt(null)
            ->setUpdatedAt(null)
            ->setAttributeSetId($attribute_set_id)
        ;
        $product->setTypeId($typeId);
    }

    // Options
    $product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
    $product->setWebsiteIds(array($website_id));

    // Set the default data
    foreach($default_fields as $field_name => $field_value) {
        $product->setData($field_name, $field_value);
    }

    // Set the data
    $images = array();
    foreach($row as $row_name => $row_value) {

        // Skip certain fields
        if(in_array($row_name, $skip_fields)) continue;
        
        // Gather images and skip them
        if(preg_match('/^image_([0-9]+)/', $row_name)) {
            $image = $joomla_image_folder.$row_value;
            if(is_file($image)) {
                $images[] = $image;
                continue;
            }
        } 
        
        // Convert the name or value
        switch($row_name) {
            case 'product_name':
                $row_name = 'name';
                break;

            case 'category':
                $category_parts = explode(' - ', $row_value);
                $current_category_id = null;
                $row_value = array();
                foreach($category_parts as $category_name) {
                    $category_name = trim($category_name);
                    $category_id = getCategoryIdByName($category_name, $current_category_id);
                    if(!empty($category_id)) {
                        $row_value[] = $category_id;
                        $current_category_id = $category_id;
                    }
                }
                $row_name = 'category_ids';
                break;
        }

        // Set the data
        $product->setData($row_name, $row_value);
    }

    // Save the images
    if(!empty($images)) {
        removeImages($product);
        $i = 0;
        foreach($images as $image) {
            if($i == 0) {
                $types = array('small_image', 'image', 'thumbnail');
            } else {
                $types = null;
            }
            $product = $product->addImageToMediaGallery($image, $types, false, false);
            $i++;
        }
    }

    // Save product 
    $product->save();

    // Set approved in this table
    $query = 'UPDATE `'.$joomla_table.'` SET `status`="submitted" WHERE `id`='.(int)$row['id'];
    mysql_query($query);
}

// Function to find a category by name
function getCategoryIdByName($category_name, $parent_id = null) {
    static $collection = null;
    if(empty($collection)) {
        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSort('name')
        ;
    }

    foreach($collection as $category) {
        if($category->getName() == $category_name) {
            if($parent_id > 0) {
                if($parent_id == $category->getParentId()) {
                    return $category->getId();
                }
            } else {
                return $category->getId();
            }
        }
    }
}

// Function to remove all images
function removeImages($product) {
    $galleryImages = $product->getMediaGalleryImages();
    if(is_object($galleryImages) && $galleryImages->count() > 0) {
        $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
        $mediaGalleryAttribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode($entityTypeId, 'media_gallery');
        foreach ($galleryImages as $galleryImage) {
            $mediaGalleryAttribute->getBackend()->removeImage($product, $galleryImage->getFile());
        }
        $product->save();
    }
}

// Finalize
