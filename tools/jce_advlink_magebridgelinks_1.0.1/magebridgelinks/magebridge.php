<?php
/**
 * Joomla! 1.5 extension MageBridge - JCE plugin
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2010
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// no direct access
defined( '_JCE_EXT' ) or die( 'Restricted access' );

/* Class definition */
class AdvlinkMagebridge 
{
    /**
     * Helper method to get the MageBridge URL for a specific request
     *
     * @access public
     * @param string $request
     * @param string $view
     * @return string
     */
    public function getRequestUrl($request = null, $view = 'root')
    {
        return 'index.php?option=com_magebridge&view='.$view.'&request='.$request;
    }

    /**
     * Helper method to get a product listing for a specific Magento category
     *
     * @access public
     * @param string $category_url_key
     * @return array
     */
    public function getProducts($category_url_key = null)
    {
        // Do not build a list if the category URL Key is empty
        if(empty($category_url_key)) {
            return null;
        }

        // Build the arguments
        $arguments = array(
            'category_url_key' => $category_url_key,
        );

        // Register this request
        $register = MageBridgeModelRegister::getInstance();
        $id = $register->add('api', 'magebridge_product.list', $arguments);

        // Send the request to the bridge
        $bridge = MageBridgeModelBridge::getInstance();
        $bridge->build();
        $product_list = $bridge->getAPI('magebridge_product.list', $arguments);
        return $product_list;
    }

    /**
     * Helper method to get a listing of Magento categories
     *
     * @access public
     * @param string $request
     * @return array
     */
    public function getCategories($request = null)
    {
        // Get the list from the API
        $_POST = array(); // @todo: Ugly workaround to prevent request-forwarding of POST
        $tree = MageBridgeElementHelper::getCategoryTree();
        $list = MageBridgeElementHelper::getCategoryList($tree);

        // Determine the subcategories to display
        $categories = array();
        if(!empty($request)) {

            // First loop through the categories, to get the parent ID
            foreach($list as $category) {
                if($category['url_key'] == $request) {
                    $parent_id = $category['category_id'];
                }
            }

            // Next, match the parent IDs
            foreach($list as $category) {
                if($category['parent_id'] == $parent_id) {
                    $categories[] = $category;
                }
            }

        // Display the root-categories
        } else {
            foreach($list as $category) {
                if($category['level'] == 1) {
                    $categories[] = $category;
                }
            }
        }

        return $categories;
    }

    /**
     * JCE-method to get the first node of this MageBridge plugin
     *
     * @access public
     * @param null
     * @return string
     */
    public function getOptions()
    {
        $advlink =& AdvLink::getInstance();
        $list = '';
        if( $advlink->checkAccess( 'advlink_magebridgelinks', '1' ) ){
            $list = '<li id="index.php?option=com_magebridge&view=root">'
                . '<div class="tree-row"><div class="tree-image"></div>'
                . '<span class="folder magebridgelinks nolink">'
                . '<a href="javascript:;">'.JText::_('MAGEBRIDGE').'</a>'
                . '</span>'
                . '</div>'
                . '</li>'
            ;
        }
        return $list;    
    }

    /**
     * JCE-method to get all the subnodes of this MageBridge plugin
     *
     * @access public
     * @param array $args
     * @param string $view
     * @return string
     */
    public function getItems($args = null)
    {
        //$advlink =& AdvLink::getInstance();
        //$params = $advlink->getPluginParams();

        // Get the URL-arguments
        $view = isset($args->view) ? $args->view : JRequest::getVar('view');
        $request = isset($args->request) ? $args->request : JRequest::getVar('request');
        
        // Match the catalog-pages
        if($view == 'catalog') {

            // Get all the subcategories for the current request
            $categories = AdvlinkMagebridge::getCategories($request);
            if(!empty($categories)) {
                foreach($categories as $category) {
                    $items[] = array(
                        'id' => 'index.php?option=com_magebridge&view=catalog&layout=category&request='.$category['url_key'],
                        'name' => 'Category '.$category['name'].' [ID '.$category['category_id'].']',
                        'class' => 'folder magebridgelinks'
                    );
                }
            }

            // Get all the products for the current request
            $products = AdvlinkMagebridge::getProducts($request);
            if(!empty($products)) {
                foreach($products as $product) {
                    $items[] = array(
                        'id' => AdvlinkMagebridge::getRequestUrl($product['url_key'], 'product'),
                        'name' => $product['name'].' [SKU: '.$product['sku'].']',
                        'class' => 'file magebridgelinks'
                    );
                }
            }

        // Default listing of MageBridge pages
        } else {
            $items = array(
                array(
                    'id' => 'index.php?option=com_magebridge&view=catalog',
                    'name' => 'Magento category/product',
                    'class' => 'folder magebridgelinks'
                ),
                array(
                    'id' => AdvlinkMagebridge::getRequestUrl(),
                    'name' => 'Magento homepage',
                    'class' => 'page magebridgelinks'
                ),
                array(
                    'id' => AdvlinkMagebridge::getRequestUrl('customer/account'),
                    'name' => 'Magento customer account',
                    'class' => 'page magebridgelinks'
                ),
                array(
                    'id' => AdvlinkMagebridge::getRequestUrl('wishlist'),
                    'name' => 'Magento wishlist',
                    'class' => 'page magebridgelinks'
                ),
                array(
                    'id' => AdvlinkMagebridge::getRequestUrl('checkout/cart'),
                    'name' => 'Magento shopping cart',
                    'class' => 'page magebridgelinks'
                ),
                array(
                    'id' => AdvlinkMagebridge::getRequestUrl('checkout'),
                    'name' => 'Magento checkout',
                    'class' => 'page magebridgelinks'
                ),
                array(
                    'id' => AdvlinkMagebridge::getRequestUrl('customer/account/login'),
                    'name' => 'Magento customer login',
                    'class' => 'page magebridgelinks'
                ),
                array(
                    'id' => AdvlinkMagebridge::getRequestUrl('customer/account/logout'),
                    'name' => 'Magento customer logout',
                    'class' => 'page magebridgelinks'
                ),
            );
        }

        return $items;
    }
}
