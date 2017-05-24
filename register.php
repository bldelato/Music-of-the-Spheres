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

$user=false;
$incorrectUser = false;
$incorrectPasswords =false;
$incorrectRegister = false;

// Si es un user que ya está logueado -> Llevo a main
if (!empty($_SESSION['user'])) {
    $user = $_SESSION['user'];
    header('Location: main.php');
}
else {
  if (!empty($_POST['usuario']) && !empty($_POST['password']) && !empty($_POST['repassword']) && !empty($_POST['age'])) {
    // Si se ha rellenado el formulario
    $name = $_POST['usuario'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];
    $age = $_POST['age'];

    // Comprobamos que el usuario no existe
    $sql = "SELECT * FROM usuarios WHERE id='$name'";
    $consulta = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($consulta);
    // Si el usuario ya existe
		if(isset($fila['id'])){
			$incorrectUser = true;
		}
    // Si las contraseñas no coinciden
    elseif ($repassword != $password) {
      $incorrectPasswords = true;
    }
		else{
      // Todo correcto
      $sql = "INSERT INTO usuarios(id, password, age) VALUES ('$name','$password','$age')";
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

/* FUNCIONES */

function mostrar_formulario_register(){
  echo '<form class="login-form" action="" method="POST">
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
	echo '<div id="usererrormodal" class="modal fade" role="dialog">
	<div class="modal-dialog">
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
			$("#usererrormodal").modal();
		});
	</script>
</div>';
}

function mostrar_error_passwords() {
	echo '<div id="passworderrormodal" class="modal fade" role="dialog">
	<div class="modal-dialog">
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
			$("#passworderrormodal").modal();
		});
	</script>
</div>';
}

function mostrar_error_register() {
	echo '<div id="registererrormodal" class="modal fade" role="dialog">
	<div class="modal-dialog">
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
			$("#registererrormodal").modal();
		});
	</script>
</div>';
}
?>

<!DOCTYPE html>
<html lang="es" class="particlesbody">
	<head>
    <?php require('head.html'); ?>
	</head>
	<body class="particlesbody">
		<!-- particles.js container -->
		<div id="particles-js"></div>

		<!-- scripts -->
		<script src="js/particles.js"></script>
		<script src="js/app.js"></script>

		<div class="panel panelplanet" id="registerpanel">
      <?php
        mostrar_formulario_register();
      ?>
    </div>
    <br><br>
    <div id="enlace-login">
      <a href="login.php">¿Ya tienes una cuenta? Logueate</a>
    </div>

    <!-- MENSAJES DE CONTROL DE ERRORES/FINALIZACIÓN -->
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
