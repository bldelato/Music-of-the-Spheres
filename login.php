<?php
/*setcookie('user', '');
exit;*/
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$db = mysqli_connect('localhost', 'musicofthesphere', 'adminMS', 'musicofthesphere');
if (!$db) {
    die('Error al conectarse con la base de datos');
}

$user=false;
$incorrectPassword = false;

if(isset($_GET["logout"])){
  $_SESSION['user'] = '';
  header('Location: login.php');
}

if (!empty($_SESSION['user'])) {
    $user = $_SESSION['user'];
    header('Location: main.php');
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
            $_SESSION['user'] = $user;
            header('Location: main.php');
        }
    }
}

function mostrar_formulario(){
  echo '
	<form class="login-form" action="" method="POST">
		<div class="panel-body form-group form">
				<input type="text" class="form-control" placeholder="Usuario" name="usuario" data-toggle="tooltip" class="tooltip tooltip-top tooltip-arrow"  data-placement="top" title="Máximo 16 caracteres">
				<br>
				<input type="password" class="form-control" placeholder="Contraseña" name="password">
				<br>
				<input type="submit" value="ENTRAR" class="button-form" id="loginbutton">
		</div>
	</form>';
}

function mostrar_error() {
	echo '<div class="modal fade myModal" role="dialog" id="modalerrorlogin">
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
				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Cerrar</button>
			</div>
		</div>

	</div>
	<script>
		$( document ).ready(function() {
			$("#modalerrorlogin").modal();
		});
	</script>
</div>';
}

?>
<!DOCTYPE html>
<html lang="es" class="particlesbody">
	<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

		<link rel="stylesheet" media="screen" type="text/css" href="css/style.css">
		<link href="https://fonts.googleapis.com/css?family=Amatic+SC" rel="stylesheet">

    <link rel='shortcut icon' type='image/x-icon' href='img/note-icon.png' />
		<title>MUSIC OF THE SPHERES</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	</head>
	<body class="particlesbody">
		<!-- particles.js container -->
		<div id="particles-js"></div>

		<!-- scripts -->
		<script src="js/particles.js"></script>
		<script src="js/app.js"></script>

		<div class="panel panelplanet">
			<?php
      	mostrar_formulario();
			?>
		</div>
    <br><br>
    <div id="enlace-registro">
      <a href="register.php">¿Aún no tienes una cuenta? Regístrate</a>
    </div>
		<?php
		  if($incorrectPassword){
				mostrar_error();
			}
		?>
	</body>
</html>
