<?php
/* CONECTARSE A LA BASE DE DATOS */
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$db = mysqli_connect('localhost', 'musicofthesphere', 'adminMS', 'musicofthesphere');
if (!$db) {
    die('Error al conectarse con la base de datos');
}

/* ADMINISTRAR EL USUARIO */

$user = false;

if (!empty($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
    header('Location: login.php');
}

/* ACTUALIZAR LA LISTA DE GRUPOS */

function refrescar_grupos(&$grupos, &$grouptitles){
  global $db;
  $sql = "SELECT * FROM `grupos`";
  $consulta = mysqli_query($db, $sql);
  $grupos=[];
  $grouptitles =[];

  while ($row = mysqli_fetch_assoc($consulta)) {
      $grupos[] = $row;
      $grouptitles[]=$row['title'];
  }
}

/* ACTUALIZAR LA LISTA DE ESTILOS */

function refrescar_estilos(&$musictypes){
  global $db;
  $sql = "SELECT type FROM `typesmusic`";
  $consulta = mysqli_query($db, $sql);
  $musictypes=[];

  while ($row = mysqli_fetch_assoc($consulta)) {
      $musictypes[] = $row['type'];
  }
}
