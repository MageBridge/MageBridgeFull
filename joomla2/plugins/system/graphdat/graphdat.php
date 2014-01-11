<?php
/**
 * Joomla! plugin for Graphdat
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 *
 * 1) Install Graphdat Agent (see graphdat.com)
 * 2) Install Graphdat PHP extension (pecl install graphdat)
 * 3) Enable this System - Graphdat plugin
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Import the parent class
jimport( 'joomla.plugin.plugin' );
        
/**
 * System Graphdat Plugin
 */
class plgSystemGraphdat extends JPlugin
{
    /**
     * Wrapper function for graphdat_begin
     * 
     * @access private
     * @param string $name
     * @return null
     */
    private function graphdatBegin($timer = null)
    {
        $app = JFactory::getApplication();
        if($app->isSite() == false) {
            return false;
        }

        if(function_exists('graphdat_begin')) {
            graphdat_begin($timer);
        }
    }

    /**
     * Wrapper function for graphdat_end
     * 
     * @access private
     * @param string $name
     * @return null
     */
    private function graphdatEnd($timer = null)
    {
        $app = JFactory::getApplication();
        if($app->isSite() == false) {
            return false;
        }

        if(function_exists('graphdat_end')) {
            //graphdat_end($timer);
        }
    }

    /**
     * Event onAfterLoad
     *
     * @access public
     * @param null
     * @return null
     */
    public function onAfterLoad()
    {
        $this->graphdatBegin('onAfterLoad');
    }

    /**
     * Event onAfterInitialise
     *
     * @access public
     * @param null
     * @return null
     */
    public function onAfterInitialise()
    {
        $this->graphdatEnd('onAfterLoad');
        $this->graphdatBegin('onAfterInitialise');
    }

    /**
     * Event onAfterRoute
     *
     * @access public
     * @param null
     * @return null
     */
    public function onAfterRoute()
    {
        $this->graphdatEnd('onAfterInitialise');
        $this->graphdatBegin('onAfterRoute');
    }

    /**
     * Event onAfterDispatch
     *
     * @access public
     * @param null
     * @return null
     */
    public function onAfterDispatch()
    {
        $this->graphdatEnd('onAfterRoute');
        $this->graphdatBegin('onAfterDispatch');
    }

    /**
     * Event onAfterRender
     *
     * @access public
     * @param null
     * @return null
     */
    public function onAfterRender()
    {
        $this->graphdatEnd('onAfterDispatch');
        $this->graphdatBegin('onAfterRender');
    }
}
