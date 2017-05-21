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

$sql = "SELECT * FROM `groups`";
$consulta = mysqli_query($db, $sql);
$grupos=[];
$grouptitles =[];

while ($row = mysqli_fetch_assoc($consulta)) {
  $grupos[] = $row;
  $grouptitles[]=$row['title'];
}

$notcreated = false;
$groupcreated = false;
$notcomplete = false;
$repeatedtitle=false;

if(!empty($_POST['complete']) && $_POST['complete']=='completeform'){
  if (!empty($_POST['title']) && !empty($_POST['type']) && !empty($_POST['minage']) && !empty($_POST['maxage'])) {
      $titulo = $_POST['title'];
      $tipo = $_POST['type'];
      $minage = $_POST['minage'];
      $maxage = $_POST['maxage'];

      if(in_array($titulo, $grouptitles)){
        $repeatedtitle=true;
      }
      else{
        $sql = "INSERT INTO groups(title, type, minage, maxage) VALUES ('$titulo','$tipo','$minage','$maxage')";
        $consulta = mysqli_query($db, $sql);
        if(!$consulta){
          $notcreated= true;
        }
        else {
          $groupcreated = true;

          $sql = "SELECT users.id FROM users, musictypes WHERE age >='$minage' AND age<='$maxage' AND musictypes.id=users.id AND musictypes.type='$tipo'";
          $consulta = mysqli_query($db, $sql);
          $usuariosvalidos=[];
          while ($row = mysqli_fetch_assoc($consulta)) {
            $sql="INSERT INTO groupsrelation(grouptitle, iduser) VALUES ('$titulo','{$row['id']}')";
            $consulta2 = mysqli_query($db, $sql);
          }

          $sql = "SELECT * FROM `groups`";
          $consulta = mysqli_query($db, $sql);
          $grupos=[];
          $grouptitles =[];
          while ($row = mysqli_fetch_assoc($consulta)) {
            $grupos[] = $row;
            $grouptitles[]=$row['title'];
          }
        }
      }
  }
  else{
    $notcomplete = true;
  }
}

if(!empty($_POST['eliminar']) && $_POST['eliminar']=='eliminargrupo' && !empty($_POST['grupoparaeliminar'])){
  $title = $_POST['grupoparaeliminar'];

  $sql = "DELETE FROM `groups` WHERE title='$title'";
  $consulta = mysqli_query($db, $sql);

  $sql = "SELECT * FROM `groups`";
  $consulta = mysqli_query($db, $sql);
  $grupos=[];
  $grouptitles =[];
  while ($row = mysqli_fetch_assoc($consulta)) {
    $grupos[] = $row;
    $grouptitles[]=$row['title'];
  }
}

$sql = "SELECT DISTINCT type FROM `musictypes`";
$consulta = mysqli_query($db, $sql);
$musictypes=[];

while ($row = mysqli_fetch_assoc($consulta)) {
  $musictypes[] = $row['type'];
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

function mostrar_groupcreated() {
	echo '<div class="modal fade myModal" role="dialog" id="modalgroupcreated">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="text-success">El grupo se ha creado correctamente.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default error-button-close" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
	<script>
		$( document ).ready(function() {
			$("#modalgroupcreated").modal();
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
          <li><a href="mgrupal"  class=" navbar-element navbar-main-title barraBasica">Mensajes Grupales</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="login.php?logout" title="Log out"><i class="icono-power"></i></a></li>
      </ul>
    </div>


    <!-- MUNDO IZQUIERDO -> MENSAJES PERSONALES -->
    <div  class="main-panel">
      <table class="table table-hover table-condensed table-responsive personaltable">
        <thead>
            <tr>
              <th class="personaltitles">Grupo</th>
              <th class="personaltitles">Estilo</th>
              <th class="personaltitles">Edad</th>
              <th class="personaltitles">Eliminar</th>
            </tr>
        </thead>
        <tbody>
          <?php
            $i=0;
            while(isset($grupos[$i])){
              echo "<tr>
                  <td>{$grupos[$i]['title']}</td>
                  <td>{$grupos[$i]['type']}</td>
                  <td>{$grupos[$i]['minage']} - {$grupos[$i]['maxage']}</td>
                  <td  class='pointercursor' onclick='mostrar_eliminargrupo(\"{$grupos[$i]['title']}\")'><i class='icono-trash pointercursor'></i></td>
                </tr>";
              ++$i;
            }?>
        </tbody>
      </table>

      <div>
        <button type="button" class="btn btn-primary btn-lg button-send-message groupsbutton" onclick='mostrar_añadirgrupo()'>Crear grupo</button>
      </div>
    </div>

        <script>
          function mostrar_eliminargrupo(grupoaeliminar){
            $('#erasegroup').val(grupoaeliminar);
            $('#grupoaeliminar').html(grupoaeliminar);
            $('#erasegroupmodal').modal();
          }
          function mostrar_añadirgrupo(){
            $('#addgroupmodal').modal();
          }
        </script>

        <!-- Modal  ELIMINAR GRUPO-->
        <div id="erasegroupmodal" class="modal fade myModalmessage" role="dialog">
        <div class="modal-dialog">

          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title message-title">ELIMINAR GRUPO </h4>
            </div>
            <div class="modal-body">
              <form action="" method="POST" id="formerasegroup">
                <div class="panel-body form-group form">
                    ¿Estás seguro de querer eliminar <span id="grupoaeliminar" class="text-danger musictype"></span> de la lista de grupos?
                    <input type="hidden" class="form-control" name="eliminar" value="eliminargrupo">
                    <input type="hidden" class="form-control" name="grupoparaeliminar" value="" id="erasegroup">
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-default error-button-close" form="formerasegroup">Eliminar</button>
            </div>
          </div>
        </div>
      </div>
      <!--END  Modal content-->

      <!-- Modal  AÑADIR GROUP-->
      <div id="addgroupmodal" class="modal fade myModalmessage" role="dialog">
        <div class="modal-dialog">

          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title message-title">AÑADIR GRUPO </h4>
            </div>
            <div class="modal-body">
              <form action="" method="POST" id="formaddgroup">
            		<div class="panel-body form-group form">
            				<input type="text" class="form-control" placeholder="Título" name="title" value="<?php if(!empty($_POST['title']) && (!$groupcreated || $repeatedtitle)) {
                      echo $_POST['title'];
                    } ?>">
            				<br>
                    <select type="text"  class="form-control select-admin" name="type">
                      <?php
                        foreach($musictypes as $i){
                          echo '<option value="'.$i.'">'.$i.'</option>';
                        }
                      ?>
                    </select>
            				<br>
                    <input type="number" min="1" max="100" class="form-control" placeholder="Edad mínima" name="minage" value="<?php if(!empty($_POST['minage']) && !$groupcreated) {
                      echo $_POST['minage'];
                    } ?>">
            				<br>
                    <input type="number" min="1" max="100" class="form-control" placeholder="Edad máxima" name="maxage" value="<?php if(!empty($_POST['maxage']) && !$groupcreated) {
                      echo $_POST['maxage'];
                    } ?>">
            				<br>
                    <input type="hidden" class="form-control" placeholder="Título" name="complete" value="completeform">

            		</div>
            	</form>
            </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-default error-button-close" form="formaddgroup">CREAR</button>
          </div>
        </div>
      </div>
    </div>
    <!--END  Modal content-->


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
    ?>

  </body>
</html>
