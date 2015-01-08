<?php 
/**
 * Joomla! Yireo Lib
 *
 * @author Yireo
 * @package YireoLib
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidation');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('adminForm')))
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>

<?php echo $this->loadTemplate('script'); ?>
<?php $fieldsetsRight = array('category', 'images', 'attributeset', 'other', 'admin'); ?>

<form method="post" name="adminForm" id="adminForm">
<div class="row">
    <div class="span6">
        <?php foreach($this->form->getFieldsets() as $fieldsetCode => $fieldset): ?>
            <?php if(in_array($fieldsetCode, $fieldsetsRight)) continue; ?>
            <?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsetCode, 'legend' => $fieldset->label)); ?>
        <?php endforeach; ?>
    </div>
    <div class="span6">
        <?php foreach($fieldsetsRight as $fieldsetCode): ?>
            <?php echo $this->loadTemplate('fieldset', array('fieldset' => $fieldsetCode)); ?>
        <?php endforeach; ?>
    </div>
</div>
<?php echo $this->loadTemplate('formend'); ?>
</form>
