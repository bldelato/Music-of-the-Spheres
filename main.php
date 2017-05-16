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

if(!empty($_POST['eliminar']) && $_POST['eliminar']=='eliminartipo' && !empty($_POST['tipoparaeliminar'])){
  $type = $_POST['tipoparaeliminar'];

  $sql = "DELETE FROM `musictypes` WHERE id='$user' AND type='$type'";
  $consulta = mysqli_query($db, $sql);
}

if(!empty($_POST['añadir']) && $_POST['añadir']=='añadirtipo' && !empty($_POST['tipoparaañadir'])){
  $type = $_POST['tipoparaañadir'];

  $sql = "INSERT INTO `musictypes`(`type`, `id`) VALUES ('$type', '$user')";
  $consulta = mysqli_query($db, $sql);
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

$sql = "SELECT * FROM `users` WHERE id='$user'";
$consulta = mysqli_query($db, $sql);
$usuario = mysqli_fetch_assoc($consulta);


$sql = "SELECT DISTINCT * FROM `musictypes` WHERE type NOT IN (SELECT type FROM `musictypes` WHERE id='$user') OR id='$user'";
$consulta = mysqli_query($db, $sql);
$addedtypes=[];
$nonaddedtypes=[];

while ($row = mysqli_fetch_assoc($consulta)) {
  if($row['id']==$user){
    if(!in_array($row['type'],$addedtypes)){
      $addedtypes[] = $row['type'];
    }
  }
  else{
    if(!in_array($row['type'],$nonaddedtypes)){
    $nonaddedtypes[]=$row['type'];
    }
  }
}



?>

<!DOCTYPE html>
<html lang="es" class="particlesbody">
	<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">-->
    <link rel="stylesheet" media="screen" type="text/css" href="lib/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!--<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">-->
    <link rel="stylesheet" media="screen" type="text/css" href="lib/jquery-ui.css">
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

      <!-- PANEL BODY -->
      <div class="panel-body">

      <!-- ACCORDION -->
        <div class="panel-group" id="accordion">

          <!-- PART 1 ACCORDION-->
          <div class="panel-default">
            <div class="panel-heading">
              <h4 class="panel-title  perfil-panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Estilos de música</a>
              </h4>
            </div>
            <div id="collapse1" class="panel-collapse collapse">
              <div class="panel-body">
                <table class="table table-hover table-condensed table-responsive">
                    <thead>
                        <tr>
                          <th class="personaltitles" colspan="2">Tipo añadido</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i=0;
                        if(empty($addedtypes)){
                          echo "<tr >
                              <td class='musictype_row'>No tienes ningún tipo de música preferido. Si quieres puedes añadir uno haciendo click en los estilos de abajo.</td>
                            </tr>";
                        }
                        while(isset($addedtypes[$i])){
                          echo "<tr>
                              <td class='defaultcursor myModalmessage'>{$addedtypes[$i]}</td>
                              <td class='th_responder pointercursor myModalmessage' onclick='mostrar_eliminartipo(\"{$addedtypes[$i]}\")'><i class='icono-trash pointercursor'></i></td>
                            </tr>";
                          ++$i;
                        }
                      ?>
                    </tbody>
                  </table>
                  <table class="table table-hover table-condensed table-responsive">
                    <thead>
                        <tr>
                          <th class="personaltitles" colspan="2">Tipos para añadir</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i=0;
                        if(empty($nonaddedtypes)){
                          echo "<tr >
                              <td class='musictype_row'>No tienes ningún tipo de música sin añadir. Eres todo un melómano!</td>
                            </tr>";
                        }
                        while(isset($nonaddedtypes[$i])){
                          echo "<tr>
                              <td  class='defaultcursor myModalmessage'>{$nonaddedtypes[$i]}</td>
                              <td  class='th_responder pointercursor' onclick='mostrar_añadirtipo(\"{$nonaddedtypes[$i]}\")'><i class='icono-plus'></i></td>
                            </tr>";
                          ++$i;
                        }
                      ?>
                    </tbody>
                  </table>
                </div>
            </div>
          </div>
            <!-- END PART 1 ACCORDION-->

            <!-- PART 2 ACCORDION-->
            <div class="panel-default">
             <div class="panel-heading">
               <h4 class="panel-title  perfil-panel-title">
                 <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Edad</a>
               </h4>
             </div>
             <div id="collapse2" class="panel-collapse collapse">
               <div class="panel-body text-age-perfil">
                 Tienes <?php echo $usuario['age'];?> años.
               </div>
             </div>
           </div>
            <!-- END PART 2 ACCORDION-->
        </div>
        <!-- END ACCORDION -->
      </div>
      <!-- END PANEL BODY -->

    </div>
    <!-- END PERFIL -->

    <script>
      function mostrar_eliminartipo(tipoaeliminar){
        $('#erasetype').val(tipoaeliminar);
        $('#tipoaeliminar').html(tipoaeliminar);
        $('#erasetypemodal').modal();
      }
      function mostrar_añadirtipo(tipoaañadir){
        $('#addtype').val(tipoaañadir);
        $('#tipoaañadir').html(tipoaañadir);
        $('#addtypemodal').modal();
      }
    </script>

    <!-- Modal  ELIMINAR TIPO-->
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
                ¿Estás seguro de querer eliminar <span id="tipoaeliminar" class="text-danger musictype"></span> de tu lista de música?
                <input type="hidden" class="form-control" name="eliminar" value="eliminartipo">
                <input type="hidden" class="form-control" name="tipoparaeliminar" value="" id="erasetype">
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

  <!-- Modal  AÑADIR TIPO-->
  <div id="addtypemodal" class="modal fade myModalmessage" role="dialog">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title message-title">Añadir TIPO DE MÚSICA </h4>
      </div>
      <div class="modal-body">
        <form action="" method="POST" id="formaddtype">
          <div class="panel-body form-group form">
              ¿Estás seguro de querer añadir <span id="tipoaañadir" class="text-success musictype"></span> a tu lista de música?
              <input type="hidden" class="form-control" name="añadir" value="añadirtipo">
              <input type="hidden" class="form-control" name="tipoparaañadir" value="" id="addtype">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default error-button-close" form="formaddtype">Añadir</button>
      </div>
    </div>
  </div>
</div>
<!--END  Modal content-->

    <!-- MUNDO IZQUIERDO -> MENSAJES PERSONALES -->
    <a href="mpersonal"><div id ="mainpanelleft" class="panel panelplanet main-panel">
      <div class="login-form">
        <h3 class="panel-content"> Mensajes Personales <span class="label label-default" id="label-mercury"><?php echo $noleidospersonales; ?></span></h3>
      </div>
    </div></a>
    <!-- END MUNDO IZQUIERDO -> MENSAJES PERSONALES -->

    <!-- MUNDO IZQUIERDO -> MENSAJES GLOBALES -->
    <a href="mglobal"><div class="panel panelplanet main-panel" id ="mainpanelcenter">
      <div class="login-form">
        <h3 class="panel-content"> Mensajes Globales </h3>
      </div>
    </div></a>
    <!-- END MUNDO IZQUIERDO -> MENSAJES GLOBALES -->

    <!-- MUNDO IZQUIERDO -> MENSAJES GRUPALES -->
    <a href="#"><div class="panel panelplanet main-panel" id ="mainpanelright">
      <div class="login-form">
        <h3 class="panel-content"> Mensajes Grupales <span class="label label-default" id="label-moon">5</span></h3>
      </div>
    </div></a>
    <!--  END MUNDO IZQUIERDO -> MENSAJES GRUPALES -->

    <?php if($usuario['rol'] == 'admin'):?>
    <div>
      <a href="admin.php"><button type="button" class="btn btn-primary btn-lg button-send-message groupsbutton">Administrar grupos</button></a>
    </div>
  <?php endif; ?>
  </body>
</html>
