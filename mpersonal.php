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
$noleidospersonales = 0;
$noleidosglobales = 0;

if (!empty($_SESSION['user'])) {
  $user = $_SESSION['user'];
} else {
    header('Location: login.php');
}

/* ROL DEL USUARIO */

$sql="SELECT rol FROM users WHERE id='$user'";
$consulta = mysqli_query($db, $sql);
$row=mysqli_fetch_assoc($consulta);
$rol = $row['rol'];

/* HACER LISTA DE MENSAJES (ENVIADOS Y RECIBIDOS) */

$sql = "SELECT * FROM `messages` WHERE iddestination='$user' OR (idorigin='$user' AND iddestination IS NOT NULL) ORDER BY id DESC";
$consulta = mysqli_query($db, $sql);

while ($row = mysqli_fetch_assoc($consulta)) {
  if($row['iddestination'] == $user){
    $mensajesrecibidos[] = $row;
    if($row['leido'] == 0){
      ++$noleidospersonales;
    }
  }
  if($row['idorigin'] == $user){
    $mensajesenviados[] = $row;
  }
}

/* VARIABLES DE CONTROL DE ERRORES/FINALIZACIÓN */

$notexistingdestination = false;
$notsent = false;
$messagesent = false;
$notcomplete = false;

/* ENVIAR MENSAJE PERSONAL */

if(!empty($_POST['complete']) && $_POST['complete']=='completeform'){
  if (!empty($_POST['destinatario']) && !empty($_POST['titulo']) && !empty($_POST['mensaje'])) {
    $destinatario = $_POST['destinatario'];
    $titulo = $_POST['titulo'];
    $mensaje = $_POST['mensaje'];

    $sql = "SELECT * FROM users WHERE id='$destinatario'";
    $consulta = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($consulta);
    // Si el destinatario es erroneo
    if(!isset($fila['id'])){
      $notexistingdestination = true;
    }
    // Se envía el mensaje
    else{
      $sql = "INSERT INTO messages(idorigin, iddestination, title, message, leido) VALUES ('$user','$destinatario','$titulo', '$mensaje', 0)";
      $consulta = mysqli_query($db, $sql);
      if(!$consulta){
        $notsent= true;
      }
      else {
        $messagesent = true;
      }
    }
  }
  else{
    $notcomplete = true;
  }
}

/* FUNCIONES */

function mostrar_errornotexistingdestination() {
	echo '<div class="modal fade myModal" role="dialog" id="modalnotexistingdestination">
  	<div class="modal-dialog">
  		<div class="modal-content">
  			<div class="modal-header">
  				<button type="button" class="close" data-dismiss="modal">&times;</button>
  			</div>
  			<div class="modal-body">
  				<p class="text-danger">El destinatario no existe. Por favor, inténtelo de nuevo.</p>
  			</div>
  			<div class="modal-footer">
  				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Cerrar</button>
  			</div>
  		</div>
  	</div>
  	<script>
  		$( document ).ready(function() {
  			$("#modalnotexistingdestination").modal();
  		});
  	</script>
  </div>';
}

function mostrar_errornotsent() {
	echo '<div class="modal fade myModal" role="dialog" id="modalnotsent">
  	<div class="modal-dialog">
  		<div class="modal-content">
  			<div class="modal-header">
  				<button type="button" class="close" data-dismiss="modal">&times;</button>
  			</div>
  			<div class="modal-body">
  				<p class="text-danger">El mensaje no se ha enviado. Por favor, inténtelo de nuevo.</p>
  			</div>
  			<div class="modal-footer">
  				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Cerrar</button>
  			</div>
  		</div>
  	</div>
  	<script>
  		$( document ).ready(function() {
  			$("#modalnotsent").modal();
  		});
  	</script>
  </div>';
}

function mostrar_errormessagesent() {
	echo '<div class="modal fade myModal" role="dialog" id="modalmessagesent">
  	<div class="modal-dialog">
  		<div class="modal-content">
  			<div class="modal-header">
  				<button type="button" class="close" data-dismiss="modal">&times;</button>
  			</div>
  			<div class="modal-body">
  				<p class="text-success">El mensaje se ha enviado correctamente.</p>
  			</div>
  			<div class="modal-footer">
  				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Cerrar</button>
  			</div>
  		</div>
  	</div>
  	<script>
  		$( document ).ready(function() {
  			$("#modalmessagesent").modal();
  		});
  	</script>
  </div>';
}

function mostrar_errornotcomplete() {
	echo '<div class="modal fade myModal" role="dialog" id="modalnotcompletemessage">
  	<div class="modal-dialog">
  		<div class="modal-content">
  			<div class="modal-header">
  				<button type="button" class="close" data-dismiss="modal">&times;</button>
  			</div>
  			<div class="modal-body">
  				<p class="text-danger">No ha rellenado todos los campos, por favor complete el mensaje.</p>
  			</div>
  			<div class="modal-footer">
  				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Cerrar</button>
  			</div>
  		</div>
  	</div>
  	<script>
  		$( document ).ready(function() {
  			$("#modalnotcompletemessage").modal();
  		});
  	</script>
  </div>';
}
?>

<!DOCTYPE html>
<html lang="es">
	<head>
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>-->
    <script src="lib/js/jquery.min.js"></script>
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">-->
    <link rel="stylesheet" media="screen" type="text/css" href="lib/css/bootstrap.min.css">
    <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
    <script src="lib/js/bootstrap.min.js"></script>
    <!--<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">-->
    <link rel="stylesheet" media="screen" type="text/css" href="lib/css/jquery-ui.css">
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>-->
    <script src="lib/js/jquery-ui.min.js"></script>

    <link rel="stylesheet" media="screen" type="text/css" href="css/style.css">
    <!--<link href="https://fonts.googleapis.com/css?family=Amatic+SC" rel="stylesheet">-->
    <link rel="stylesheet" media="screen" type="text/css" href="lib/css/amaticSC-font.css">
    <!--<link rel="stylesheet" href="http://icono-49d6.kxcdn.com/icono.min.css">-->
    <link rel="stylesheet" media="screen" type="text/css" href="lib/css/icono.min.css">

    <link rel='shortcut icon' type='image/x-icon' href='img/note-icon.png'/>
		<title>MUSIC OF THE SPHERES</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	</head>

	<body id="mainbody">
    <!-- BARRA SUPERIOR -->
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
          <li><a href="main"  class=" navbar-element navbar-main-title barraBasica">Home</a></li>
          <li><a href="mglobal"  class=" navbar-element navbar-main-title barraBasica">Mensajes Globales</a></li>
          <li><a href="mgrupal"  class=" navbar-element navbar-main-title barraBasica">Mensajes Grupales</a></li>
          <?php if($rol == 'admin'):?>
            <li><a href="admin"  class=" navbar-element navbar-main-title barraBasica">Administrar Grupos y Estilos</a></li>
          <?php endif; ?>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="login.php?logout" title="Log out"><i class="icono-power"></i></a></li>
      </ul>
    </div>
    <!-- FINAL BARRA SUPERIOR -->

    <div  class="main-panel">
      <!-- BARRA MENSAJERÍA-->
      <ul class="nav nav-tabs nav-justified">
        <li class="active"><a data-toggle="tab" href="#recibidos">Bandeja de entrada</a></li>
        <li><a data-toggle="tab" href="#enviados">Enviados</a></li>
      </ul>
      <!-- FINAL BARRA MENSAJERÍA-->

      <div class="tab-content">
        <!-- TABLA MENSAJES ENVIADOS -->
        <div id="enviados" class="tab-pane fade">
          <table class="table table-hover table-condensed table-responsive personaltable">
            <thead>
              <tr>
                <th class="personaltitles">Destinatario</th>
                <th class="personaltitles">Título</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $i=0;
                while(isset($mensajesenviados[$i])){
                  $messageText = str_replace("\r\n", "<br>", $mensajesenviados[$i]['message']);
                  $messageText = str_replace("\n", "<br>", $messageText);
                  echo "<tr class='pointercursor' onclick='mostrar_mensajeenviado($i, \"{$mensajesenviados[$i]['title']}\", \"{$mensajesenviados[$i]['iddestination']}\", \"{$messageText}\")'>
                    <td>{$mensajesenviados[$i]['iddestination']}</td>
                    <td>{$mensajesenviados[$i]['title']}</td>
                  </tr>";
                  ++$i;
                }
              ?>
            </tbody>
          </table>
        </div>

        <!-- TABLA MENSAJES RECIBIDOS -->
        <div id="recibidos" class="tab-pane fade in active">
          <table class="table table-hover table-condensed table-responsive personaltable">
            <thead>
              <tr>
                <th class="personaltitles">Autor</th>
                <th class="personaltitles">Título</th>
                <th class="personaltitles" id="th-leido">Leído</th>
                <th class="personaltitles th_responder">Responder</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $i=0;
                while(isset($mensajesrecibidos[$i])){
                  $messageText = str_replace("\r\n", "<br>", $mensajesrecibidos[$i]['message']);
                  $messageText = str_replace("\n", "<br>", $messageText);
                  echo "<tr id='messagerow{$mensajesrecibidos[$i]['id']}'>
                      <td onclick='mostrar_mensajerecibido($i, \"{$mensajesrecibidos[$i]['id']}\", \"{$mensajesrecibidos[$i]['title']}\", \"{$mensajesrecibidos[$i]['idorigin']}\", \"{$messageText}\")' class='pointercursor'>{$mensajesrecibidos[$i]['idorigin']}</td>
                      <td onclick='mostrar_mensajerecibido($i, \"{$mensajesrecibidos[$i]['id']}\", \"{$mensajesrecibidos[$i]['title']}\", \"{$mensajesrecibidos[$i]['idorigin']}\", \"{$messageText}\")' class='pointercursor'>{$mensajesrecibidos[$i]['title']}</td>
                      <td><i class='icono-".($mensajesrecibidos[$i]['leido'] ? 'bookmarkEmpty':'bookmark')."'></i></td>
                      <td onclick='mostrar_respondermensaje(\"{$mensajesrecibidos[$i]['idorigin']}\")'><i class='icono-caretRightCircle  pointercursor'></i></td>
                    </tr>";
                  ++$i;
                }
              ?>
            </tbody>
          </table>
          <!-- BOTÓN ENVIAR MENSAJE -->
          <button type="button" class="btn btn-primary btn-lg button-send-message" onclick='mostrar_enviarmensaje()'>Enviar mensaje</button>
        </div>
      </div>

      <!-- Modal  ENVIADOS -->
      <div id="messageModalenviado" class="modal fade myModal" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title message-title">Para: <span id="messageauthor"></h4>
              <h4 class="modal-title message-title">Título: <span id="messagetitle"></span></h4>
            </div>
            <div class="modal-body">
              <p id="messagebody"></p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
      <!-- FINAL MODAL ENVIADOS-->

      <!-- Modal  RECIBIDOS-->
      <div id="messageModalrecibido" class="modal fade myModal" role="dialog">
      	<div class="modal-dialog">
      		<div class="modal-content">
      			<div class="modal-header">
      				<button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title message-title">De: <span id="messageauthorr"></h4>
              <h4 class="modal-title message-title">Título: <span id="messagetitler"></span></h4>
      			</div>
      			<div class="modal-body">
      				<p id="messagebodyr"></p>
      			</div>
      			<div class="modal-footer">
      				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Cerrar</button>
      			</div>
      		</div>
      	</div>
      </div>
      <!--FINAL MODAL RECIBIDOS-->

      <!-- JAVASCRIPT -->
      <script>
        function mostrar_mensajerecibido(i, id, messagetitle, messageauthor, messagebody) {
          $('#messagetitler').html(messagetitle);
          $('#messageauthorr').html(messageauthor);
          $('#messagebodyr').html(messagebody);
          $('#messageModalrecibido').modal();

          $.ajax({
            url: "marcarleido.php",
            type: "POST",
            data: {
              idmessage: id,
            },
            success: function(data){
              console.log(data);
              $('#messagerow'+id).find('i.icono-bookmark').removeClass('icono-bookmark').addClass('icono-bookmarkEmpty');
            }
          });
        }

        function mostrar_mensajeenviado(i, messagetitle, messagedestination, messagebody) {
          $('#messagetitle').html(messagetitle);
          $('#messageauthor').html(messagedestination);
          $('#messagebody').html(messagebody);
          $('#messageModalenviado').modal();
        }

        function mostrar_enviarmensaje(){
          $('#sendmessageModal').modal();
        }

        function mostrar_respondermensaje(replydestination){
          $('#replydestination').val(replydestination);
          $('#sendmessageModal').modal();
        }
      </script>

      <!-- Modal  ENVIAR MENSAJE-->
      <div id="sendmessageModal" class="modal fade myModalmessage" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title message-title">REDACTAR MENSAJE </h4>
            </div>
            <div class="modal-body">
              <form action="" method="POST" id="formsendmessage">
            		<div class="panel-body form-group form">
          				<input type="text" class="form-control" placeholder="Destinatario" name="destinatario" id="replydestination" value="<?php if(!empty($_POST['destinatario']) && !$messagesent) {
                    echo $_POST['destinatario'];
                  } ?>">
          				<br>
          				<input type="text" class="form-control" placeholder="Título" name="titulo" value="<?php if(!empty($_POST['titulo']) && !$messagesent) {
                    echo $_POST['titulo'];
                  } ?>">
          				<br>
                  <input type="hidden" class="form-control" placeholder="Título" name="complete" value="completeform">
                  <textarea class="form-control" rows="5" name="mensaje" placeholder="Mensaje"><?php if(!empty($_POST['mensaje']) && !$messagesent) {
                    echo $_POST['mensaje'];
                  } ?></textarea>
            		</div>
            	</form>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-default error-button-close" form="formsendmessage">ENVIAR</button>
            </div>
          </div>
        </div>
      </div>
      <!--FINAL MODAL ENVIAR MENSAJE-->
    </div>

    <!-- MENSAJES DE CONTROL DE ERRORES/FINALIZACIÓN -->
    <?php
		  if($notexistingdestination){
				mostrar_errornotexistingdestination();
			}
      elseif($notsent){
        mostrar_errornotsent();
      }
      elseif($messagesent){
        mostrar_errormessagesent();
      }
      elseif($notcomplete){
        mostrar_errornotcomplete();
      }
		?>
  </body>
</html>
