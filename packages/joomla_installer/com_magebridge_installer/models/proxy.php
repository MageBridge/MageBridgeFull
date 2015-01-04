<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo (http://www.yireo.com/)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class MageBridgeModelProxy
{
    /*
     * Content fetched through the proxy
     */
    private $_data = '';

    /*
     * Method to fetch the data
     *
     */
    public static function getInstance()
    {
        static $instance;

        if ($instance === null) {
            $instance = new MageBridgeModelProxy();
        }

        return $instance;
    }

    /*
     * Method to fetch data from a remote URL
     */
    public function getRemote($url = '', $variables = array(), $type = null)
    {
        // Take over the _POST data
        if($type == null) {
            $type = 'get';
        }

        // If this is a _GET request, append the variables to the URL
        if(!empty($variables)) {
            $arguments = array();
            foreach($variables as $name => $value) {
                $arguments[] = $name.'='.$value;
            }
            $arguments = implode('&', $arguments);
        }

        if($type == 'get' && !empty($arguments)) {
            $url .= '?'.$arguments;
        }

        return $this->getCURL($url, $type);
    }

    /*
     * CURL-wrapper
     */
    public function getCURL($url, $type = 'get')
    {
        // Initialize CURL
        $handle = curl_init();
        curl_setopt( $handle, CURLOPT_URL, $url );
        curl_setopt( $handle, CURLOPT_MAXREDIRS, true );
        curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $handle, CURLOPT_HEADER, true );
        curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 10 );
        curl_setopt( $handle, CURLOPT_DNS_CACHE_TIMEOUT, 10 );
        curl_setopt( $handle, CURLOPT_TIMEOUT, 20 );

        // Set extra options when a POST is handled
        if($type == 'post') {
            curl_setopt( $handle, CURLOPT_POST, true );    
            curl_setopt( $handle, CURLOPT_POSTFIELDS, $arguments );    
        }

        $data = curl_exec($handle);

        // Generate an error if something went wrong
        $error = trim(curl_error($handle));
        if(!empty($error)) {
            JError::raiseWarning('MB', 'CURL error: '.$error);
            return false;
        }

        // Seperate the headers from the body
        $last_url = curl_getinfo( $handle, CURLINFO_EFFECTIVE_URL );
        $http_code = curl_getinfo( $handle, CURLINFO_HTTP_CODE );
        $header_size = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $header = substr($data, 0, $header_size - 4);
        $body = substr($data, $header_size);

        if($http_code == 301 || $http_code == 302) {

            $matches = array();
            preg_match('/Location: ([^\s]+)/', $header, $matches);
            $url = @parse_url(trim(array_pop($matches)));
            if(!$url) {
                return false;
            }

            //$headers = curl_getinfo( $handle );

            $location = null;
            if(!empty($url['scheme'])) $location .= $url['scheme'] . '://';
            if(!empty($url['host'])) $location .= $url['host'];
            $location .= $url['path'] . ((!empty($url['query'])) ? '?'.html_entity_decode($url['query']) : '');

            if(empty($location)) $location = $last_url;
            header( 'Location: '.$location);
            exit;
        }

        curl_close($handle);
        return $body;
    }
}
