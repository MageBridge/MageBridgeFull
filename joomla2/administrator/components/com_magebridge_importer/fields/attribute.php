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
 * Form Field-class for choosing a specific Magento attribute-set from a dropdown
 */
class JFormFieldAttribute extends JFormFieldList
{
    /*
     * Form field type
     */
    public $type = 'Magento attribute';

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
            $options = MageBridgeImporterHelper::getWidgetData('attributes');
            $skipAttributes = MageBridgeImporterHelper::skipAttributes();

            // Parse the result into an HTML form-field
            if (!empty($options) && is_array($options)) {
                foreach ($options as $index => $option) {

                    // Skip attributes
                    if(in_array($option['code'], $skipAttributes)) {
                        unset($options[$index]);
                        continue;
                    }

                    // Customize the return-value when the attribute "output" is defined
                    $output = (string)$this->element['output'];
                    if (!empty($output) && array_key_exists($output, $option)) {
                        $option['value'] = $option[$output];
                    } else {
                        $option['value'] = $option['code'];
                    }

                    // Customize the label
                    $option['label'] = $option['label'] . ' ('.$option['code'].') ';

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

                // Construct the extra arguments
                $attributes = array();
                $multiple = (string)$this->element['multiple'];
                if(!empty($multiple)) {
                    $attributes[] = 'multiple="multiple"';
                    $fieldName = $fieldName.'[]';
                }

                // Add the size
                $size = (int)$this->element['size'];
                if(!empty($size)) $attributes[] = 'size="'.$size.'"';

                // Return a dropdown list
                return JHTML::_('select.genericlist', $options, $fieldName, implode(' ',$attributes), 'value', 'label', $value);

            // Fetching data from the bridge failed, so report a warning
            } else {
                MageBridgeModelDebug::getInstance()->warning( 'Unable to obtain MageBridge API Widget "attribute": '.var_export($options, true));
            }
        }

        // Return a simple input-field by default
        return '<input type="text" name="'.$fieldName.'" value="'.$value.'" />';
    }
}
