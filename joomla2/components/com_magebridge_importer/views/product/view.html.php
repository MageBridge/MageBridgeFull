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
 * HTML View class
 *
 * @static
 * @package MageBridgeImporter
 */
class MageBridgeImporterViewProduct extends YireoViewForm
{
    /*
     * Method to display the requested view
     */
    public function display($tpl = null)
    {
        // Before loading anything, we build the bridge
        $this->preBuildBridge();

        // Set the layout
        $layout = JRequest::getCmd('layout');
        if(!empty($layout)) {
            $this->_layout = $layout;
        }

        // Allow for loading existing data by ID
        $this->fetchItem();
        
        // Fetch the attribute set ID
        $session = JFactory::getSession();
        $attributeset_id = $session->get('com_magebridge_importer.attributeset_id');
        $this->assignRef('attributeset_id', $attributeset_id);

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

    /*
     * Shortcut method to build the bridge for this page
     *
     * @param null
     * @return null
     */
    public function preBuildBridge()
    {
        // Register the needed segments
        $register = MageBridgeModelRegister::getInstance();
        $register->add('headers');
        $register->add('api', 'magebridge_attribute.attributesets');
        $register->add('api', 'magebridge_attribute.attributes');
        $register->add('api', 'magebridge_category.list');

        // Build the bridge and collect all segments
        $bridge = MageBridge::getBridge();
        $bridge->build();
    }
}

