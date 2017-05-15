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
$noleidosglobales = 0;
$notexistingdestination = false;
$notsent = false;
$messagesent = false;
$notcomplete = false;

if (!empty($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
      header('Location: login.php');
}

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

if(!empty($_POST['complete']) && $_POST['complete']=='completeform'){
  if (!empty($_POST['destinatario']) && !empty($_POST['titulo']) && !empty($_POST['mensaje'])) {
      $destinatario = $_POST['destinatario'];
      $titulo = $_POST['titulo'];
      $mensaje = $_POST['mensaje'];

      $sql = "SELECT * FROM users WHERE id='$destinatario'";
      $consulta = mysqli_query($db, $sql);
      $fila = mysqli_fetch_row($consulta);
      if(!isset($fila[0])){
        $notexistingdestination = true;
      }
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

function mostrar_errornotexistingdestination() {
	echo '<div class="modal fade myModal" role="dialog" id="modalnotexistingdestination">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="text-danger">El destinatario no existe. Por favor, inténtelo de nuevo.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Close</button>
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

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="text-danger">El mensaje no se ha enviado. Por favor, inténtelo de nuevo.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Close</button>
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

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="text-success">El mensaje se ha enviado correctamente.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Close</button>
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
	echo '<div class="modal fade myModal" role="dialog" id="modalmessagesent">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="text-danger">No ha rellenado todos los campos, por favor complete el mensaje.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Close</button>
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

?>

<!DOCTYPE html>
<html lang="es">
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

		<link rel="stylesheet" media="screen" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Amatic+SC|Lobster+Two|Rajdhani|Poiret+One" rel="stylesheet">
    <link rel="stylesheet" href="http://icono-49d6.kxcdn.com/icono.min.css">

    <link rel='shortcut icon' type='image/x-icon' href='img/note-icon.png' />
		<title>MUSIC OF THE SPHERES</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	</head>

	<body id="mainbody">

    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
          <li><a href="main"  class=" navbar-element navbar-main-title barraBasica">Home</a></li>
          <li><a href="mglobal"  class=" navbar-element navbar-main-title barraBasica">Mensajes Globales</a></li>
          <li><a href="main"  class=" navbar-element navbar-main-title barraBasica">Mensajes Grupales</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="login.php?logout" title="Log out"><i class="icono-power"></i></a></li>
      </ul>
    </div>


    <!-- MUNDO IZQUIERDO -> MENSAJES PERSONALES -->
    <div  class="main-panel">

      <ul class="nav nav-tabs nav-justified">
        <li class="active"><a data-toggle="tab" href="#recibidos">Bandeja de entrada</a></li>
        <li><a data-toggle="tab" href="#enviados">Enviados</a></li>
      </ul>

      <div class="tab-content">
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
                    echo "<tr class='pointercursor' onclick='mostrar_mensajeenviado($i, \"{$mensajesenviados[$i]['title']}\", \"{$mensajesenviados[$i]['iddestination']}\", \"{$mensajesenviados[$i]['message']}\")'>
                        <td>{$mensajesenviados[$i]['iddestination']}</td>
                        <td>{$mensajesenviados[$i]['title']}</td>
                      </tr>";
                    ++$i;
                  }?>
              </tbody>
            </table>
        </div>

        <div id="recibidos" class="tab-pane fade  in active">
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
                    echo "<tr>
                        <td onclick='mostrar_mensajerecibido($i, \"{$mensajesrecibidos[$i]['title']}\", \"{$mensajesrecibidos[$i]['idorigin']}\", \"{$mensajesrecibidos[$i]['message']}\")' class='pointercursor'>{$mensajesrecibidos[$i]['idorigin']}</td>
                        <td onclick='mostrar_mensajerecibido($i, \"{$mensajesrecibidos[$i]['title']}\", \"{$mensajesrecibidos[$i]['idorigin']}\", \"{$mensajesrecibidos[$i]['message']}\")' class='pointercursor'>{$mensajesrecibidos[$i]['title']}</td>
                        <td><span class='glyphicon glyphicon-eye-".($mensajesrecibidos[$i]['leido'] ? 'open':'close')."'></span></td>
                        <td onclick='mostrar_respondermensaje(\"{$mensajesrecibidos[$i]['idorigin']}\")'><span class='glyphicon glyphicon-share-alt pointercursor'></span></td>
                      </tr>";

                    ++$i;
                  }?>
              </tbody>
            </table>
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
            <button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- END Modal content-->

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
      				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Close</button>
      			</div>
      		</div>
      	</div>
      </div>
      <!--END  Modal content-->
      <script>
        function mostrar_mensajerecibido(i, messagetitle, messageauthor, messagebody) {
          $('#messagetitler').html(messagetitle);
          $('#messageauthorr').html(messageauthor);
          $('#messagebodyr').html(messagebody);
          $('#messageModalrecibido').modal();
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
    <!--END  Modal content-->


    </div>
    <!--  END MUNDO IZQUIERDO -> MENSAJES GRUPALES -->

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
