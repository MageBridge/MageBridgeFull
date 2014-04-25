<?php
/**
 * Joomla! component MageBridge Importer
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge Importer
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* MageBridge Importer Field Table class
*
* @package MageBridgeImporter
*/
class TableField extends YireoTable
{
    /**
     * Constructor
     *
     * @param JDatabase $db
     * @return null
     */
    public function __construct(& $db) 
    {
        parent::__construct('#__magebridge_importer_fields', 'id', $db);
    }
}
