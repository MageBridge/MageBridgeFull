<?php
$url = "http://YOUR_MAGENTO/magebridge.php";
$handle = curl_init($url);
curl_setopt_array( $handle, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_MAXREDIRS => 0,
    CURLOPT_HEADER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_CONNECTTIMEOUT => 60,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_DNS_CACHE_TIMEOUT => 60,
    CURLOPT_DNS_USE_GLOBAL_CACHE => false,
    CURLOPT_COOKIESESSION => true,
    CURLOPT_FRESH_CONNECT => false,
    CURLOPT_HTTPHEADER,array('Expect:'),
));

curl_setopt( $handle, CURLOPT_POST, true );
curl_setopt( $handle, CURLOPT_POSTFIELDS, array('mbtest', 1));       
$data = curl_exec( $handle );

echo "CURL response: <br/>\n"; 
echo "<pre>\n";
print_r($data);
echo "</pre>\n";

echo "CURL error: <br/>\n"; 
echo "<pre>\n";
print_r(curl_errno($handle));
print_r(curl_error($handle));
echo "</pre>\n";

echo "CURL info: <br/>\n"; 
echo "<pre>\n";
print_r(curl_getinfo($handle));
echo "</pre>\n";
