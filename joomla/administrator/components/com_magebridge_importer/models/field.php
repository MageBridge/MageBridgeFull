<?php
/*
 * Joomla! component MageBridge Importer
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge Importer
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Import Joomla! libraries
jimport('joomla.utilities.date');

/*
 * MageBridge Importer Field model
 */
class MagebridgeImporterModelField extends YireoModel
{
    /**
     * Indicator if this is a model for multiple or single entries
     */
    protected $_single = true;

    /**
     * Constructor method
     *
     * @package MageBridge
     * @access public
     * @param null
     * @return null
     */
    public function __construct()
    {
        parent::__construct('field');
    }
}
