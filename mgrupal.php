<?php
require('init.php');

/* MOSTRAR LOS GRUPOS */

$sql = "SELECT grupos.title 'group_title', grupos.type 'group_type', grupos.minage, grupos.maxage, groupmessages.idorigin, groupmessages.destinationgroup, groupmessages.title, groupmessages.message  FROM grupos, relacion_usuario_grupo, groupmessages WHERE destinationgroup=relacion_usuario_grupo.grouptitle AND relacion_usuario_grupo.iduser='$user' AND grupos.title=relacion_usuario_grupo.grouptitle ORDER BY groupmessages.id DESC";
$consulta = mysqli_query($db, $sql);
$grupos=[];
$mensajes =[];

while ($row = mysqli_fetch_assoc($consulta)) {
  $group = [
    'title' => $row['group_title'],
    'type' => $row['group_type'],
    'minage' => $row['minage'],
    'maxage' => $row['maxage'],
  ];
  $mensaje = [
    'idorigin' => $row['idorigin'],
    'destinationgroup' => $row['destinationgroup'],
    'title' => $row['title'],
    'message' => $row['message'],
  ];

  //Agregar los grupos

  if(!in_array($group,$grupos )){
    $grupos[] = $group;
  }

  // Almacenar los mensajes de cada grupo

  if(!isset($mensajes[$row['group_title']])){
    $mensajes[$row['group_title']] = [];
  }
  $mensajes[$row['group_title']][] = $mensaje;
}

//Agregar los grupos que no tienen mensajes

$sql = "SELECT title, type, minage, maxage FROM grupos, relacion_usuario_grupo WHERE iduser='$user' AND grupos.title=relacion_usuario_grupo.grouptitle";
$consulta2 = mysqli_query($db, $sql);

while ($row2 = mysqli_fetch_assoc($consulta2)) {
  $group2 = [
    'title' => $row2['title'],
    'type' => $row2['type'],
    'minage' => $row2['minage'],
    'maxage' => $row2['maxage'],
  ];

  if(!in_array($row2, $grupos)){
    $grupos[] = $group2;
  }
}

/* VARIABLES DE CONTROL DE ERRORES/FINALIZACIÓN */

$messagesent = false;
$notsent = false;
$notcomplete = false;

/* ROL DEL USUARIO */

$sql="SELECT rol FROM usuarios WHERE id='$user'";
$consulta = mysqli_query($db, $sql);
$row=mysqli_fetch_assoc($consulta);
$rol = $row['rol'];

/* ENVIAR MENSAJE GRUPAL */

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

function mostrar_messagesent() {
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
	echo '<div class="modal fade myModal" role="dialog" id="modalmessagenotcomplete">
  	<div class="modal-dialog">
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
  			$("#modalmessagenotcomplete").modal();
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
        <li><a href="main"  class=" navbar-element navbar-main-title barraBasica">Home</a></li>
        <li><a href="mpersonal"  class=" navbar-element navbar-main-title barraBasica">Mensajes Personales</a></li>
        <li><a href="mglobal"  class=" navbar-element navbar-main-title barraBasica">Mensajes Globales</a></li>
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
      <!-- BARRA GRUPOS -->
      <ul class="nav nav-tabs nav-justified">
        <?php
        if(empty($grupos)){
          echo "<li><a data-toggle='tab' href='#nogroups'> </a></li>";
        } else {
          foreach ($grupos as $i=>$grupo){
            echo  "<li".($i==0 ? ' class=\'active\'':'')."><a data-toggle='tab' href='#tablamensajes-$i'>{$grupo['title']} - {$grupo['type']}</a></li>";
          }
        }
        ?>
      </ul>
      <!-- FINAL BARRA GRUPOS -->

      <!-- INTERIOR DE LOS GRUPOS-->
      <div class="tab-content">
        <?php
        if(empty($grupos)){
          echo "<div id='nogroups' class='tab-pane fade in active'>
          <p class='panel-title grouptext'>No coincides con las características de ningún grupo.<br>Prueba a añadir un estilo de música para poder compartir mensajes con otros usuarios que compartan tus gustos.</p></div>";
        }
        else {
          foreach ($grupos as $i=>$grupo){
            echo "<div id='tablamensajes-$i' class='tab-pane fade ".($i==0 ? 'in active':'')."'>
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
                $messageText = str_replace("\r\n", "<br>", $mensajes[$grupo['title']][$i]['message']);
                $messageText = str_replace("\n", "<br>", $messageText);
                echo "<tr onclick='mostrar_mensaje($i, \"{$mensajes[$grupo['title']][$i]['title']}\", \"{$mensajes[$grupo['title']][$i]['idorigin']}\", \"{$messageText}\")' class='pointercursor'>
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
        }
        ?>
      </div>
      <!-- FINAL INTERIOR DE LOS GRUPOS-->

      <!-- MODAL MENSAJE GRUPAL-->
      <div id="messageModalgrupal" class="modal fade myModal" role="dialog">
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
      <!-- FINAL MODAL MENSAJE GRUPAL-->

      <!-- JAVASCRIPT-->
      <script>
        function mostrar_mensaje(i, messagetitle, messageauthor, messagebody) {
          $('#messagetitle').html(messagetitle);
          $('#messageauthor').html(messageauthor);
          $('#messagebody').html(messagebody);
          $('#messageModalgrupal').modal();
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
    <!-- FINAL Modal  ENVIAR MENSAJE-->
    </div>

    <!--  MENSAJES DE CONTROL DE ERRORES/FINALIZACIÓN -->
    <?php
      if($notcomplete){
        mostrar_errornotcomplete();
      }
      elseif($messagesent){
        mostrar_messagesent();
      }
      elseif($notsent){
        mostrar_errornotsent();
      }
    ?>
  </body>
</html>
