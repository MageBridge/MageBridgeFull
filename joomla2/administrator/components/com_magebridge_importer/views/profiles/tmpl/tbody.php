<?php 
/*
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

defined('_JEXEC') or die('Restricted access');
?>
<td>
    <a href="<?php echo $item->edit_link; ?>"><?php echo $item->label; ?></a>
</td>
<td>
    <?php echo count($item->include_fields); ?>
</td>
<td>
    <?php echo count($item->exclude_fields); ?>
</td>
