<?php

/*
$fecha = date("Y-m-d H:i:s","1505840962");
echo $fecha."<br>";
$nuevafecha = strtotime ('-5 hour',"1505840962");
echo $fecha = date("Y-m-d H:i:s",$nuevafecha);
*/



$configdata = "Tzo4OiJzdGRDbGFzcyI6MTU6e3M6NzoibmV0d29yayI7czoxMToidXRwbC5lZHUuZWMiO3M6NToidGl0bGUiO3M6MTg6IlJlZCBkZSBBbGV4IFJvbWVybyI7czo4OiJmZWVkdHlwZSI7czo1OiJncm91cCI7czo2OiJmZWVkaWQiO2k6MTI4NTY2NDc7czoxNDoiZGVmYXVsdGdyb3VwaWQiO3M6MDoiIjtzOjE4OiJkZWZhdWx0dG9jYW5vbmljYWwiO2k6MTtzOjY6InVzZXNzbyI7aTowO3M6MTM6InNob3dvZ3ByZXZpZXciO2k6MDtzOjU6ImZldGNoIjtpOjA7czo3OiJwcml2YXRlIjtpOjA7czoyMDoiaWdub3JlX2Nhbm9uaWNhbF91cmwiO2k6MDtzOjEwOiJwcm9tcHR0ZXh0IjtzOjA6IiI7czoxMDoic2hvd2hlYWRlciI7aToxO3M6MTU6ImhpZGVOZXR3b3JrTmFtZSI7aTowO3M6MTA6InNob3dmb290ZXIiO2k6MTt9";
$config_object = unserialize(base64_decode($configdata));
echo "<pre>";
print_r($config_object);

return;


$config_object->title = "Luis Rios";
$config_object->feedid = 30004000;

$configdata = base64_encode(serialize($config_object));
echo "<pre>";
print_r($configdata);
echo "</pre>";

?>