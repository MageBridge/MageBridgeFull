<?php
/**
 * MageBridge data source for JSitemap
 *
 * @author Yireo
 * @copyright (C) 2015 - Yireo
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */

defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * MageBridge data source for JSitemap
 *
 * @subpackage plugins
 * @since 3.3
 */
class JMapFilePluginMageBridge implements JMapFilePlugin {

    protected $returndata = array();

	public function getSourceData(JRegistry $pluginParams, JDatabase $db, JMapModel $sitemapModel)
    {
		// Check if the extension is installed
		if(!file_exists(JPATH_SITE . '/components/com_magebridge')) {
			throw new JMapException(JText::sprintf('COM_JMAP_ERROR_EXTENSION_NOTINSTALLED', 'MageBridge'), 'warning');
		}

        // Check if the MageBridge class is available
		if(class_exists('MageBridge') == false) {
			throw new JMapException(JText::_('COM_JMAP_PLGMAGEBRIDGE_CLASS_NOT_FOUND'), 'warning');
        }
		
		// The associative array holding the returned data
		$this->returndata = array(
            'items' => array(),
            'items_tree' => array(),
            'categories_tree' => array(),
        );
		
		$user = JFactory::getUser();
		if(!is_object($user)) {
			throw new JMapException(JText::_('COM_JMAP_PLGMAGEBRIDGE_NOUSER_OBJECT'), 'warning');
		}

		// Check access-levels
		$accessLevel = $user->getAuthorisedViewLevels();
		$langTag = JFactory::getLanguage()->getTag();

        // Get the API results
        $arguments = array(
            'active' => 1,
            'include_products' => true,
        );
        $results = $this->getAPIResult($arguments);
		
		// Route links for each record
        if (!empty($results)) {
            $this->addCategory($results);
        }

        //print_r($this->returndata);exit;

		return $this->returndata;
	}

    protected function addCategory($results, $parentId = 0)
    {
        foreach($results as $category) {
                
            $categoryId = $category['entity_id'];

            if(isset($category['level']) && $category['level'] == 1) {
                $rootItem = MageBridgeUrlHelper::getRootItem();
                $category['name'] = (is_object($rootItem)) ? $rootItem->title : JText::_('COM_MAGEBRIDGE');
                $category['link'] = MageBridgeUrlHelper::route();
            }

            $data = (object)null;
            $data->category_id = $categoryId;
            $data->category_title = $category['name'];
            $data->category_link = MageBridgeUrlHelper::route($category['request_path']);
            $data->category_link = str_replace(JURI::root(), '/', $data->category_link);
            $data->lastmod = $category['updated_at'];

            $this->returndata['categories_tree'][$parentId][] = $data;

            if(!empty($category['children'])) {
                $categoryData = $this->addCategory($category['children'], $categoryId);
            }

            if(!empty($category['products'])) {
                $this->returndata['items_tree'][$categoryId] = array();
                foreach($category['products'] as $product) {
                    $this->addProduct($product, $categoryId);
                }
            }
        }
    }

    protected function addProduct($product, $categoryId = 0)
    {
        $data = (object)null;
        $data->title = $product['name'];
        $data->lastmod = $product['updated_at'];
        $data->metakey = $product['meta_keyword'];
        $data->link = MageBridgeUrlHelper::route($product['url']);
        $data->link = str_replace(JURI::root(), '', $data->link);
        if (preg_match('/^\//', $data->link) == false) {
            $data->link = '/' . $data->link;
        }

        $productId = $product['product_id'];
        $this->returndata['items'][$productId] = $data;

        if($categoryId > 0) {
            $this->returndata['items_tree'][$categoryId][] = $data;
        }
    }

    /*
     * Method to get the tree result from the API
     */
    protected function getAPIResult($arguments)
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
}
