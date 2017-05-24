<?php
require('init.php');

/* ROL DEL USUARIO */

$sql="SELECT rol FROM usuarios WHERE id='$user'";
$consulta = mysqli_query($db, $sql);
$row=mysqli_fetch_assoc($consulta);
$rol = $row['rol'];

/* VARIABLES DE CONTROL DE ERRORES/FINALIZACIÓN */

$notsent = false;
$messagesent = false;
$notcomplete = false;

/* ENVIAR MENSAJE GLOBAL */

if (!empty($_POST['complete']) && $_POST['complete']=='completeform') {
    if (!empty($_POST['titulo']) && !empty($_POST['mensaje'])) {
        $titulo = $_POST['titulo'];
        $mensaje = $_POST['mensaje'];

        $sql = "INSERT INTO messages(idorigin, iddestination, title, message, leido) VALUES ('$user',NULL,'$titulo', '$mensaje', 0)";
        $consulta = mysqli_query($db, $sql);
        if (!$consulta) {
            $notsent= true;
        } else {
            $messagesent = true;
        }
    } else {
        $notcomplete = true;
    }
}

/* HACER LISTA DE MENSAJES GLOBALES */

$sql = "SELECT * FROM `messages` WHERE iddestination IS NULL ORDER BY id DESC";
$consulta = mysqli_query($db, $sql);

while ($row = mysqli_fetch_assoc($consulta)) {
    $mensajesrecibidos[] = $row;
}

/* FUNCIONES */

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
    echo '<div class="modal fade myModal" role="dialog" id="modalerrormessagesent">
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
  			$("#modalerrormessagesent").modal();
  		});
  	</script>
  </div>';
}

function mostrar_errornotcomplete() {
    echo '<div class="modal fade myModal" role="dialog" id="modalerrornotcomplete">
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
  			$("#modalerrornotcomplete").modal();
  		});
  	</script>
  </div>';
}
?>

<!DOCTYPE html>
<html lang="es">
	<head>
    <?php require('head.html'); ?>
	</head>

	<body id="mainbody">
    <!-- BARRA SUPERIOR -->
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
          <li><a href="main" class=" navbar-element navbar-main-title barraBasica">Home</a></li>
          <li><a href="mpersonal" class=" navbar-element navbar-main-title barraBasica">Mensajes Personales</a></li>
          <li><a href="mgrupal" class=" navbar-element navbar-main-title barraBasica">Mensajes Grupales</a></li>
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
      <!-- TABLA MENSAJES GLOBALES -->
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
            while (isset($mensajesrecibidos[$i])) {
              $messageText = str_replace("\r\n", "<br>", $mensajesrecibidos[$i]['message']);
              $messageText = str_replace("\n", "<br>", $messageText);
                echo "<tr onclick='mostrar_mensaje($i, \"{$mensajesrecibidos[$i]['title']}\", \"{$mensajesrecibidos[$i]['idorigin']}\", \"{$messageText}\")' class='pointercursor'>
                  <td>{$mensajesrecibidos[$i]['idorigin']}</td>
                  <td>{$mensajesrecibidos[$i]['title']}</td>
                </tr>";

                ++$i;
            }?>
        </tbody>
      </table>

      <!-- BOTÓN ENVIAR MENSAJE -->
      <button type="button" class="btn btn-primary btn-lg button-send-message" onclick='mostrar_enviarmensaje()'>Enviar mensaje</button>

      <!-- Modal MOSTRAR MENSAJE-->
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
      <!-- FINAL MODAL MOSTRAR MENSAJE-->

      <!-- JAVASCRIPT -->
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
          				<input type="text" class="form-control" placeholder="Título" name="titulo" value="<?php if (!empty($_POST['titulo']) && !$messagesent) {
                echo $_POST['titulo'];
            } ?>">
          				<br>
                  <input type="hidden" class="form-control" placeholder="Título" name="complete" value="completeform">
                  <textarea class="form-control" rows="5" name="mensaje" placeholder="Mensaje"><?php if (!empty($_POST['mensaje']) && !$messagesent) {
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
      <!-- FINAL MODAL ENVIAR MENSAJE-->
    </div>

    <!--  MENSAJES DE CONTROL DE ERRORES/FINALIZACIÓN -->
    <?php
      if ($notsent) {
          mostrar_errornotsent();
      } elseif ($messagesent) {
          mostrar_errormessagesent();
      } elseif ($notcomplete) {
          mostrar_errornotcomplete();
      }
    ?>
  </body>
</html>
