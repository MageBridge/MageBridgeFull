<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright Yireo.com 2012
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
* MageBridge Table class
*
* @package MageBridge
*/
class TableConfig extends JTable
{
    /*
     * Primary key
     * @var int 
     */
    var $id = null;

    /*
     * @var string
     */
    var $name = null;

    /*
     * @var string
     */
    var $value = null;

    /**
     * Constructor
     */
    public function __construct(& $db) {

        // Call the constructor
        parent::__construct('#__magebridge_config', 'id', $db);

    }
}

