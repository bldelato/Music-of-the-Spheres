<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = mysqli_connect('localhost', 'musicofthesphere', 'adminMS', 'musicofthesphere');
if (!$db) {
    die('Error al conectarse con la base de datos');
}

$user=false;

if (!empty($_COOKIE['user'])) {
    $user = $_COOKIE['user'];
} else {
    if (!empty($_POST['name']) && !empty($_POST['password'])) {
        $name = $_POST['name'];
        $password = $_POST['password'];
        $sql = "SELECT * FROM usuarios WHERE id='$name'";
        $consulta = mysqli_query($db, $sql);
        $fila = mysqli_fetch_row($consulta);
        if ($fila) {
            $user = $name;
            setcookie('user', $user);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
	<head>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<link rel="stylesheet" media="screen" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Amatic+SC" rel="stylesheet">

		<title>MUSIC OF THE SPHERES</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	</head>

	<body id="mainbody">
    <!-- MUNDO IZQUIERDO -> MENSAJES PERSONALES -->
    <a href="#"><div id ="mainpanelleft" class="panel">
      <div class="login-form">
        <h3 class="panel-content"> Mensajes Personales <span class="label label-default" id="label-mercury">3</span></h3>
      </div>
    </div></a>
    <!-- END MUNDO IZQUIERDO -> MENSAJES PERSONALES -->

    <!-- MUNDO IZQUIERDO -> MENSAJES GLOBALES -->
    <a href="#"><div class="panel" id ="mainpanelcenter">
      <div class="login-form">
        <h3 class="panel-content"> Mensajes Globales <span class="label label-default" id="label-mars">10</span> </h3>
      </div>
    </div></a>
    <!-- END MUNDO IZQUIERDO -> MENSAJES GLOBALES -->

    <!-- MUNDO IZQUIERDO -> MENSAJES GRUPALES -->
    <a href="#"><div class="panel" id ="mainpanelright">
      <div class="login-form">
        <h3 class="panel-content"> Mensajes Grupales <span class="label label-default" id="label-moon">5</span></h3>
      </div>
    </div></a>
    <!--  END MUNDO IZQUIERDO -> MENSAJES GRUPALES -->
  </body>
</html>
