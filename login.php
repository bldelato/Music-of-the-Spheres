<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = mysqli_connect('localhost', 'musicofthesphere', 'adminMS', 'musicofthesphere');
if (!$db) {
    die('Error al conectarse con la base de datos');
}

$user=false;
$incorrectPassword = false;

if (!empty($_COOKIE['user'])) {
    $user = $_COOKIE['user'];
} else {
    if (!empty($_POST['usuario']) && !empty($_POST['password'])) {
        $name = $_POST['usuario'];
        $password = $_POST['password'];
        $sql = "SELECT * FROM users WHERE id='$name'";
        $consulta = mysqli_query($db, $sql);
        $fila = mysqli_fetch_row($consulta);
				if($fila[1] != $password){
					$incorrectPassword = true;
				}
				else{
            $user = $name;
            setcookie('user', $user);
        }
    }
}

function mostrar_formulario(){
  echo '
	<form class="login-form" action="" method="POST">
		<div class="panel-body form-group form" id="form-style">
				<input type="text" class="form-control" placeholder="Usuario" name="usuario" data-toggle="tooltip" class="tooltip tooltip-top tooltip-arrow"  data-placement="top" title="Máximo 16 caracteres">
				<br>
				<input type="password" class="form-control" placeholder="Contraseña" name="password">
				<br>
				<input type="submit" value="LOG IN" class="button-form">
		</div>
	</form>';
}

function mostrar_error() {
	echo '<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="text-danger">Contraseña incorrecta. Por favor, inténtelo de nuevo.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
	<script>
		$( document ).ready(function() {
			$("#myModal").modal();
		});
	</script>
</div>';
}

?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<link rel="stylesheet" media="screen" type="text/css" href="css/style.css">
		<link href="https://fonts.googleapis.com/css?family=Amatic+SC" rel="stylesheet">

		<title>MUSIC OF THE SPHERES</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	</head>
	<body>
		<!-- particles.js container -->
		<div id="particles-js"></div>

		<!-- scripts -->
		<script src="js/particles.js"></script>
		<script src="js/app.js"></script>

		<div class="panel">
			<?php
      	mostrar_formulario();
			?>
		</div>
		<?php
		  if($incorrectPassword){
				mostrar_error();
			}
		   else {

		  }
		?>
	</body>
</html>
