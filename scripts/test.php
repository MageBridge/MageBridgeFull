<?php
$string = 'aHR0cDovL2pvb21sYTI1Lm1hZ2VicmlkZ2UuZGV2L3Nob3AxL2Z1cm5pdHVyZT8v';
echo base64_decode(strtr($string, '-_,', '+/='))."\n";
