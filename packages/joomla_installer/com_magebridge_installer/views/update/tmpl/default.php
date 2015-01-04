<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<form method="post" name="adminForm" id="adminForm">

<div id="editcell">
<fieldset>
<legend><?php echo 'System check'; ?></legend>
<table cellspacing="0" cellpadding="0" border="0" class="admintable">
    <?php foreach($this->checks['system'] as $check) { ?>
    <tr>
        <td class="key" valign="top" width="200">
            <?php echo $check['text']; ?>
        </td>
        <td width="30">
            <?php echo $check['image']; ?>
        </td>
        <td>
            <?php echo $check['description']; ?>
        </td>
    </tr>
    <?php } ?>
</table>
</fieldset>
</div>

<div id="editcell">
<fieldset>
<legend><?php echo 'Support information'; ?></legend>
<table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminlist">
    <tr class="row1">
        <td width="200">
            <?php echo JText::_( 'Support key' ); ?>
        </td>
        <td>
            <input type="text" name="supportkey" value="<?php echo $this->config->value; ?>" size="60" />
        </td>
    </tr>
</table>
</fieldset>
</div>

<input type="hidden" name="option" value="com_magebridge" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
