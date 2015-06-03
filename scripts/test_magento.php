<?php 
$string = 'aHR0cHM6Ly9pcm9uc2hpcnRvZmdvbGYuY29tL3N0b3JlL2NhdGFsb2cvcHJvZHVjdC92aWV3L2Zvcm1fa2V5L002QzMwYXhWd3JjSDh0Zk0vaWQvMi8v';
echo base64_decode(strtr($string, '-_,', '+/='))."\n";
