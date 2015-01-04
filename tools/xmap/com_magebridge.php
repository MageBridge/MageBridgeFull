<?php
/*
 * Joomla! extension - MageBridge plugin for com_xmap
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

defined('_JEXEC') or die('Restricted Access');

/* 
 * xmap extension-class 0.8 for MageBridge and Xmap 1.0
 */
class xmap_com_magebridge {

    /*
     * Method called by Xmap with the parent Menu-Item and some (empty) parameters
     *
     * @access public
     * @param XmapHtml $xmap
     * @param object $parent
     * @param array $params
     * @return null
     */
    public function getTree(&$xmap, &$parent, &$params) 
    {
        // Initialize the current category ID
        $category = 0;

        // Only build a subtree for MageBridge pages
        if($parent->link != 'index.php?option=com_magebridge&view=root' && !preg_match('/index.php\?option=com_magebridge\&view=catalog\&layout=category\&request=/', $parent->link)) {
            return;
        }

        // Check if collapsing of the MageBridge Root Menu-Item is disabled
        if($parent->link == 'index.php?option=com_magebridge&view=root') {
            if(isset($params['expand_root']) && $params['expand_root'] == 0) {
                return;
            }
        }

        // Check if collapsing of the MageBridge Category Menu-Item is disabled
        if(preg_match('/index.php\?option=com_magebridge\&view=catalog\&layout=category\&request=([a-zA-Z0-9\_\-]+)/', $parent->link, $match)) {
            if((!isset($match[1]) || !is_numeric($match[1])) && isset($params['expand_category']) && $params['expand_category'] == 0) {
                return;
            }

            // Set the current category ID to the ID configured within the Menu-Item (instead of using the Root Catalog 0)
            $category = $match[1];
        }

        // Build the arguments
        $arguments = array(
            'active' => 1,
        );

        if(isset($params['enable_products']) && $params['enable_products'] == 1) {
            $arguments['include_products'] = true;
        }

        // Build the bridge
        $results = xmap_com_magebridge::getAPIResult($arguments);
        $tree = $results[0];

        // Set the current category ID to the Root Catalog (as received from the MageBridge API-response)
        if($category == 0 && $parent->link == 'index.php?option=com_magebridge&view=root') {
            $category = $results[1];
        }

        // Call the recursive function to build the categories underneath the root
        if(!empty($tree) && isset($tree['children']) && $category > 0) {
            xmap_com_magebridge::getCategoryTree($xmap, $tree['children'], $category, $params, $parent);
        }
    
        return null;
    }

    /*
     * Static method to get the right result from the API
     * 
     * @access public
     * @param XmapHtml $xmap
     * @param array $tree
     * @param int $root_category
     * @return null
     */
    public function getAPIResult($arguments)
    {
        static $apiResult = null;
        if(empty($apiResult)) {

            // Include the MageBridge register
            $register = MageBridgeModelRegister::getInstance();
            $id = $register->add('api', 'magebridge_category.tree', $arguments);

            // Include the MageBridge bridge
            $bridge = MageBridgeModelBridge::getInstance();
            $bridge->build();
            $root_category = (int)$bridge->getMageConfig('root_category');

            // Get the result
            $tree = $register->getDataById($id);

            // Return the bundled result
            $apiResult = array(
                0 => $tree,
                1 => $root_category,
            );
        }

        return $apiResult;
    }

    /*
     * Recursive method to build Xmap nodes that represent the Magento categories
     * 
     * @access public
     * @param XmapHtml $xmap
     * @param array $tree
     * @param int $parent_category
     * @param JParameter $params
     * @param object $parent_menuitem
     * @return null
     */
    public function getCategoryTree($xmap, $tree, $parent_category = 0, $params = null, $parent_menuitem = null)
    {
        // Only continue if there are still categories in this tree
        if(!empty($tree)) {
            foreach($tree as $category) {

                // Do not continue, if this is a Root Category for a different Website
                if($category['parent_id'] == 0 && $category['category_id'] != $parent_category) {
                    continue;
                }

                // Build the path of this category
                $path = (isset($category['path_id'])) ? explode('/', $category['path_id']) : array();

                // Index this category 
                if($params['enable_categories'] == 1) {
                    if($category['parent_id'] > 0 && $category['category_id'] != $parent_category && in_array($parent_category, $path)) {
                        $node = new stdclass;
                        $node->id = $category['category_id'];
                        $node->browserNav = null;
                        $node->uid = $category['category_id'];
                        $node->name = $category['name'];
                        $node->priority = (isset($params['category_priority'])) ? $params['category_priority'] : -1;
                        $node->changefreq = (isset($params['category_changefreq'])) ? $params['category_changefreq'] : -1 ;
                        $node->expandible = true;

                        if(MagebridgeModelConfig::load('use_rootmenu') == 0) {
                            $node->link = 'index.php?option=com_magebridge&amp;view=root&amp;request='.$category['url'].'&Itemid='.$parent_menuitem->id;
                        } else {
                            $node->link = 'index.php?option=com_magebridge&amp;view=root&amp;request='.$category['url'];
                        }

                        $rs = $xmap->printNode($node);
                    }
                }

                // If there are any products, include them 
                if($params['enable_products'] == 1) {
                    if($category['category_id'] == $parent_category) {
                        if(isset($category['products']) && !empty($category['products'])) {
                            $xmap->changeLevel(1);
                            xmap_com_magebridge::getProductNodes($xmap, $category['products'], $params, $parent_menuitem);
                            $xmap->changeLevel(-1);
                        }
                    }
                }

                // If there are any subcategories, include them recursively
                if(isset($category['children']) && !empty($category['children'])) {
                    $xmap->changeLevel(1);
                    xmap_com_magebridge::getCategoryTree( $xmap, $category['children'], $parent_category, $params, $parent_menuitem);
                    $xmap->changeLevel(-1);
                }
            }
        }

        return null;
    }

    /*
     * Method to build Xmap nodes that represent the Magento products
     * 
     * @access public
     * @param XmapHtml $xmap
     * @param array $products
     * @param JParameter $params
     * @param object $parent
     * @return null
     */
    public function getProductNodes($xmap, $products, $params = null, $parent = null)
    {
        if(!empty($products)) {
            foreach($products as $product) {
                $node = new stdclass;
                $node->id = $product['product_id'];
                $node->browserNav = null;
                $node->uid = $product['product_id'];
                $node->name = $product['name'];
                $node->priority = (isset($params['product_priority'])) ? $params['product_priority'] : -1;
                $node->changefreq = (isset($params['product_changefreq'])) ? $params['product_changefreq'] : -1;
                if(MagebridgeModelConfig::load('use_rootmenu') == 0) {
                    $node->link = 'index.php?option=com_magebridge&amp;view=root&amp;request='.$product['url_key'].'&Itemid='.$parent->id;
                } else {
                    $node->link = 'index.php?option=com_magebridge&amp;view=root&amp;request='.$product['url_key'];
                }
                $node->expandible = true;
                $rs = $xmap->printNode($node);
            }
        }
    }
}
