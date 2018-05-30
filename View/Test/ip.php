<?php
  
 function clientIP(){   
  $cIP = getenv('REMOTE_ADDR');   
  $cIP1 = getenv('HTTP_X_FORWARDED_FOR');   
  $cIP2 = getenv('HTTP_CLIENT_IP');   
  $cIP1 ? $cIP = $cIP1 : null;   
  $cIP2 ? $cIP = $cIP2 : null;   
  return $cIP;   
 }   
 function serverIP(){   
  return gethostbyname($_SERVER["SERVER_NAME"]);   
 }   
  
    
   
$clientIp = clientIP();   
$serverIp = serverIP();   
    
echo 'Client IP is ',$clientIp,'<br />';   
echo 'Server IP is ',$serverIp,'<br />';  

?>