<?php
/*
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('JPATH_BASE') or die();

// Import the MageBridge autoloader
require_once JPATH_SITE.'/components/com_magebridge/helpers/loader.php';

/*
 * Form Field-class for choosing a Magento category
 */
class JFormFieldMcategorylist extends JFormFieldAbstract
{
    /*
     * Form field type
     */
    public $type = 'Magento category';

    /*
     * Method to get the HTML of this element
     *
     * @param null
     * @return string
     */
	protected function getInput()
	{
        $name = $this->name;
        $fieldName = $name;
        $value = $this->value;

        // Check for access
        $access = (string)$this->element['access'];
        if(!empty($access)) {
            $user = JFactory::getUser();
            if ($user->authorise($access) == false) {
                return '<input type="text" name="'.$fieldName.'" value="'.$value.'" disabled="disabled" />';
            }
        }

        // Only build a dropdown when the API-widgets are enabled
        if (MagebridgeModelConfig::load('api_widgets') == true) {

            // Fetch the widget data from the API
            $options = MageBridgeImporterHelper::getWidgetData('category');

            // Parse the result into an HTML form-field
            if (!empty($options) && is_array($options)) {
                foreach ($options as $index => $option) {

                    // Skip certain categories
                    $skip = false;
                    if($option['level'] < 2) $skip = true;
                    if(empty($option['name'])) $skip = true;
                    if($option['is_active'] == 0) $skip = true;
                    if($skip == true) {
                        unset($options[$index]);
                        continue;
                    }
        
                    // Set the basic values
                    $option['value'] = $option['entity_id'];
                    $option['label'] = $option['name'];

                    // Customize the return-value when the attribute "output" is defined
                    $output = (string)$this->element['output'];
                    if (!empty($output) && array_key_exists($output, $option)) {
                        $option['value'] = $option[$output];
                    }

                    // Customize the label
                    $prefix = str_repeat('- ', $option['level'] - 2);
                    $option['label'] = $prefix.' '.$option['label'] . ' [ID '.$option['value'].'] ';

                    // Add the option back to the list of options
                    $options[$index] = $option;
                }

                // Return a dropdown list
                //array_unshift( $options, array( 'value' => '', 'label' => ''));
                return JHTML::_('select.genericlist', $options, $fieldName, null, 'value', 'label', $value);

            // Fetching data from the bridge failed, so report a warning
            } else {
                MageBridgeModelDebug::getInstance()->warning( 'Unable to obtain MageBridge API Widget "mcategorylist": '.var_export($options, true));
            }
        }

        // Return a simple input-field by default
        return '<input type="text" name="'.$fieldName.'" value="'.$value.'" />';
    }
}
