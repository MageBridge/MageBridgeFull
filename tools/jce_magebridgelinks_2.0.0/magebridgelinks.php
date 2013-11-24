<?php
/**
 * Joomla! extension MageBridge - JCE plugin
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2012
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// no direct access
defined( '_WF_EXT' ) or die( 'ERROR_403' );

class WFLinkBrowser_Magebridgelinks extends JObject {

    /*
     * Singleton
     *
     * @access public
     * @param null
     * @return self
     */
	public function &getInstance()
    {
		static $instance;

		if ( !is_object( $instance ) ){
			$instance = new WFLinkBrowser_Magebridgelinks();
		}
		return $instance;
	}
	
    /*
     * Method to check whether this plugin can be used or not
     *
     * @access public
     * @param null
     * @return boolean
     */
	public function isEnabled() 
	{
		$wf = WFEditorPlugin::getInstance();
		$enabled = $wf->checkAccess($wf->getName() . '.links.magebridgelinks.enable', 1);
        if($enabled == false) return false;

        if(!is_dir(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_magebridge')) {
            return false;
        }

        jimport('joomla.application.component.helper');
        if(JComponentHelper::isEnabled('com_magebridge') == false) {
            return false;
        }

        return true;
    }

    /*
     * Method to display extra HTML-code
     *
     * @access public
     * @param null
     * @return null
     */
	public function display()
	{
        return;
    }

    /*
     * Method to return the component-name
     *
     * @access public
     * @param null
     * @return string
     */
	public function getOption()
	{
        return array('com_magebridge');
    }

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
        if(!empty($request)) {
            return 'index.php?option=com_magebridge&view='.$view.'&request='.$request;
        } else {
            return 'index.php?option=com_magebridge&view='.$view;
        }
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
        $product_list = $register->getDataById($id);

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
        // Ugly workaround to prevent request-forwarding of POST
        $_POST = array(); 

        // Parse the request
        if(preg_match('/^([0-9]+)\:(.*)$/', $request, $match)) {
            $request = $match[2];
            $current_id = $match[1];
        } else {
            $current_id = false;
        }

        // Parse the request, if it is a Root Catalog
        if(preg_match('/^ROOT_CATALOG_([0-9]+)/', $request, $match)) {
            $current_id = $match[1];
        }

        // Get the list from the API
        if($current_id > 0) {
            $arguments = array('parentId' => $current_id);
        } elseif(!empty($request)) {
            $arguments = array('parentUrlKey' => $request);
        } else {
            $arguments = null;
        }
        $tree = MageBridgeElementHelper::getCategoryTree($arguments);
        $list = MageBridgeElementHelper::getCategoryList($tree);

        // Determine the subcategories to display
        $categories = array();
        if(!empty($request)) {

            // First loop through the categories, to get the parent ID
            $parent_id = null;
            foreach($list as $category) {

                // Check for a Root Catalog, with which we should start anyway
                if($category['parent_id'] == 1) {
                    $parent_id = $category['category_id'];
                    break;

                // Check for another category of which the ID is the current ID
                } elseif($current_id > 0 && $category['category_id'] == $current_id) {
                    $parent_id = $category['category_id'];
                    break;

                // Check for another category of which the URL-key is the current request
                } elseif($category['url_key'] == $request) {
                    $parent_id = $category['category_id'];
                    break;
                }
            }

            // Next, match the parent IDs
            foreach($list as $category) {
                if(isset($category['parent_id']) && $category['parent_id'] == $parent_id) {
                    $categories[] = $category;
                }
            }

        // Display the root-categories
        } else {
            foreach($list as $category) {
                if($category['level'] == 1) {

                    // Override the URL-key for Root Catalogs
                    if($category['parent_id'] == 1) {
                        $category['url_key'] = 'ROOT_CATALOG_'.$category['category_id'];
                    }

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
    public function getList()
    {
        $wf =& WFEditorPlugin::getInstance();
        if( $wf->checkAccess( 'advlink_magebridgelinks', '1' ) ){
            return '<li id="index.php?option=com_magebridge&view=root">'
                . '<div class="tree-row"><div class="tree-image"></div>'
                . '<span class="folder magebridgelinks nolink">'
                . '<a href="javascript:;">'.WFText::_('MAGEBRIDGE').'</a>'
                . '</span>'
                . '</div>'
                . '</li>'
            ;
        }
        return null;    
    }

    /**
     * JCE-method to get all the subnodes of this MageBridge plugin
     *
     * @access public
     * @param array $args
     * @param string $view
     * @return string
     */
    public function getLinks($args = null)
    {
        // Do not pickup non-MageBridge links
        if(!empty($args->option) && $args->option != 'com_magebridge') {
            return null;
        }

        // Get the URL-arguments
        $view = isset($args->view) ? $args->view : JRequest::getVar('view');
        $request = isset($args->request) ? $args->request : JRequest::getVar('request');
        
        // Match the catalog-pages
        if($view == 'catalog') {

            $items = array();

            // Get all the subcategories for the current request
            $categories = self::getCategories($request);
            if(!empty($categories)) {
                foreach($categories as $category) {
                    $items[] = array(
                        'id' => 'index.php?option=com_magebridge&view=catalog&layout=category&request='.$category['category_id'].':'.$category['url_key'],
                        'name' => 'Category '.$category['name'].' [ID '.$category['category_id'].']',
                        'class' => 'folder magebridgelinks'
                    );
                }
            }

            // Get all the products for the current request
            $products = self::getProducts($request);
            if(!empty($products)) {
                foreach($products as $product) {
                    $items[] = array(
                        'id' => self::getRequestUrl($product['url_key'], 'product'),
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
                    'id' => self::getRequestUrl(),
                    'name' => 'Magento homepage',
                    'class' => 'page magebridgelinks'
                ),
                array(
                    'id' => self::getRequestUrl('customer/account'),
                    'name' => 'Magento customer account',
                    'class' => 'page magebridgelinks'
                ),
                array(
                    'id' => self::getRequestUrl('wishlist'),
                    'name' => 'Magento wishlist',
                    'class' => 'page magebridgelinks'
                ),
                array(
                    'id' => self::getRequestUrl('checkout/cart'),
                    'name' => 'Magento shopping cart',
                    'class' => 'page magebridgelinks'
                ),
                array(
                    'id' => self::getRequestUrl('checkout'),
                    'name' => 'Magento checkout',
                    'class' => 'page magebridgelinks'
                ),
                array(
                    'id' => self::getRequestUrl('customer/account/login'),
                    'name' => 'Magento customer login',
                    'class' => 'page magebridgelinks'
                ),
                array(
                    'id' => self::getRequestUrl('customer/account/logout'),
                    'name' => 'Magento customer logout',
                    'class' => 'page magebridgelinks'
                ),
            );
        }

        return $items;
    }
}	
