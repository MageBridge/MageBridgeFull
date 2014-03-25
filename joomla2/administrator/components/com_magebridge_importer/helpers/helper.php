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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * MageBridge Importer Helper
 */
class MageBridgeImporterHelper
{
    /*
     * Wrapper-method to get specific widget-data with caching options
     *
     * @param string $name
     * @return mixed
     */
    static public function getWidgetData($name = null)
    {
        switch($name) {
            case 'attributeset':
                $function = 'getAttributesets';
                break;

            case 'category':
                $function = 'getCategories';
                break;

            case 'attributes':
                $function = 'getAttributes';
                break;

            default:
                return null;
        }

        $cache = JFactory::getCache('com_magebridge_importer.admin');
        $cache->setCaching(0);
        $result = $cache->call( array( 'MageBridgeImporterHelper', $function ));
        return $result;
    }

    /*
     * Get a list of attribute-sets from the API
     *
     * @param null
     * @return array
     */
    static public function getAttributesets()
    {
        return self::getApiData('magebridge_attribute.attributesets');
    }

    /*
     * Get a list of attributes from the API
     *
     * @param null
     * @return array
     */
    static public function getAttributes()
    {
        $arguments = array();
        $attributeset_id = JFactory::getSession()->get('com_magebridge_importer.attributeset_id');
        if(!empty($attributeset_id)) {
            $arguments['attributeset_id'] = $attributeset_id;
        }
        return self::getApiData('magebridge_attribute.attributes', $arguments);
    }

    /*
     * Get a list of categories from the API
     *
     * @param null
     * @return array
     */
    static public function getCategories()
    {
        return self::getApiData('magebridge_category.list');
    }

    /*
     * Get a list of attributes to skip
     *
     * @param null
     * @return array
     */
    static public function skipAttributes()
    {
        return array(
            'activation_information',
            'custom_design*',
            'custom_layout_update',
            'enable_googlecheckout',
            'featured',
            'finish',
            'gallery',
            'gift_message_available',
            'group_price',
            'image',
            'image_label',
            'is_imported',
            'is_recurring',
            'links_*',
            'media_gallery',
            'minimal_price',
            'msrp_*',
            'news_*',
            'options_container',
            'page_layout',
            'price_type',
            'price_view',
            'recurring_profile',
            'small_image',
            'special_*',
            'status',
            'tax_class_id',
            'thumbnail*',
            'url_key',
            'visibility',
            'country_of_manufacture',
        );
    }

    /*
     * Get an API-result
     *
     * @param null
     * @return array
     */
    static public function getApiData($method, $arguments = array())
    {
        $bridge = MageBridgeModelBridge::getInstance();
        $result = $bridge->getAPI($method, $arguments);
        if (empty($result)) {

            // Register this request
            $register = MageBridgeModelRegister::getInstance();
            $id = $register->add('api', $method, $arguments);

            // Build the bridge
            $bridge->build();

            // Send the request to the bridge
            $result = $register->getDataById($id);
        }

        return $result;
    }

    /*
     * Method to display the requested view
     */
    static public function addFormField($form, $attribute, $fieldset = 'basic')
    {
        $name = $attribute['code'];
        $label = $attribute['label'];
        $type = $attribute['input'];
        $options = $attribute['options'];

        if($type == 'select') {
            $type = 'list';
        } else {
            $type = 'text';
        }

        if(!empty($options)) {
            $optionsXml = '';
            foreach($options as $optionValue => $optionLabel) {
                if(empty($optionValue) && empty($optionLabel) && count($options) == 1) break;
                $optionsXml .= '<option value="'.$optionValue.'">'.htmlentities($optionLabel).'</option>'."\n";
            }
        }

        $required = ($attribute['required'] == 1) ? 'required="required" ' : '';

        $elementXml = '<fieldset name="attributegroup'.$attribute['group_value'].'" label="'.$attribute['group_label'].'">
            <field name="'.$name.'"
               type="'.$type.'"
               label="'.htmlentities($label).'"
               class="inputbox"
               '.$required.'
            >
            '.$optionsXml.'
            </field>
        </fieldset>';

        $debug = true;
        $debug = false;
        if($debug) {
            echo '<pre>'.htmlentities($elementXml).'</pre><hr/>';
        } else {
            $element = new SimpleXMLElement($elementXml);
            @$form->setField($element, 'item');
        }
    }
}
