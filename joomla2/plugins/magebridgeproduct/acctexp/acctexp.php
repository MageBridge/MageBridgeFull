<?php
/**
 * MageBridge Product plugin - AEC Membership
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2013
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * MageBridge Product-connector for AEC Membership
 *
 * @package MageBridge
 */
class plgMageBridgeProductAcctexp extends MageBridgePluginProduct
{
    /*
     * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
     */
    protected $connector_field = 'acctexp_plan';

    /*
     * Method to check whether this connector is enabled or not
     *
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        return $this->checkComponent('com_acctexp');
    }

    /*
     * Event "onMageBridgeProductPurchase"
     * 
     * @access public
     * @param array $actions
     * @param object $user Joomla! user object
     * @param tinyint $status Status of the current order
     * @param string $sku Magento SKU
     */
    public function onMageBridgeProductPurchase($actions = null, $user = null, $status = null, $sku = null)
    {
        // Make sure this event is allowed
        if($this->isEnabled() == false) {
            return false;
        }

        // Check for the plan ID
        if(!isset($actions['acctexp_plan'])) {
            return false;
        }

        // Make sure it is not empty
        $acctexp_plan = (int)$actions['acctexp_plan'];
        if(!$acctexp_plan > 0) {
            return false;
        }

        // See if the user is already there
        $query = 'SELECT * FROM `#__acctexp_subscr` WHERE `userid`='.(int)$user->id;
        $this->db->setQuery($query);
        $row = $this->db->loadObject();

        // Expiry = 1 year
        // @todo: Make this configurable 
        $expiration = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', mktime()) . ' + 1 year'));

        if (empty($row)) {
            $values = array(
                'userid' => (int)$user->id,
                'primary' => 1,
                'type' => 'none',
                'status' => 'Active',
                'signup_date' => JFactory::getDate()->toMySQL(),
                'lastpay_date' => JFactory::getDate()->toMySQL(),
                'plan' => (int)$acctexp_plan,
                'expiration' => $expiration,
            );
            $query = 'INSERT INTO `#__acctexp_subscr` SET '.MageBridgeHelper::arrayToSql($values);
        } else {
            $query = 'UPDATE `#__acctexp_subscr` SET `plan`="'.(int)$acctexp_plan.'" WHERE `userid`='.(int)$user->id;
        }
        
        $this->db->setQuery($query);
        $this->db->query();

        // Fully apply the plan
	    include_once JPATH_ROOT.'/components/com_acctexp/acctexp.class.php';
        if(class_exists('metaUser')) {
	        $metaUser = new metaUser( $user->id );
	        $plan = new SubscriptionPlan($this->db);
        	$plan->load( $acctexp_plan );
        	$metaUser->establishFocus( $plan );
	        $metaUser->focusSubscription->applyUsage( $acctexp_plan, 'none', 1 );
        }

        return true;
    }

    /*
     * Event "onMageBridgeProductReverse"
     * 
     * @param array $actions
     * @param JUser $user
     * @param string $sku Magento SKU
     * @return bool
     */
    public function onMageBridgeProductReverse($actions = null, $user = null, $sku = null)
    {
        // Make sure this event is allowed
        if($this->isEnabled() == false) {
            return false;
        }

        // Check for the plan ID
        if(!isset($actions['acctexp_plan'])) {
            return false;
        }

        // Make sure it is not empty
        $acctexp_plan = (int)$actions['acctexp_plan'];
        if(!$acctexp_plan > 0) {
            return false;
        }

        $query = 'UPDATE `#__acctexp_subscr` SET `plan`="", `previous_plan`='.(int)$acctexp_plan.' WHERE `userid`='.(int)$user->id;
        $this->db->setQuery($query);
        $this->db->query();

        return true;
    }
}

