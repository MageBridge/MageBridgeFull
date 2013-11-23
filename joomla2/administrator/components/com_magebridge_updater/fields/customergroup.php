<?php
/*
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2013
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('JPATH_BASE') or die();

// Import the MageBridge autoloader
require_once JPATH_SITE.'/components/com_magebridge/helpers/loader.php';

/*
 * Form Field-class for choosing a specific Magento customer-group in a selection-box
 */
class JFormFieldCustomerGroup extends JFormFieldAbstract
{
    /*
     * Form field type
     */
    public $type = 'Magento customer-group';

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

        // Only build a dropdown when the API-widgets are enabled
        if (MagebridgeModelConfig::load('api_widgets') == true) {

            // Fetch the widget data from the API
            $cache = JFactory::getCache('com_magebridge_admin');
            $cache->setCaching(0);
            $options = $cache->call( array( 'JFormFieldCustomerGroup', 'getResult' ));

            // Parse the result into an HTML form-field
            if (!empty($options) && is_array($options)) {

                foreach ($options as $index => $option) {

                    // Set default return-value
                    $option['value'] = $option['customer_group_id'];

                    // Customize the return-value when the attribute "output" is defined
                    $output = (string)$this->element['output'];
                    if (!empty($output) && array_key_exists($output, $option)) {
                        $option['value'] = $option[$output];
                    }

                    // Strip empty options (like the "NOT LOGGED IN" group)
                    if (empty($option['value']) || $option['value'] == 0) {
                        unset($options[$index]);
                        continue;
                    }

                    // Customize the label
                    $option['label'] = $option['customer_group_code'];

                    // Add the option back to the list of options
                    $options[$index] = $option;
                }

                // Return a dropdown list
                array_unshift( $options, array( 'value' => '', 'label' => ''));
                return JHTML::_('select.genericlist', $options, $name, null, 'value', 'label', $value);

            // Fetching data from the bridge failed, so report a warning
            } else {
                MageBridgeModelDebug::getInstance()->warning( 'Unable to obtain MageBridge API Widget "customer group": '.var_export($options, true));
            }
        }

        // Return a simple input-field by default
        return '<input type="text" name="'.$name.'" value="'.$value.'" />';
    }

    /*
     * Helper-method to get a list of customer-groups from the API
     *
     * @param null
     * @return array
     */
    static public function getResult()
    {
        $bridge = MageBridgeModelBridge::getInstance();
        $result = $bridge->getAPI('customer_group.list');
        if (empty($result)) {

            // Register this request
            $register = MageBridgeModelRegister::getInstance();
            $register->add('api', 'customer_group.list');

            // Send the request to the bridge
            $result = $bridge->getAPI('customer_group.list');
        }
        return $result;
    }
}
