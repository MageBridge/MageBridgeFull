<?php 
/**
 * Joomla! Yireo Lib
 *
 * @author Yireo
 * @package YireoLib
 * @copyright Copyright (C) 2014
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Set the right image directory for JavaScipt
jimport('joomla.utilities.utility');

// Set the task for usage in formend-template
$this->_task = 'store';
?>
<?php echo $this->loadTemplate('script'); ?>

<h1><?php echo JText::_('COM_MAGEBRIDGE_IMPORTER_VIEW_PRODUCT_LAYOUT_ATTRIBUTES_HEADING'); ?></h1>

<form method="post" name="importerForm" id="importerForm">
    
<?php foreach($this->form->getFieldsets() as $fieldsetCode => $fieldset): ?>
    <?php if($fieldsetCode == 'attributeset') continue; ?>
    <?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsetCode, 'legend' => JText::_($fieldset->label))); ?>
<?php endforeach; ?>
<input type="submit" value="<?php echo JText::_('JSAVE'); ?>">
<input type="hidden" name="item[attributeset_id]" value="<?php echo (int)$this->attributeset_id; ?>">
<?php echo $this->loadTemplate('formend'); ?>
</form>
