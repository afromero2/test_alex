<?php
 
require("config.php");

global $CFG;

echo "TEST NIVEL7<br>";

$path_info = explode("=", $_SERVER['QUERY_STRING']);
$url = $path_info[1];
echo $url."<br><br>";

echo "TEST DE PATH_INFO<br>";

echo "Valor:".$_SERVER['PATH_INFO'];

echo "<pre>";
print_r(var_export($_SERVER));
echo "</pre>";

return;



$fp = fsockopen("172.16.50.19", 27017, $errno, $errstr, 30); 
if($fp){
 print_object("Si hay conexion");
 print_object($fp);
}else{
   print_object("No hay conexion");
   echo "$errstr ($errno)"; 
}

fclose($fp); 

/*
function GetPing($ip=NULL) {
 if(empty($ip)) {$ip = $_SERVER['REMOTE_ADDR'];}
 if(getenv("OS")=="Windows_NT") {
  $exec = exec("ping -n 3 -l 64 ".$ip);
  return end(explode(" ", $exec ));
 }
 else {
  $exec = exec("ping -c 3 -s 64 -t 64 ".$ip);
  $array = explode("/", end(explode("=", $exec )) );
  return ceil($array[1]) . 'ms';
 }
}
 
$ip = '172.16.50.19';
 
if (GetPing($ip) == 'perdidos),') {
    echo 'Tiempo agotado';
} else if (GetPing($ip) == '0ms') {
    echo 'servidor apagado';
} else {
    echo 'servidor con conectividad';
}
*/

?>
