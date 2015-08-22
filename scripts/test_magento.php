<?php 
$string = 'aHR0cHM6Ly93d3cuYXVndXN0b2l0YWxpYW5mb29kLmNvbS9wcm9kb3R0aS9jYXRhbG9nc2VhcmNoL3Jlc3VsdD9xPXBhc3Rh';
echo base64_decode(strtr($string, '-_,', '+/='))."\n";
