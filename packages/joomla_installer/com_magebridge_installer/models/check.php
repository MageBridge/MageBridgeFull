<?php
/*
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright Yireo.com 2012
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Include Joomla! libraries
jimport( 'joomla.application.component.model' );

// Define some static variables
define('CHECK_OK', 1);
define('CHECK_WARNING', 0);
define('CHECK_ERROR', -1);

/*
 * MageBridge Check model
 */
class MagebridgeModelCheck extends MagebridgeModelAbstract
{
    private $_endcheck = true;

    public function addResult($group, $check, $status = 0, $description = '')
    {
        if(empty($this->_checks[$group])) {
            $this->_checks[$group] = array();
        }

        if($status == CHECK_WARNING) {
            $image = '<img src="'.JURI::root().'/media/com_magebridge/images/check-warning.png" />';
        } elseif($status == CHECK_ERROR) {
            $this->_endcheck = false;
            $image = '<img src="'.JURI::root().'/media/com_magebridge/images/check-error.png" />';
        } else {
            $image = '<img src="'.JURI::root().'/media/com_magebridge/images/check-ok.png" />';
        }

        $this->_checks[$group][] = array(
            'text' => JText::_($check),
            'status' => $status,
            'image' => $image,
            'description' => $description,
        );
    }

    public function getEndCheck()
    {
        return $this->_endcheck;
    }

    public function getChecks()
    {
        $this->getSystemChecks();
        return $this->_checks;
    }

    public function getSystemChecks()
    {
        $application = JFactory::getApplication();
        $db = JFactory::getDBO();

        $config = MagebridgeModelConfig::load();

        $result = (empty($config) || strlen($config->value) < 20) ? CHECK_WARNING : CHECK_OK;
        if($config->value == 'TRIAL') $result = CHECK_OK;
        $this->addResult('system', 'Support key', $result, JText::_('CHECK_SUPPORT_KEY'));

        $result = (version_compare(phpversion(), '5.2.8', '>=')) ? CHECK_OK : CHECK_ERROR;
        $this->addResult('system', 'PHP version', $result, JText::_('CHECK_PHP_VERSION'));

        $result = (version_compare(ini_get('memory_limit'), '31M', '>')) ? CHECK_OK : CHECK_ERROR;
        $this->addResult('system', 'PHP memory', $result, JText::_('CHECK_PHP_MEMORY'));

        $jversion = new JVersion();
        $result = (version_compare($jversion->getShortVersion(), '1.6.0', '>=')) ? CHECK_OK : CHECK_ERROR;
        $this->addResult('system', 'Joomla! version', $result, JText::_('CHECK_JOOMLA_VERSION'));

        $result = (function_exists('simplexml_load_string')) ? CHECK_OK : CHECK_ERROR;
        $this->addResult('system', 'SimpleXML', $result, JText::_('CHECK_SIMPLEXML'));

        $result = (function_exists('json_decode')) ? CHECK_OK : CHECK_ERROR;
        $this->addResult('system', 'JSON', $result, JText::_('CHECK_JSON'));

        $result = (function_exists('curl_init')) ? CHECK_OK : CHECK_ERROR;
        $this->addResult('system', 'CURL', $result, JText::_('CHECK_CURL'));

        $result = (function_exists('mcrypt_cfb')) ? CHECK_OK : CHECK_ERROR;
        $this->addResult('system', 'mcrypt', $result, JText::_('CHECK_MCRYPT'));

        $result = (ini_get('allow_url_fopen')) ? CHECK_OK : CHECK_ERROR;
        $this->addResult('system', 'allow_url_fopen', $result, JText::_('CHECK_ALLOW_URL_FOPEN'));

        $result = (is_writable(JPATH_SITE.'/cache')) ? CHECK_OK : CHECK_WARNING;
        $this->addResult('system', 'Cache writable', $result, JText::_('CHECK_CACHE'));

        $result = (is_writable($application->getCfg('tmp_path'))) ? CHECK_OK : CHECK_WARNING;
        $this->addResult('system', 'Temp-path writable', $result, JText::_('CHECK_TMP'));
        
        $result = (@fsockopen('api.yireo.com', 80, $errno, $errmsg, 10)) ? CHECK_OK : CHECK_ERROR;
        $this->addResult('system', 'Download connection', $result, JText::_('CHECK_CONNECTION'));
    }
}
