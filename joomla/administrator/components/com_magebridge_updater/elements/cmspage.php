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
defined('_JEXEC') or die();

// Import the MageBridge autoloader
require_once JPATH_SITE.'/components/com_magebridge/helpers/loader.php';

// Import the parent class
jimport('joomla.html.parameter.element');

/*
 * Element class for selecting Magento CMS pages
 */
class JElementCMSPage extends JElement
{
    /*
     * Name for this element
     */
    public $_name = 'Magento CMS Page';

    /*
     * Method to construct the HTML of this element
     *
     * @param string $name
     * @param string $value
     * @param object $node
     * @param string $control_name
     * @return string
     */
	public function fetchElement($name, $value, &$node, $control_name)
	{
        // Add the control name
        if (!empty($control_name)) $name = $control_name.'['.$name.']';

        // Only build a dropdown when the API-widgets are enabled
        if (MagebridgeModelConfig::load('api_widgets') == true) {

            // Fetch the widget data from the API
            $options = MageBridgeWidgetHelper::getWidgetData('cmspage');

            // Parse the result into an HTML form-field
            if (!empty($options) && is_array($options)) {
                foreach ($options as $index => $option) {

                    // Customize the return-value when the XML-attribute "output" is defined
                    if (is_object($node)) {
                        $output = $node->attributes('output');
                        if (!empty($output) && array_key_exists($output, $option)) $option['value'] = $option[$output];
                    }

                    // Customize the label
                    $option['label'] = $option['label'] . ' ('.$option['value'].') ';

                    // Add the option back to the list of options
                    $options[$index] = $option;

                    // Support the new format "[0-9]:(.*)"
                    if (preg_match('/([0-9]+)\:/', $value) == false) {
                        $v = explode(':', $option['value']);
                        if ($v[1] == $value) $value = $option['value'];
                    }
                }

                // Return a dropdown list
                array_unshift( $options, array( 'value' => '', 'label' => ''));
                return JHTML::_('select.genericlist', $options, $name, null, 'value', 'label', $value);

            // Fetching data from the bridge failed, so report a warning
            } else {
                MageBridgeModelDebug::getInstance()->warning( 'Unable to obtain MageBridge API Widget "cmspage": '.var_export($options, true));
            }
        }

        // Return a simple input-field by default
        return '<input type="text" name="'.$name.'" value="'.$value.'" />';
    }
}
