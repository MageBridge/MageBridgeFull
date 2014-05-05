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

<h1><?php echo $this->title; ?><?php echo JText::_('COM_MAGEBRIDGE_IMPORTER_VIEW_PRODUCT_LAYOUT_ATTRIBUTES_HEADING'); ?></h1>

<form method="post" name="importerForm" id="importerForm">
    
<?php echo $this->loadTemplate('fieldset', array('fieldset' => 'category', 'legend' => JText::_('COM_MAGEBRIDGE_IMPORTER_MODEL_PRODUCT_FIELDSET_CATEGORY'))); ?>

<?php foreach($this->form->getFieldsets() as $fieldsetCode => $fieldset): ?>

    <?php if(in_array($fieldsetCode, array('attributeset', 'category', 'images', 'other'))) : ?>
        <?php continue; ?>
    <?php endif; ?>

    <?php if(isset($fieldset->site) && $fieldset->site == 0) : ?>
        <?php continue; ?>
    <?php endif; ?>

    <?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsetCode, 'legend' => JText::_($fieldset->label))); ?>
<?php endforeach; ?>

<?php echo $this->loadTemplate('fieldset', array('fieldset' => 'images', 'legend' => JText::_('COM_MAGEBRIDGE_IMPORTER_MODEL_PRODUCT_FIELDSET_IMAGES'))); ?>

<?php echo $this->loadTemplate('fieldset', array('fieldset' => 'other', 'legend' => JText::_('COM_MAGEBRIDGE_IMPORTER_MODEL_PRODUCT_FIELDSET_OTHER'))); ?>

<input type="submit" value="<?php echo JText::_('JSAVE'); ?>">
<input type="hidden" name="item[attributeset_id]" value="<?php echo (int)$this->attributeset_id; ?>">
<?php echo $this->loadTemplate('formend'); ?>
</form>

