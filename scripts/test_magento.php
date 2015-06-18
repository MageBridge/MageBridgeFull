<?php 
$string = 'aHR0cDovL21hZ2VicmlkZ2UxLnlpcmVvLWRldi5jb20vc2hvcC9jYXRhbG9nc2VhcmNoL3Jlc3VsdD9xPXBob25lLw,,';
echo base64_decode(strtr($string, '-_,', '+/='))."\n";
