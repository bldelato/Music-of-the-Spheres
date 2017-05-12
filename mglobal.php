<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$db = mysqli_connect('localhost', 'musicofthesphere', 'adminMS', 'musicofthesphere');
if (!$db) {
    die('Error al conectarse con la base de datos');
}

$user = false;

if (!empty($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
      header('Location: login.php');
}

$notsent = false;
$messagesent = false;
$notcomplete = false;

if(!empty($_POST['complete']) && $_POST['complete']=='completeform'){
  if (!empty($_POST['titulo']) && !empty($_POST['mensaje'])) {
      $titulo = $_POST['titulo'];
      $mensaje = $_POST['mensaje'];


      $sql = "INSERT INTO messages(idorigin, iddestination, title, message, leido) VALUES ('$user',NULL,'$titulo', '$mensaje', 0)";
      $consulta = mysqli_query($db, $sql);
      if(!$consulta){
        $notsent= true;
      }
      else {
        $messagesent = true;
      }
  }
  else{
    $notcomplete = true;
  }
}

$sql = "SELECT * FROM `messages` WHERE iddestination IS NULL ORDER BY id DESC";
$consulta = mysqli_query($db, $sql);

while ($row = mysqli_fetch_assoc($consulta)) {
  $mensajesrecibidos[] = $row;
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
          <li><a href="mpersonal"  class=" navbar-element navbar-main-title barraBasica">Mensajes Personales</a></li>
          <li><a href="main"  class=" navbar-element navbar-main-title barraBasica">Mensajes Grupales</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#" title="Perfil"><i class="icono-user"></i></a></li>
        <li><a href="login.php?logout" title="Log out"><i class="icono-power"></i></a></li>
      </ul>
    </div>


    <!-- MUNDO IZQUIERDO -> MENSAJES PERSONALES -->
    <div  class="main-panel">
      <table class="table table-hover table-condensed table-responsive personaltable">
          <thead>
              <tr>
                <th class="personaltitles">Autor</th>
                <th class="personaltitles">Título</th>
              </tr>
          </thead>
          <tbody>
            <?php
              $i=0;
              while(isset($mensajesrecibidos[$i])){
                echo "<tr onclick='mostrar_mensaje($i, \"{$mensajesrecibidos[$i]['title']}\", \"{$mensajesrecibidos[$i]['idorigin']}\", \"{$mensajesrecibidos[$i]['message']}\")' class='pointercursor'>
                    <td>{$mensajesrecibidos[$i]['idorigin']}</td>
                    <td>{$mensajesrecibidos[$i]['title']}</td>
                  </tr>";

                ++$i;
              }?>
          </tbody>
        </table>
        <button type="button" class="btn btn-primary btn-lg button-send-message" onclick='mostrar_enviarmensaje()'>Enviar mensaje</button>

        <!-- Modal content-->
        <div id="messageModal" class="modal fade myModal" role="dialog">
      	<div class="modal-dialog">

      		<div class="modal-content">
      			<div class="modal-header">
      				<button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title message-title">De: <span id="messageauthor"></h4>
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
      <!-- Modal content-->
      <script>
        function mostrar_mensaje(i, messagetitle, messageauthor, messagebody) {
          $('#messagetitle').html(messagetitle);
          $('#messageauthor').html(messageauthor);
          $('#messagebody').html(messagebody);
          $('#messageModal').modal();
        }
        function mostrar_enviarmensaje(){
          $('#sendmessageglobalModal').modal();
        }
      </script>

      <!-- Modal  ENVIAR MENSAJE-->
      <div id="sendmessageglobalModal" class="modal fade myModalmessage" role="dialog">
        <div class="modal-dialog">

          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title message-title">REDACTAR MENSAJE </h4>
            </div>
            <div class="modal-body">
              <form action="" method="POST" id="formsendmessage">
            		<div class="panel-body form-group form">
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
    <!--  MENSAJES GRUPALES -->
    <?php
    if($notsent){
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
