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
?>
<?php echo $this->loadTemplate('script'); ?>
<?php $fieldsetsRight = array('category', 'attributeset'); ?>

<form method="post" name="adminForm" id="adminForm">
<div>
    <div class="width-60 fltlft">
        <?php foreach($this->form->getFieldsets() as $fieldsetCode => $fieldset): ?>
            <?php if(in_array($fieldsetCode, $fieldsetsRight)) continue; ?>
            <?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsetCode, 'legend' => $fieldset->label)); ?>
        <?php endforeach; ?>
    </div>
    <div class="width-40 fltlft">
        <?php foreach($fieldsetsRight as $fieldsetCode): ?>
            <?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsetCode)); ?>
        <?php endforeach; ?>
    </div>
</div>
<?php echo $this->loadTemplate('formend'); ?>
</form>
