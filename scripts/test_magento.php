<?php
$string =
'aHR0cDovL2Nyb2NrZXJhcnRtdXNldW0ub3JnL2Nyb2NrZXJ2Ni90ZXN0aW5nL2dpZnRzdG9yZS9ib29rcy8,';
echo base64_decode(strtr($string, '-_,', '+/='))."\n";
