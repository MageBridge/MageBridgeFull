<?php
/**
 * MageBridge App for Watchful
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     GNU Public License (GPL) version 3 (http://www.gnu.org/licenses/gpl-3.0.html)
 * @link        https://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('JPATH_BASE') or die;

/**
 * App class
 *
 * @package MageBridge
 */
require_once(JPATH_ADMINISTRATOR . '/components/com_watchfulli/classes/apps.php');

/**
 * Class MageBridgeAlert
 */
class MageBridgeAlert extends AppAlert
{
	public $parameter1;
	public $parameter2;
	public $parameter3;

	public function MageBridgeAlert($level, $message, $parameter1 = null, $parameter2 = null, $parameter3 = null)
	{
		if ($level != null && $message != null)
		{
			$this->level = $level;
			$this->message = $message;
			$this->parameter1 = $parameter1;
			$this->parameter2 = $parameter2;
			$this->parameter3 = $parameter3;
		}
	}
}

/**
 * Plugin class for reusing redirection of the MageBridge component
 *
 * @package MageBridge
 */
class PlgWatchfulliAppsMageBridge extends watchfulliApps
{
	/**
	 * Constructor.
	 *
	 * @param object &$subject The object to observe.
	 * @param array  $config   An optional associative array of configuration settings.
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * @param      $level
	 * @param      $message
	 * @param null $parameter1
	 * @param null $parameter2
	 * @param null $parameter3
	 */
	public function createAppAlert($level, $message, $parameter1 = null, $parameter2 = null, $parameter3 = null)
	{
		$alert = new MageBridgeAlert($level, $message, $parameter1, $parameter2, $parameter3);
		$this->addAlert($alert);
	}

	/**
	 * @param null $oldValuesSerialized
	 *
	 * @return $this
	 */
	public function appMainProgram($oldValuesSerialized = null)
	{
		$this->setName('MageBridge');
		$this->setDescription('MageBridge app to check if bridge is operating properly.');
		$debug = $this->params->get('debug', 0);

		// Alert and return if MageBridge not installed
		if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_magebridge/magebridge.php'))
		{
			$this->createAppAlert(1, JText::_('PLG_WATCHFULLIAPPS_MAGEBRIDGE_ALERT_COMPONENT_NOTFOUND'));

			return $this;
		}

		require_once JPATH_SITE . '/components/com_magebridge/libraries/factory.php';
		require_once JPATH_SITE . '/components/com_magebridge/helpers/loader.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_magebridge/libraries/loader.php';

		// Check for offline setting
		if (MagebridgeModelConfig::load('offline') == 1)
		{
			$this->createAppAlert(1, JText::_('PLG_WATCHFULLIAPPS_MAGEBRIDGE_ALERT_BRIDGE_OFFLINE'));

			return $this;
		}

		// Check for version on Magento end
		$register = MageBridgeModelRegister::getInstance();
		$bridge = MageBridgeModelBridge::getInstance();
		$version_id = $register->add('version');
		$bridge->build();
		$magebridge_version_magento = $register->getDataById($version_id);
		$magebridge_version_joomla = MageBridgeUpdateHelper::getComponentVersion();

		if (empty($magebridge_version_magento))
		{
			$this->createAppAlert(1, JText::_('PLG_WATCHFULLIAPPS_MAGEBRIDGE_ALERT_BRIDGE_NOVERSION'));
		}
		else
		{
			$result = (version_compare($magebridge_version_magento, $magebridge_version_joomla, '=')) ? true : false;

			if ($result == false)
			{
				$this->createAppAlert(1, JText::_('PLG_WATCHFULLIAPPS_MAGEBRIDGE_ALERT_BRIDGE_VERSION_MISMATCH'));
			}
		}

		return $this;
	}
}
