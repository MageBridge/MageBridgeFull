<?php
/**
 * Joomla! component MageBridge Importer
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge Importer
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * MageBridge Importer Controller 
 *
 * @package MageBridgeImporter
 */
class MageBridgeImporterController extends YireoController
{
    /**
     * List of allowed tasks
     *
     * @protected array
     */
    protected $_allow_tasks = array(
        'display',
        'attributeset',
        'store',
    );

    /**
     * Value of the default View to use
     *
     * @protected string
     */
    protected $_default_view = 'product';

    /**
     * Display the current page
     *
     * @access public
     * @param null
     * @return null
     */
    public function display($cachable = false, $urlparams = false)
    {
        $params = JFactory::getApplication()->getParams();
        $profile_id = (int)$params->get('profile_id');
        $attributeset_id = 0;
        $id = JRequest::getInt('id');

        if($profile_id > 0) {
            $db = JFactory::getDBO();
            $db->setQuery('SELECT `attributeset_id` FROM `#__magebridge_importer_profiles` WHERE `id`='.$profile_id.' AND `published`=1');
            $attributeset_id = $db->loadResult();
        }

        if($id > 0 || $attributeset_id > 0) {
            JRequest::setVar('layout', 'attributes');
        }

        parent::display($cachable, $urlparams);
    }

    /**
     * Handle the post of the attributeset
     *
     * @access public
     * @param null
     * @return null
     */
    public function attributeset()
    {
        $session = JFactory::getSession();
        $item = JRequest::getVar('item');

        if(!empty($item) && isset($item['attributeset_id'])) {
            $attributeset_id = $item['attributeset_id'];
            $session->set('com_magebridge_importer.attributeset_id', $attributeset_id);
        }

        if(empty($attributeset_id)) {
            $attributeset_id = $session->get('com_magebridge_importer.attributeset_id');
        }

        $this->doRedirect(null, array('layout' => 'attributes')); 
    }

    public function store($post = null)
    {
        parent::store();
        $this->doRedirect('products');
    }
}
