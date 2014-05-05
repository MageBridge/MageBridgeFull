<?php
/*
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('JPATH_BASE') or die();

// Import the MageBridge autoloader
require_once JPATH_SITE.'/components/com_magebridge/helpers/loader.php';

// Import other libraries
require_once JPATH_ADMINISTRATOR.'/components/com_magebridge_importer/helpers/helper.php';
include_once JPATH_LIBRARIES.'/joomla/form/fields/list.php';

/*
 * Form Field-class for choosing a specific Magento attributegroup from a dropdown
 */
class JFormFieldAttributegroup extends JFormFieldList
{
    /*
     * Form field type
     */
    public $type = 'Magento attributegroup';

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
            $options = MageBridgeImporterHelper::getWidgetData('attributegroups');

            // Parse the result into an HTML form-field
            if (!empty($options) && is_array($options)) {
                foreach ($options as $index => $option) {

                    // Customize the return-value when the attribute "output" is defined
                    $output = (string)$this->element['output'];
                    if (!empty($output) && array_key_exists($output, $option)) {
                        $option['value'] = $option[$output];
                    }

                    // Customize the label
                    $option['label'] .= ' - '.$option['attributeset_name'] . ' ('.$option['value'].') ';

                    // Add the option back to the list of options
                    $options[$index] = $option;
                }

                $parentOptions = parent::getOptions();
                if(!empty($parentOptions)) {
                    foreach($parentOptions as $parentOption) {
                        $parentOption->label = $parentOption->text;
                        $options[] = $parentOption;
                    }
                }

                // Return a dropdown list
                return JHTML::_('select.genericlist', $options, $fieldName, null, 'value', 'label', $value);

            // Fetching data from the bridge failed, so report a warning
            } else {
                MageBridgeModelDebug::getInstance()->warning( 'Unable to obtain MageBridge API Widget "attributegroup": '.var_export($options, true));
            }
        }

        // Return a simple input-field by default
        return '<input type="text" name="'.$fieldName.'" value="'.$value.'" />';
    }
}
