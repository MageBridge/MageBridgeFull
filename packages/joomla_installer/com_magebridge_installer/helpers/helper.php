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

/*
 * General helper for usage in Joomla!
 */
class MageBridgeHelper
{
    /*
     * Helper-method to get help-URLs for usage in the content
     */
    static public function getHelpItem($name = null)
    {
        $links = array(
            'faq' => array(
                'title' => 'General FAQ',
                'link' => 'https://www.yireo.com/software/magebridge/faq',
                'internal' => 0,
            ),
            'faq-troubleshooting' => array(
                'title' => 'Troubleshooting FAQ',
                'link' => 'https://www.yireo.com/software/magebridge/faq/troubleshooting',
                'internal' => 0,
            ),
            'faq-troubleshooting:api-widgets' => array(
                'title' => 'API Widgets FAQ',
                'link' => 'http://www.yireo.com/software/magebridge/faq/troubleshooting#item60',
                'internal' => 0,
            ),
            'faq-development' => array(
                'title' => 'Development FAQ',
                'link' => 'https://www.yireo.com/software/magebridge/faq/development',
                'internal' => 0,
            ),
            'forum' => array(
                'title' => 'MageBridge Support Form',
                'link' => 'https://www.yireo.com/forum/',
                'internal' => 0,
            ),
            'tutorials' => array(
                'title' => 'Yireo Tutorials',
                'link' => 'http://www.yireo.com/tutorials',
                'internal' => 0,
            ),
            'quickstart' => array(
                'title' => 'MageBridge Quick Start Guide',
                'link' => 'https://www.yireo.com/tutorials/magebridge-basics/quick-start-guide',
                'internal' => 0,
            ),
            'troubleshooting' => array(
                'title' => 'MageBridge Troubleshooting Guide',
                'link' => 'https://www.yireo.com/tutorials/23-magebridge/73-magebridge-troubleshooting-guide', 
                'internal' => 0,
            ),
            'subscriptions' => array(
                'title' => 'your Yireo Subscriptions page',
                'link' => 'https://www.yireo.com/my-software-subscriptions',
                'internal' => 0,
            ),
            'config' => array(
                'title' => 'Global Configuration',
                'link' => 'index.php?option=com_config',
                'internal' => 1,
            ),
        );

        if(!empty($name) && isset($links[$name])) {
            return $links[$name];
        }
        
        return null;
    }

    /*
     * Helper-method to display Yireo.com-links
     */
    static public function getHelpText($name = null, $title = null)
    {
        $help = MageBridgeHelper::getHelpItem($name);
        $target = ($help['internal'] == 0) ? ' target="_new"' : '';
        $title = (!empty($title)) ? $title : JText::_($help['title']);

        return '<a href="'.$help['link'].'"'.$target.'>'.$title.'</a>';
    }

    /*
     * Helper-method to insert notices into the application
     */
    static public function help($text = null)
    {
        if(MagebridgeModelConfig::load('show_help') == 1) {

            if(preg_match('/\{([^\}]+)\}/', $text, $match)) {
                $array = explode(':', $match[1]);
                $text = str_replace($match[0], MageBridgeHelper::getHelpText($array[0], $array[1]), $text);
            }

            $html = '<div class="magebridge-help">';
            $html .= $text;
            $html .= '</div>';
            return $html;
        }
    }

    /*
     * Helper-method to determine the current Magento request
     */
    static public function getRequest()
    {
        static $request = null;
        if(is_null($request) && JFactory::getApplication()->isSite()) {

            $root = MageBridgeUrlHelper::getRootItem();
            $request = JRequest::getString('request');

            // Build a list of current variables
            $current_vars = array('option','view','layout','format','request','Itemid','lang','SID');

            // If the request is set, filter all rubbish
            if(!empty($request)) {

                // Parse the current request
                //if(MageBridgeModelBridge::urlSuffix()) {
                //    $request = preg_replace( '/\.html/', '', $request );
                //}
                $request = str_replace( 'index.php', '', $request );
                $request = str_replace( '//', '/', $request );
                $request = str_replace( '\/', '/', $request );
                $request = preg_replace( '/(SID|sid)=(U|S)/', '', $request );
                $request = preg_replace( '/^\//', '', $request );

                // Convert the current request into an array (example: /checkout/cart)
                $request_vars = explode('/', preg_replace('/\?([*]+)/', '', $request));
                if(!empty($request_vars)) {
                    foreach($request_vars as $var) {
                        $current_vars[] = $var;
                    }
                }

                // Convert the current GET-variables into an array (example: ?limit=25)
                if(preg_match('/([^\?]+)\?/', $request)) {
                    $query = preg_replace('/([^\?]+)\?/', '', $request);
                    parse_str($query, $query_array);
                    if(!empty($query_array)) {
                        foreach($query_array as $name => $value) {
                            $current_vars[] = $name;
                        }
                    }
                }
            }

            // Add custom GET variables
            $get = array();
            $get_vars = JRequest::get('get');
            if(!empty($get_vars)) {
                foreach($get_vars as $name => $value) {
                    if(!in_array($name, $current_vars)) {
                        $get[$name] = $value;
                    }
                }
            }
            if(!empty($get)) $request .= '?'.http_build_query($get);
        }

        return $request;
    }

    /*
     * Helper-method to filter the original Magento content from unneeded/unwanted bits
     */
    static public function filterContent($content)
    {
        // Implement a very dirty hack because PayPal converts URLs "&" to "and"
        $current = MageBridgeUrlHelper::current();
        if(strstr($current, 'paypal') && strstr($current, 'redirect')) {

            // Try to find the distorted URLs
            if(preg_match_all('/([^\"\']+)com_magebridgeand([^\"\']+)/', $content, $matches)) {
                foreach($matches[0] as $match) {

                    // Replace the wrong "and" words with "&" again
                    $url = str_replace('com_magebridgeand', 'com_magebridge&', $match);
                    $url = str_replace('rootand', 'root&', $url);

                    // Replace the wrong URL with its correction
                    $content = str_replace($match, $url, $content); 
                }
            }
        }

        // Replace all uenc-URLs from Magento with URLs parsed through JRoute
        if(preg_match_all( '/\/uenc\/([a-zA-Z0-9\-\_\,]+)/', $content, $matches )) {
            foreach($matches[1] as $match) {

                // Decode the match
                $decoded = base64_decode(strtr($match, '-_,', '+/='));
                $decoded = str_replace(JURI::base(), '', $decoded);
                
                // Convert the non-SEF URL to a SEF URL
                if(preg_match('/^index.php\?option=com_magebridge/', $decoded)) {

                    // Parse the URL but do NOT turn it into SEF because of Mage_Core_Controller_Varien_Action::_isUrlInternal()
                    $url = MageBridgeHelper::filterUrl(str_replace('/', urldecode('/'), $decoded), false);

                    // Add the domain again to the URL
                    $url = JURI::base().preg_replace('/^\//', '', $url);
                    MageBridgeModelDebug::getInstance()->notice( 'uenc: replacing "'.$decoded.'" with "'.$url.'"' );

                    // Replace the URL in the content
                    $content = str_replace( $match, strtr( base64_encode($url), '+/=', '-_,' ), $content );
                }
            }
        }

        // Match all URLs and filter them
        $url = null;
        if(preg_match_all( '/index.php\?option=com_magebridge([^\'\"\<]+)([\'\"\<]{1})/', $content, $matches )) {
            for($i = 0; $i < count($matches[0]); $i++ ) {
                $oldurl = 'index.php?option=com_magebridge'.$matches[1][$i];
                $end = $matches[2][$i];
                $newurl = MageBridgeHelper::filterUrl($oldurl);
                if(!empty($newurl)) {
                    $content = str_replace( $oldurl.$end, $newurl.$end, $content );
                }
            }
        }

        // Remove double-slashes
        $content = str_replace(JURI::base().'/', JURI::base(), $content);

        return $content;
    }

    /*
     * Helper-method to merge the original Magento URL into the Joomla! URL
     */
    static public function filterUrl($url, $use_sef = true)
    {
        if(empty($url)) return null;

        // Parse the query-part of the URL 
        $q = explode('?', $url);
        array_shift($q);

        // Merge the Magento query with the Joomla! query
        $qs = implode('&', $q);
        $qs = str_replace('&amp;', '&', $qs);
        parse_str($qs, $query);

        // Get rid of the annoying SID
        $sids = array('SID', 'sid', '__SID', '___SID');
        foreach($sids as $sid) {
            if(isset($query[$sid])) unset($query[$sid]);
        }

        // Construct the URL again
        $url = 'index.php?';
        foreach($query as $name => $value) {
            $url_segments[] = $name.'='.$value;
        }
        $url = 'index.php?'.implode('&', $url_segments);

        if($use_sef == true) {
            $url = MageBridgeHelper::getSefUrl($url);
        }

	    $prefix = JURI::getInstance()->toString(array('scheme', 'host', 'port'));
        $path = str_replace($prefix, '', JURI::base());
        $pos = strpos($url, $path);
        if(!empty($path) && $pos !== false ) {
            $url = substr($url, $pos + strlen($path)); 
        }

        return $url;
    }

    /*
     * Helper-method to parse the comma-seperated setting "disable_css_mage" into an array
     */
    static public function getDisableCss()
    {
        $disable_css = MagebridgeModelConfig::load('disable_css_mage');
        if(empty($disable_css)) {
            return array();
        }

        $disable_css = explode(',', $disable_css);
        if(!empty($disable_css)) {
            foreach($disable_css as $name => $value) {
                $value = trim($value);
                $disable_css[$value] = $value;
            }
        }
        return $disable_css;
    }

    /*
     * Helper-method to find out if some kind of CSS-file is disabled or not
     */
    static public function cssIsDisabled($css)
    {
        $disable_css = MageBridgeHelper::getDisableCss();
        if(!empty($disable_css)) {
            foreach($disable_css as $disable) {
                $disable = str_replace('/', '\/', $disable);
                if(preg_match("/$disable$/", $css)) {
                    return true;
                }
            }
        }
        return false;
    }

    /*
     * Helper-method to parse the comma-seperated setting "disable_js_mage" into an array
     */
    static public function getDisableJs()
    {
        $disable_js = MagebridgeModelConfig::load('disable_js_mage');
        if(empty($disable_js)) {
            return array();
        }

        $disable_js = explode(',', $disable_js);
        if(!empty($disable_js)) {
            foreach($disable_js as $name => $value) {
                $value = trim($value);
                $disable_js[$value] = $value;
            }
        }
        return $disable_js;
    }

    /*
     * Helper-method to find out if some kind of JS-file is disabled or not
     */
    static public function jsIsDisabled($js)
    {
        $disable_js = MageBridgeHelper::getDisableJs();
        if(!empty($disable_js)) {
            foreach($disable_js as $disable) {
                $disable = str_replace('/', '\/', $disable);
                if(preg_match("/$disable$/", $js)) {
                    return true;
                }
            }
        }
        return false;
    }

    /*
     * Helper-method to get the current Joomla! core version
     */
    static public function getJoomlaVersion()
    {
        JLoader::import( 'joomla.version' );
        $version = new JVersion();
        return $version->getShortVersion();
    }

    /*
     * Helper-method to get the current Joomla! core version
     */
    static public function isJoomla15()
    {
        JLoader::import( 'joomla.version' );
        $version = new JVersion();
        if(version_compare( $version->RELEASE, '1.5', 'eq')) {
            return true;
        }
        return false;
    }

    /*
     * Helper-method to get the current Joomla! core version
     */
    static public function isJoomla25()
    {
        JLoader::import( 'joomla.version' );
        $version = new JVersion();
        if(version_compare( $version->RELEASE, '1.6', 'eq')) {
            return true;
        } elseif(version_compare( $version->RELEASE, '1.7', 'eq')) {
            return true;
        } elseif(version_compare( $version->RELEASE, '2.5', 'eq')) {
            return true;
        }
        return false;
    }

    /*
     * Helper-method to get the current Joomla! core version
     */
    static public function isJoomla35()
    {
        JLoader::import( 'joomla.version' );
        $version = new JVersion();
        if(version_compare( $version->RELEASE, '3.0', 'eq')) {
            return true;
        } elseif(version_compare( $version->RELEASE, '3.1', 'eq')) {
            return true;
        } elseif(version_compare( $version->RELEASE, '3.2', 'eq')) {
            return true;
        } elseif(version_compare( $version->RELEASE, '3.5', 'eq')) {
            return true;
        }
        return false;
    }

    /*
     * Helper-method to get a Joomla! SEF URL
     */
    static public function getSefUrl($url)
    {
        if( MageBridgeModelBridge::sh404sef() == true ) {
            $oldurl = $url;
            $newurl = JRoute::_($oldurl);
            if(!empty($url)) {
                $url = $newurl;
                $sh404sef = shGetNonSefURLFromCache($oldurl, $newurl);
                if(!$sh404sef) {
                    shAddSefURLToCache( $oldurl, $url, sh404SEF_URLTYPE_CUSTOM);
                }
            }

        // Regular Joomla! SEF
        } else {
            $url = JRoute::_($url);
        }

        return $url;
    }
}
