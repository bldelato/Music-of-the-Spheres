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
$incorrectPassword = false;

// Si es un user que se ha deslogueado
if (isset($_GET["logout"])) {
  $_SESSION['user'] = '';
  header('Location: login');
}

// Si es un user que ya está logueado -> Llevo a main
if (!empty($_SESSION['user'])) {
  $user = $_SESSION['user'];
  header('Location: main');
}
else {
  if (!empty($_POST['usuario']) && !empty($_POST['password'])) {
    // Si se ha rellenado el formulario
    $name = $_POST['usuario'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM usuarios WHERE id='$name'";
    $consulta = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($consulta);

    if ($fila['password'] != $password) {
      // Si la contraseña es incorrecta
      $incorrectPassword = true;
    } else {
      // Si está todo correcto
      $user = $name;
      $_SESSION['user'] = $user;
      header('Location: main.php');
    }
  }
}

/* FUNCIONES */

function mostrar_formulario_login() {
    echo '<form class="login-form" action="" method="POST">
		<div class="panel-body form-group form">
			<input type="text" class="form-control" placeholder="Usuario" name="usuario" data-toggle="tooltip" class="tooltip tooltip-top tooltip-arrow"  data-placement="top" title="Máximo 16 caracteres">
			<br>
			<input type="password" class="form-control" placeholder="Contraseña" name="password">
			<br>
			<input type="submit" value="ENTRAR" class="button-form" id="loginbutton">
		</div>
	</form>';
}

function mostrar_errorpassword() {
    echo '<div class="modal fade myModal" role="dialog" id="modalerrorlogin">
	<div class="modal-dialog">
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
    <?php require('head.html'); ?>
	</head>
	<body class="particlesbody">
		<!-- particles.js container -->
		<div id="particles-js"></div>

		<!-- scripts -->
		<script src="js/particles.js"></script>
		<script src="js/app.js"></script>

		<div class="panel panelplanet">
			<?php
        mostrar_formulario_login();
      ?>
		</div>
    <br><br>
    <div id="enlace-registro">
      <a href="register.php">¿Aún no tienes una cuenta? Regístrate</a>
    </div>

    <!-- MENSAJES DE CONTROL DE ERRORES/FINALIZACIÓN -->
		<?php
      if($incorrectPassword) {
        mostrar_errorpassword();
      }
    ?>
	</body>
</html>
