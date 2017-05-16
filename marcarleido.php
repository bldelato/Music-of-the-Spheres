<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$db = mysqli_connect('localhost', 'musicofthesphere', 'adminMS', 'musicofthesphere');
if (!$db) {
    die('Error al conectarse con la base de datos');
}

$id=$_POST['idmessage'];

$sql = "UPDATE `messages` SET leido = 1 WHERE id = '$id'";
$consulta = mysqli_query($db, $sql);

if(!$consulta){
  echo 'error';
}
else {
  echo 'ok';
}

?>
