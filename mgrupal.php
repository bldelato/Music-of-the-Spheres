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

$sql = "SELECT * FROM groups, groupsrelation WHERE groupsrelation.iduser='$user' AND groups.title=groupsrelation.grouptitle";
$consulta = mysqli_query($db, $sql);
$grupos=[];
$grouptitles =[];

while ($row = mysqli_fetch_assoc($consulta)) {
  $grupos[] = $row;
  $grouptitles[]=$row['title'];

  $sql="SELECT * FROM groupmessages WHERE destinationgroup='{$row['title']}' ORDER BY id DESC";
  $consulta2 = mysqli_query($db, $sql);
  $mensajes[$row['title']] = [];
  while ($row2 = mysqli_fetch_assoc($consulta2)) {
    $mensajes[$row['title']][] = $row2;
  }
}

$notcreated = false;
$groupcreated = false;
$messagesent = false;
$notsent = false;
$notcomplete = false;
$repeatedtitle=false;

$sql="SELECT rol FROM users WHERE id='$user'";
$consulta = mysqli_query($db, $sql);
$row=mysqli_fetch_assoc($consulta);
$rol = $row['rol'];


$sql = "SELECT DISTINCT type FROM `musictypes`";
$consulta = mysqli_query($db, $sql);
$musictypes=[];
while ($row = mysqli_fetch_assoc($consulta)) {
  $musictypes[] = $row['type'];
}

$sql = "SELECT * FROM `messages` WHERE iddestination IS NULL ORDER BY id DESC";
$consulta = mysqli_query($db, $sql);

while ($row = mysqli_fetch_assoc($consulta)) {
  $mensajesrecibidos[] = $row;
}


if(!empty($_POST['complete']) && $_POST['complete']=='completeform'){
  if (!empty($_POST['titulo']) && !empty($_POST['mensaje'])) {
      $titulo = $_POST['titulo'];
      $mensaje = $_POST['mensaje'];
      $group =  $_POST['nombregrupo'];

      $sql = "INSERT INTO groupmessages(idorigin, destinationgroup, title, message) VALUES ('$user','$group','$titulo', '$mensaje')";
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

function mostrar_errornotcreated() {
	echo '<div class="modal fade myModal" role="dialog" id="modalnotcreated">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="text-danger">El grupo no se ha creado. Por favor, inténtelo de nuevo.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Cerrar</button>
			</div>
		</div>

	</div>
	<script>
		$( document ).ready(function() {
			$("#modalnotcreated").modal();
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

		<!-- Modal content-->
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
	echo '<div class="modal fade myModal" role="dialog" id="modalmessagesent">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="text-danger">No ha rellenado todos los campos, por favor complete el formulario.</p>
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

function mostrar_repeatedtitle() {
	echo '<div class="modal fade myModal" role="dialog" id="modalrepeatedtitle">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="text-danger">Ese título ya está en uso, por favor inténtelo de nuevo.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Cerrar</button>
			</div>
		</div>

	</div>
	<script>
		$( document ).ready(function() {
			$("#modalrepeatedtitle").modal();
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
          <li><a href="mglobal"  class=" navbar-element navbar-main-title barraBasica">Mensajes Globales</a></li>
          <?php if($rol == 'admin'):?>
          <li><a href="admin"  class=" navbar-element navbar-main-title barraBasica">Administrar Grupos</a></li>
        <?php endif; ?>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="login.php?logout" title="Log out"><i class="icono-power"></i></a></li>
      </ul>
    </div>


    <!-- MUNDO IZQUIERDO -> MENSAJES PERSONALES -->
    <div  class="main-panel">
      <ul class="nav nav-tabs nav-justified">
        <?php
          foreach ($grupos as $i=>$grupo){
            echo  "<li><a data-toggle='tab' href='#tablamensajes-$i'>{$grupo['title']}</a></li>";
          }
        ?>
      </ul>

      <!-- LO DE DENTRO DE LOS GRUPOS-->
      <div class="tab-content">
        <?php
        foreach ($grupos as $i=>$grupo){
          echo "<div id='tablamensajes-$i' class='tab-pane fade'>
            <table class='table table-hover table-condensed table-responsive personaltable'>
                <thead>
                    <tr>
                      <th class='personaltitles'>Autor</th>
                      <th class='personaltitles'>Título</th>
                    </tr>
                </thead>
                <tbody>";
                    $i=0;
                    while(isset($mensajes[$grupo['title']][$i])){
                      echo "<tr onclick='mostrar_mensaje($i, \"{$mensajes[$grupo['title']][$i]['title']}\", \"{$mensajes[$grupo['title']][$i]['idorigin']}\", \"{$mensajes[$grupo['title']][$i]['message']}\")' class='pointercursor'>
                          <td>{$mensajes[$grupo['title']][$i]['idorigin']}</td>
                          <td>{$mensajes[$grupo['title']][$i]['title']}</td>
                        </tr>";

                      ++$i;
                    }
                echo "</tbody>
              </table>
              <button type='button' class='btn btn-primary btn-lg button-send-message' onclick='mostrar_enviarmensaje(\"{$grupo['title']}\")''>Enviar mensaje</button>
          </div>";
        }
        ?>
      </div>
      <!-- END LO DE DENTRO DE LOS GRUPOS-->

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
            <button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Cerrar</button>
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
      function mostrar_enviarmensaje(groupdestination){
        $('#groupdestination').val(groupdestination);
        $('#sendmessagegrupalModal').modal();
      }
    </script>

    <!-- Modal  ENVIAR MENSAJE-->
    <div id="sendmessagegrupalModal" class="modal fade myModalmessage" role="dialog">
      <div class="modal-dialog">

        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title message-title">REDACTAR MENSAJE </h4>
          </div>
          <div class="modal-body">
            <form action="" method="POST" id="formsendmessagegroup">
              <div class="panel-body form-group form">
                  <input type="text" class="form-control" placeholder="Título" name="titulo" value="<?php if(!empty($_POST['titulo']) && !$messagesent) {
                    echo $_POST['titulo'];
                  } ?>">
                  <br>
                  <input type="hidden" class="form-control" placeholder="Título" name="complete" value="completeform">
                  <input type="hidden" class="form-control" placeholder="Título" name="nombregrupo" id="groupdestination" value="<?php echo $grupo['title']; ?>">
                  <textarea class="form-control" rows="5" name="mensaje" placeholder="Mensaje"><?php if(!empty($_POST['mensaje']) && !$messagesent) {
                    echo $_POST['mensaje'];
                  } ?></textarea>
              </div>
            </form>
          </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-default error-button-close" form="formsendmessagegroup">ENVIAR</button>
        </div>
      </div>
    </div>
  </div>
  <!--END  Modal content-->



    </div>

    <!--  MENSAJES GRUPALES -->
    <?php
    if($notcreated){
      mostrar_errornotcreated();
    }
    elseif($groupcreated){
      mostrar_groupcreated();
    }
    elseif($repeatedtitle){
      mostrar_repeatedtitle();
    }
    elseif($notcomplete){
      mostrar_errornotcomplete();
    }
    elseif($messagesent){
      mostrar_errormessagesent();
    }
    elseif($notsent){
      mostrar_errornotsent();
    }
    ?>

  </body>
</html>