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
$this->_task = 'attributeset';
?>
<?php echo $this->loadTemplate('script'); ?>

<form method="post" name="importerForm" id="importerForm">
<?php echo $this->loadTemplate('fieldset', array('fieldset' => 'attributeset', 'legend' => 'Attribute set')); ?>
<input type="submit" value="<?php echo JText::_('JNEXT'); ?>">
<?php echo $this->loadTemplate('formend'); ?>
</form>
