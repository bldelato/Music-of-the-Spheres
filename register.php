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
$incorrectUser = false;
$incorrectPasswords =false;
$incorrectRegister = false;

if (!empty($_SESSION['user'])) {
    $user = $_SESSION['user'];
    header('Location: main.php');
} else {
    if (!empty($_POST['usuario']) && !empty($_POST['password']) && !empty($_POST['repassword']) && !empty($_POST['age'])) {
        $name = $_POST['usuario'];
        $password = $_POST['password'];
        $repassword = $_POST['repassword'];
        $age = $_POST['age'];
        $sql = "SELECT * FROM users WHERE id='$name'";
        $consulta = mysqli_query($db, $sql);
        $fila = mysqli_fetch_row($consulta);
				if(isset($fila[0])){
					$incorrectUser = true;
				}
        elseif ($repassword != $password) {
          $incorrectPasswords = true;
        }
				else{
          $sql = "INSERT INTO users(id, password, age) VALUES ('$name','$password','$age')";
          $consulta = mysqli_query($db, $sql);
          if(!$consulta){
            $incorrectRegister = true;
          }
          else {
            $user = $name;
            $_SESSION['user'] = $user;
            header('Location: main.php');
          }
        }
    }
}

function mostrar_formulario(){
  echo '
	<form class="login-form" action="" method="POST">
		<div class="panel-body form-group form"  id="form-style-register">
				<input type="text" class="form-control" placeholder="Usuario" name="usuario" data-toggle="tooltip" class="tooltip tooltip-top tooltip-arrow"  data-placement="top" title="Máximo 16 caracteres">
				<br>
				<input type="password" class="form-control" placeholder="Contraseña" name="password" data-toggle="tooltip" class="tooltip tooltip-top tooltip-arrow"  data-placement="top" title="Máximo 20 caracteres">
				<br>
        <input type="password" class="form-control" placeholder="Repite contraseña" name="repassword" data-toggle="tooltip" class="tooltip tooltip-top tooltip-arrow"  data-placement="top" title="Máximo 20 caracteres">
				<br>
        <input type="number" min="1" max="100" class="form-control" placeholder="Edad" name="age">
				<br>
				<input type="submit" value="REGISTRARSE" class="button-form" id="registerbutton">
		</div>
	</form>';
}

function mostrar_error_user() {
	echo '<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="text-danger">Ese nombre de usuario ya está en uso. Por favor, inténtelo de nuevo.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Cerrar</button>
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

function mostrar_error_passwords() {
	echo '<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="text-danger">Las contraseñas no coinciden. Por favor, inténtelo de nuevo.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Cerrar</button>
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

function mostrar_error_register() {
	echo '<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="text-danger">No se ha realizado el registro correctamente. Por favor, inténtelo de nuevo.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Cerrar</button>
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
<html lang="es" class="particlesbody">
	<head>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<link rel="stylesheet" media="screen" type="text/css" href="css/style.css">
		<link href="https://fonts.googleapis.com/css?family=Amatic+SC" rel="stylesheet">

    <link rel='shortcut icon' type='image/x-icon' href='/img/note-icon.png' />
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

		<div class="panel panelplanet" id="registerpanel">

      <?php
        mostrar_formulario();
      ?>
      </div>
    <br><br>
    <div id="enlace-login">
      <a href="login.php">¿Ya tienes una cuenta? Logueate</a>
    </div>
		<?php
		  if($incorrectUser){
				mostrar_error_user();
			}
      elseif($incorrectPasswords){
        mostrar_error_passwords();
      }
      elseif($incorrectRegister){
        mostrar_error_register();
      }
		?>
	</body>
</html>
