<?php
/**
 * Joomla! MageBridge Tags Content plugin
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2012
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<?php if(!empty($products)) { ?>
<div class="box base-mini mini-tags">
    <div class="head">
        <h4><?php echo JText::sprintf('Products with tag: %s', $tagstring); ?></h4>
    </div>
    <div class="content">
        <ul>
        <?php foreach($products as $product) { ?>
            <li><a href="<?php echo $product['url']; ?>" title="<?php echo $product['name']; ?>"><?php echo $product['name']; ?></a></li> 
        <?php } ?>
        </ul>
    </div>
</div>
<?php } ?>
