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

/*
 * Form Field-class for generating a list of product-status
 */
class JFormFieldProductstatus extends JFormFieldAbstract
{
    /*
     * Form field type
     */
    public $type = 'Product status';

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
        $disabled = false;

        // Check for access
        $user = JFactory::getUser();
        $app = JFactory::getApplication();

        // Construct the options
        $options = array(
            array('value' => 'pending', 'label' => JText::_('COM_MAGEBRIDGE_IMPORTER_MODEL_STATUS_PENDING')),
            array('value' => 'ready', 'label' => JText::_('COM_MAGEBRIDGE_IMPORTER_MODEL_STATUS_READY')),
        );

        if($app->isSite()) {
            if(!empty($value) && !in_array($value, array('pending', 'ready'))) {
                $disabled = true;
            }
        }

        if($app->isAdmin()) {
            if(!empty($value) && in_array($value, array('submitted'))) {
                $disabled = true;
            }
            $options[] = array('value' => 'approved', 'label' => JText::_('COM_MAGEBRIDGE_IMPORTER_MODEL_STATUS_APPROVED'));
        }

        if($disabled == true) {
            $value = JText::_('COM_MAGEBRIDGE_IMPORTER_MODEL_STATUS_'.$value);
            return '<input type="text" disabled="disabled" readonly value="'.$value.'" />';
        }

        // Return a dropdown list
        return JHTML::_('select.genericlist', $options, $fieldName, null, 'value', 'label', $value);
    }
}
