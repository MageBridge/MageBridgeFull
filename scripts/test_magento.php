<?php
$string = 'aHR0cDovL29mZnlhdHJlZXJlY29yZHMuZGUvb25saW5lc2hvcC9oaXR6ZWZyZWktNjE_U0lEPQ,,';
echo base64_decode(strtr($string, '-_,', '+/='))."\n";
