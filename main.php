<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$db = mysqli_connect('localhost', 'musicofthesphere', 'adminMS', 'musicofthesphere');
if (!$db) {
    die('Error al conectarse con la base de datos');
}

$user = false;
$noleidospersonales = 0;

if (!empty($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
      header('Location: login.php');
}

$sql = "SELECT * FROM `messages` WHERE iddestination='$user'";
$consulta = mysqli_query($db, $sql);

while ($row = mysqli_fetch_row($consulta)) {
  $mensajesrecibidos[] = $row;
  if($row[5] == 0){
    if(($row[2] == $user)){
      ++$noleidospersonales;
    }
  }
}

$sql = "SELECT DISTINCT * FROM `musictypes` WHERE type NOT IN (SELECT type FROM `musictypes` WHERE id='$user') OR id='$user'";
$consulta = mysqli_query($db, $sql);

while ($row = mysqli_fetch_row($consulta)) {
  if($row[1]==$user){
    $addedtypes[] = $row;
  }
  else{
    $nonaddedtypes[]=$row;
  }
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
    <link rel="stylesheet" href="http://icono-49d6.kxcdn.com/icono.min.css">

    <link rel='shortcut icon' type='image/x-icon' href='img/note-icon.png' />
		<title>MUSIC OF THE SPHERES</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	</head>

	<body id="mainbody" class="particlesbody">

    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
          <li><a href="#"  class="navbar-element navbar-main-title barraBasica">Home</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li  class="iconos-nav"><a data-toggle="collapse" href="#collapseperfil" title="Perfil"><i class="icono-user"></i></a></li>
        <li  class="iconos-nav"><a href="login.php?logout" title="Log out"><i class="icono-power"></i></a></li>
      </ul>
    </div>

    <!-- PERFIL -->
    <div id="collapseperfil" class="panel-collapse collapse">
      <div class="panel-heading panel-title">PERFIL</div>
      <div class="panel-body">
        <h4>Estilos de música<h4>
          <table class="table table-hover table-condensed table-responsive">
              <thead>
                  <tr>
                    <th class="personaltitles">Tipo añadido</th>
                  </tr>
              </thead>
              <tbody>
          <?php
            $i=0;
            if(empty($addedtypes)){
              echo "No tienes ningún tipo de música preferido. Si quieres puedes añadir uno haciendo click abajo.";
            }
            while(isset($addedtypes[$i])){
              echo "<tr>
                  <td onclick='mostrar_eliminartipo( \"{$addedtypes[$i][0]}\")' class='pointercursor'>{$addedtypes[$i][0]}</td>
                </tr>";

              ++$i;
            }?>
        </tbody>
      </table>

      <table class="table table-hover table-condensed table-responsive">
          <thead>
              <tr>
                <th class="personaltitles">Tipos para añadir</th>
              </tr>
          </thead>
          <tbody>
      <?php
        $i=0;
        if(empty($addedtypes)){
          echo "No tienes ningún tipo de música preferido. Si quieres puedes añadir uno haciendo click abajo.";
        }
        while(isset($nonaddedtypes[$i])){
          echo "<tr>
              <td >{$nonaddedtypes[$i][0]}</td>
            </tr>";

          ++$i;
        }?>
    </tbody>
  </table>
      </div>
      <div class="panel-footer">Edad</div>
    </div>
    <!-- END PERFIL -->

    <script>
      function mostrar_eliminartipo(tipoaeliminar){
        $('#tipoaeliminar').html(tipoaeliminar);
        $('#erasetypemodal').modal();
      }
      function mostrar_añadirtipo(replydestination){
        $('#replydestination').val(replydestination);
        $('#erasetypemodal').modal();
      }
    </script>

    <!-- Modal  ELIMINAR MENSAJE-->
    <div id="erasetypemodal" class="modal fade myModalmessage" role="dialog">
    <div class="modal-dialog">

      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title message-title">ELIMINAR TIPO DE MÚSICA </h4>
        </div>
        <div class="modal-body">
          <form action="" method="POST" id="formerasetype">
            <div class="panel-body form-group form">
                ¿Estás seguro de querer eliminar <span id="tipoaeliminar" class="text-danger"></span> de tu lista de música?
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-default error-button-close" form="formerasetype">Eliminar</button>
        </div>
      </div>
    </div>
  </div>
  <!--END  Modal content-->

    <!-- MUNDO IZQUIERDO -> MENSAJES PERSONALES -->
    <a href="mpersonal"><div id ="mainpanelleft" class="panel main-panel">
      <div class="login-form">
        <h3 class="panel-content"> Mensajes Personales <span class="label label-default" id="label-mercury"><?php echo $noleidospersonales; ?></span></h3>
      </div>
    </div></a>
    <!-- END MUNDO IZQUIERDO -> MENSAJES PERSONALES -->

    <!-- MUNDO IZQUIERDO -> MENSAJES GLOBALES -->
    <a href="mglobal"><div class="panel main-panel" id ="mainpanelcenter">
      <div class="login-form">
        <h3 class="panel-content"> Mensajes Globales </h3>
      </div>
    </div></a>
    <!-- END MUNDO IZQUIERDO -> MENSAJES GLOBALES -->

    <!-- MUNDO IZQUIERDO -> MENSAJES GRUPALES -->
    <a href="#"><div class="panel main-panel" id ="mainpanelright">
      <div class="login-form">
        <h3 class="panel-content"> Mensajes Grupales <span class="label label-default" id="label-moon">5</span></h3>
      </div>
    </div></a>
    <!--  END MUNDO IZQUIERDO -> MENSAJES GRUPALES -->


  </body>
</html>
