<?php
/*
 * Joomla! component MageBridge Importer
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge Importer
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Import the needed libraries
jimport('joomla.filter.output');

/**
 * HTML View class
 */
class MageBridgeImporterViewProduct extends MageBridgeImporterView
{
    /*
     * Flag to determine whether to load the menu
     */
    protected $loadToolbar = false;
    
    /*
     * Display method
     *
     * @param string $tpl
     * @return null
     */
	public function display($tpl = null)
	{
        // Load toolbar
        JToolBarHelper::save();
        JToolBarHelper::apply();
        JToolBarHelper::custom('approve', 'copy.png', 'copy.png', 'LIB_YIREO_VIEW_TOOLBAR_APPROVE', false, true);
        JToolBarHelper::cancel();

        // Fetch this item
        $this->fetchItem();
        $attributeset_id = $this->item->attributeset_id;
        JFactory::getSession()->set('com_magebridge_importer.attributeset_id', $attributeset_id);

        // Before loading anything, we build the bridge
        $this->preBuildBridge();

        // Load the form if it's there
        $form = $this->get('Form');
        if(!empty($form)) {

            // Bind the attributeset_id
            $form->bind(array('item' => array('attributeset_id' => $attributeset_id)));

            // Fetch the Magento attributes and add them to the product-page
            $attributes = MageBridgeImporterHelper::getWidgetData('attributes');
            $skipAttributes = MageBridgeImporterHelper::skipAttributes();

            // Loop through the attributes
            foreach($attributes as $attribute) {

                // Skip certain attributes
                $skip = false;
                foreach($skipAttributes as $skipAttribute) {
                    if($skipAttribute == $attribute['code']) {
                        $skip = true;
                        break;
                    }
                    if(strstr($skipAttribute, '*') && fnmatch($skipAttribute, $attribute['code'])) {
                        $skip = true;
                        break;
                    }
                }
                if($skip) continue;

                // Skip attributes that are not in this attributeset

                // Skip attributes with no label
                if(empty($attribute['label'])) continue;

                // Loop the attribute as form-field to the form
                MageBridgeImporterHelper::addFormField($form, $attribute);
            }

            $this->assignRef('form', $form);

        }

		parent::display($tpl);
	}
}
