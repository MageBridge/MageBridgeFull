<?php
$string = 'aHR0cDovL3d3dy5idWZmYWxvLm5sL25sL2ZpdG5lc3MtaGFuZHNjaG9lbmVuLXJlZWJvay1zLW0tcm96ZQ,,';
echo base64_decode(strtr($string, '-_,', '+/='))."\n";
