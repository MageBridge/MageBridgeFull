<?php
/**
 * Magento Bridge
 *
 * @author Yireo
 * @package Magento Bridge
 * @copyright Copyright 2015
 * @license Yireo EULA (www.yireo.com)
 * @link http://www.yireo.com
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.event.plugin');

class plgFlexicontent_fieldsMageBridgeProduct extends JPlugin
{
    public function __construct($subject, $params) 
    {
		parent::__construct( $subject, $params );
        JPlugin::loadLanguage('plg_flexicontent_fields_magebridgeproduct', JPATH_ADMINISTRATOR);
    }

	public function onDisplayField(&$field, $item)
	{
		// Execute the code only if the field type match the plugin type
		if($field->field_type != 'magebridgeproduct') return;

        // Initialize system variables
		$document =& JFactory::getDocument();
		$application =& JFactory::getApplication();

        // Set the label
		$field->label = JText::_($field->label);

		// Initialize the value
		if(is_array($field->value)) $field->value = $field->value[0];
		
        // Call the JElement class to generate output
        $node = array('return' => 'id');
        require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_magebridge'.DS.'elements'.DS.'product.php';
        $field->html = JElementProduct::fetchElement($field->name, $field->value, $node);
	}

	public function onDisplayFieldValue(&$field, $item, $values = null, $prop = 'display')
	{
		// Execute the code only if the field type match the plugin type
		if($field->field_type != 'magebridgeproduct') return;

        // Get the needed variables
        $product_id = (int)$field->value[0];

        // Set the Magento URL accordingly
        MageBridgeUrlHelper::getRequest('catalog/product/view/id/'.$product_id);

		// Determine the right block-text
		$block = $field->parameters->get('block', 'addtocart') ;
        switch($block) {

            case 'addtocart':
                $addtocartUrl = 'checkout/cart/add/product/'.$product_id.'/uenc/'.base64_encode(MageBridgeUrlHelper::route('checkout/cart'));
                $variables = array('addtocart_url' => MageBridgeUrlHelper::route($addtocartUrl));
                $text = $this->getLayoutFile('addtocart', $variables);
                break;

            case 'content':
                $text = '{{block type="catalog/product_view" name="product.info.addtocart" product_id="'.$product_id.'" template="catalog/product/view/addtocart.phtml"}}';
                $text = $this->getFilteredText($text, $field->parameters);
                break;

            default:
                $text = $this->getBlock($block, $field->parameters);
                break;
        }

		// Initialise property
		$field->{$prop} = $text;
	}

    /*
     * Helper method to fetch a CMS-based text from Magento
     *
     * @access protected
     * @param string $text
     * @return string
     */
    protected function getBlock($block = null, $params)
    {
        $bridge = MageBridgeModelBridge::getInstance();
        $register = MageBridgeModelRegister::getInstance();

        $id = $register->add('block', $block);
        $bridge->build();

        // Load CSS if needed
        if($params->get('load_css', 1) == 1) {
            $bridge->setHeaders('css');
        }

        // Load JavaScript if needed
        if($params->get('load_js', 1) == 1) {
            $bridge->setHeaders('js');
        }

		// Initialise property
        return $bridge->getBlock($block);
    }

    /*
     * Helper method to fetch a layout file from this plugin
     *
    }

    /*
     * Helper method to fetch a CMS-based text from Magento
     *
     * @access protected
     * @param string $text
     * @return string
     */
    protected function getFilteredText($text = null, $params)
    {
        if(empty($text)) {
            return null;
        }

        $bridge = MageBridgeModelBridge::getInstance();
        $key = md5($text);
        $text = MageBridgeEncryptionHelper::base64_encode($text);
        $bridge->register('headers');
        $segment_id = $bridge->register('filter', $key, $text);
        $bridge->build();

        // Load CSS if needed
        if($params->get('load_css', 1) == 1) {
            $bridge->setHeaders('css');
        }

        // Load JavaScript if needed
        if($params->get('load_js', 1) == 1) {
            $bridge->setHeaders('js');
        }

		// Initialise property
        $result = $bridge->getSegmentData($segment_id);
        $result = MageBridgeEncryptionHelper::base64_decode($result);
        return $result;
    }

    /*
     * Helper method to fetch a layout file from this plugin
     *
     * @access protected
     * @param string $layout
     * @param array $variables
     * @return string
     */
    protected function getLayoutFile($layout = null, $variables = array())
    {
        // Return if empty
        if(empty($layout)) {
            return null;
        }

        // Define the variables as local
        if(!empty($variables)) {
            foreach($variables as $name => $value) {
                $$name = $value;
            }
        }

        // Load the template script (and allow for overrides)
        jimport('joomla.filesystem.path');
        $template_dir = JPATH_THEMES.DS.JFactory::getApplication()->getTemplate();
        $layout_file = $template_dir.DS.'html'.DS.'plg_magebridgeproduct'.DS.$layout.'.php';
        if(!is_file($layout_file) || !is_readable($layout_file)) {
            $layout_file = dirname(__FILE__).DS.'magebridgeproduct'.DS.$layout.'.php';
        }

        // Read the template
        ob_start();
        include $layout_file;
        $output = ob_get_contents();
        ob_end_clean();

        // Return the output
        return $output;
    }
}
