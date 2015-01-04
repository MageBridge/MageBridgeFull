<?php
/*
 * Magento Bridge Host Check
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

$script_version = '0.4';
if(isset( $_GET['phpinfo'])) {
    phpinfo();
    exit;
} 

/*
 * System Check class
 */
class systemCheck
{
    public function check($check = array()) {
        $function = $check['function'];
        if(isset($check['arguments'])) {
            $result = systemCheck::$function($check['arguments']);
        } else {
            $result = systemCheck::$function();
        }
        $check['class'] = ($result[1] == true) ? 'ok' : 'warning';
        $check['description'] = $result[0];
        return $check;
    }

    private function settingEnabled($setting)
    {
        if(ini_get($setting) == 0) {
            return array('Disabled', false);
        } else {
            return array('Enabled', true);
        }
    }

    private function settingDisabled($setting)
    {
        if(ini_get($setting) == 0) {
            return array('Disabled', true);
        } else {
            return array('Enabled', false);
        }
    }

    private function openBasedir()
    {
        $open_basedir = ini_get('open_basedir');
        if(empty($open_basedir)) {
            return array('Deactivated (security problem)', false);
        } else {
            return array('Activated', true);
        }
    }

    private function functionEnabled($function)
    {
        $php_disabled = ini_get('disable_functions');
        $suhosin_disabled = ini_get('suhosin.executor.func.blacklist');
        if( preg_match( "/([^|,]?)$function([$|,]?)/", $suhosin_disabled )) {
            return array('Disabled in Suhosin', false);
        } elseif( preg_match( "/([^|,]?)$function([$|,]?)/", $php_disabled )) {
            return array('Disabled in php.ini', false);
        } elseif(!is_callable($function)) {
            return array('Not callable', false);
        }
        return array('Available', true);
    }

    private function moduleEnabled($module)
    {
        if(extension_loaded($module)) {
            return array('Available', true);
        }
        return array('Not available', false);
    }

    private function memory($minimum)
    {
        $minimum = $minimum;
        $current = ini_get('memory_limit');
        $minimum_bytes = $minimum * 1024 * 1024;
        $current_bytes = systemCheck::getBytes($current);
        if($current_bytes < $minimum_bytes) {
            return array($minimum.' needed (current: '.$current.')', false);
        }
        return array($current.' ', true);
    }

    private function sessionSavePath()
    {
        $path = ini_get('session.save_path');
        if(empty($path)) {
            return array('Empty', false);
        } elseif(!is_dir($path)) {
            return array($path . ' is not a directory', false);
        } elseif(!is_writable($path)) {
            return array($path . ' is not writable', false);
        }
        return array('Writable', true);
    }

    private function isJoomla()
    {
        define('JPATH_BASE', dirname(__FILE__));
        $version_file = dirname(__FILE__).'/libraries/joomla/version.php';
        if(!is_file($version_file)) {
            return array('Not found', false);
        }
        include_once $version_file;
        $version = new JVersion();
        return array($version->getShortVersion(), true);
    }

    private function isMagento()
    {
        $version_file = dirname(__FILE__).'/app/Mage.php';
        if(!is_file($version_file)) {
            return array('Not found', false);
        }
        include_once $version_file;
        return array('Magento '.Mage::getVersion(), true);
    }

    private function version($package = 'php')
    {
        switch($package) {
            case 'php':
                $version = phpversion();
                $current = $version;
                $minimum = '5.2.8';
                break;
            case 'apache':
                if(function_exists('apache_get_version')) {
                    $version = apache_get_version();
                } else {
                    return array('Unable to read Apache version', false);
                }
                preg_match('/Apache\/([0-9\.]+)/', $version, $match);
                $current = $match[1];
                $minimum = '2.0.61';
                break;
        }
        if(version_compare($current, $minimum, '<')) {
            return array($minimum.' needed (current: '.$current.')', false);
        } else {
            return array($current, true);
        }
    }

    private function isWritable()
    {
        if(!is_writable(__FILE__)) {
            return array('Not writable', false);
        } else {
            return array('Writable', true);
        }
    }

    private function getUnixUser()
    {
        $user = @exec('whoami');
    }

    private function checkCURL()
    {
        $url = 'http://shop.yireo.com/magebridge.php';
        $postfields = array('mbtest' => 1);

        $handle = curl_init($url);
        curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $handle, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 20 );
        curl_setopt( $handle, CURLOPT_TIMEOUT, 20 );
        curl_setopt( $handle, CURLOPT_POST, true );
        curl_setopt( $handle, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt( $handle, CURLOPT_HTTPHEADER, array('Expect:'));
        $data = curl_exec($handle);

        if(!empty($data) && preg_match('/^\{\"mbtest/', $data)) {
            return array('OK', true);
        } else {
            return array('Failed', false);
        }
    }

    private function getBytes($value) {
        $value = trim($value);
        $last = strtolower($value[strlen($value)-1]);
        switch($last) {
            case 'g': $value *= 1024;
            case 'm': $value *= 1024;
            case 'k': $value *= 1024;
        }

        return $value;
    }
}

$checks = array(
    'common' => array(
        array('title' => 'File Writable', 'function' => 'isWritable'),
        array('title' => 'UNIX User', 'function' => 'getUnixUser'),
        array('title' => 'PHP version', 'function' => 'version', 'arguments' => 'php'),
        array('title' => 'Apache version', 'function' => 'version', 'arguments' => 'apache'),
        array('title' => 'JSON module', 'function' => 'moduleEnabled', 'arguments' => 'json'),
        array('title' => 'JSON functions', 'function' => 'functionEnabled', 'arguments' => 'json_encode'),
        array('title' => 'CURL module', 'function' => 'moduleEnabled', 'arguments' => 'curl'),
        array('title' => 'CURL functions', 'function' => 'functionEnabled', 'arguments' => 'curl_init'),
        array('title' => 'Register globals', 'function' => 'settingDisabled', 'arguments' => 'register_globals'),
        array('title' => 'Safe mode', 'function' => 'settingDisabled', 'arguments' => 'safe_mode'),
        array('title' => 'Log Errors', 'function' => 'settingEnabled', 'arguments' => 'log_errors'),
        array('title' => 'Expose PHP (optional)', 'function' => 'settingDisabled', 'arguments' => 'expose_php'),
        array('title' => 'Display Errors (optional)', 'function' => 'settingDisabled', 'arguments' => 'display_errors'),
    ),
    'joomla' => array(
        array('title' => 'Joomla! installation', 'function' => 'isJoomla'),
        array('title' => 'PHP memory', 'function' => 'memory', 'arguments' => '32M'),
        array('title' => 'mcrypt module', 'function' => 'moduleEnabled', 'arguments' => 'mcrypt'),
        array('title' => 'mcrypt functions', 'function' => 'functionEnabled', 'arguments' => 'mcrypt_cfb'),
        array('title' => 'SimpleXML module', 'function' => 'moduleEnabled', 'arguments' => 'simplexml'),
        array('title' => 'Session path', 'function' => 'sessionSavePath'),
    ),
    'magento' => array(
        array('title' => 'Magento installation', 'function' => 'isMagento'),
        array('title' => 'PHP memory', 'function' => 'memory', 'arguments' => '256M'),
        array('title' => 'PDO_MySQL module', 'function' => 'moduleEnabled', 'arguments' => 'pdo_mysql'),
        array('title' => 'hash', 'function' => 'moduleEnabled', 'arguments' => 'hash'),
        array('title' => 'DOM', 'function' => 'moduleEnabled', 'arguments' => 'dom'),
        array('title' => 'iconv', 'function' => 'moduleEnabled', 'arguments' => 'iconv'),
        array('title' => 'GD (optional)', 'function' => 'moduleEnabled', 'arguments' => 'gd'),
        array('title' => 'SOAP (optional)', 'function' => 'moduleEnabled', 'arguments' => 'soap'),
        array('title' => 'Short open tags (optional)', 'function' => 'settingEnabled', 'arguments' => 'short_open_tag'),
    ),
    'bridge' => array(
        array('title' => 'CURL connection', 'function' => 'checkCURL'),
    ),
);
?> 
<html>
<head>
<title>MageBridge Host Check <?php echo $script_version; ?></title>
<style type="text/css">
body {
    font-size: 12px;
    padding: 0;
    margin: 0;
}

img.logo {
    padding-left: 10px;
}

a img {
    border: 0;
}

div#container {
    position: absolute;
    margin-left: -400px;
    left: 50%;
    width: 700px;
    border-left: 1px solid #444444;
    border-right: 1px solid #444444;
    padding: 20px;
}

h3 {
    background-color: #516b8c;
    padding: 20px;
    color: white;
}

dl {
    border-bottom: 1px solid #444444;
    padding: 0;
    margin: 0;
    height:30px;
    line-height:20px;
}

dt {
    float: left;
    line-height: 30px;
}

dd {
    padding-left: 10px;
    line-height: 30px;
    width: 450px;
    float: right;
}

dd.ok {
    background-color: #e0ffa5;
}

dd.warning, div.warning {
    font-weight: bold;
    color: white;
    background-color: #d77400;
}

div.warning {
    padding: 10px;
}
</style>
</head>

<body>
<div id="container">
<a href="http://www.yireo.com/"><img class="logo" src="http://www.yireo.com/templates/yireo/images/logo.png" align="right" alt="Yireo Logo" title="Yireo Logo" /></a>
<h1>MageBridge Host Check <?php echo $script_version; ?></h1>
<p>
This script checks whether <a href="http://www.yireo.com/software/magebridge">MageBridge</a> 
(acting as bridge between Joomla! and Magento) can be run on this hosting environment or not. 
We recommend to install <a href="#joomla">Joomla!</a> and <a href="#magento">Magento</a> on seperate (sub)domains for optimal security.
</p>
<p><a href='<?php echo $_SERVER['PHP_SELF'] ?>?phpinfo=true'>phpinfo()</a></p>
<div class="warning">Remove this script from your site as soon as you're done</div>

<a name="common"></a>
<h3>Checks for both Joomla! as Magento</h3>
<?php foreach($checks['common'] as $check) { ?>
<dl>
    <dt><?php echo $check['title']; ?></dt>
    <?php $check = systemCheck::check($check); ?>
    <dd class="<?php echo $check['class']; ?>"><?php echo $check['description']; ?></dd>
</dl>
<?php } ?>

<a name="joomla"></a>
<h3>Joomla! checks</h3>
<?php foreach($checks['joomla'] as $check) { ?>
<dl>
    <dt><?php echo $check['title']; ?></dt>
    <?php $check = systemCheck::check($check); ?>
    <dd class="<?php echo $check['class']; ?>"><?php echo $check['description']; ?></dd>
</dl>
<?php } ?>

<a name="magento"></a>
<h3>Magento checks</h3>
<?php foreach($checks['magento'] as $check) { ?>
<dl>
    <dt><?php echo $check['title']; ?></dt>
    <?php $check = systemCheck::check($check); ?>
    <dd class="<?php echo $check['class']; ?>"><?php echo $check['description']; ?></dd>
</dl>
<?php } ?>

<a name="bridge"></a>
<h3>Bridge checks</h3>
<?php foreach($checks['bridge'] as $check) { ?>
<dl>
    <dt><?php echo $check['title']; ?></dt>
    <?php $check = systemCheck::check($check); ?>
    <dd class="<?php echo $check['class']; ?>"><?php echo $check['description']; ?></dd>
</dl>
<?php } ?>

</div>
</body>
</html>
