<?php
require('init.php');

/* ADMINISTRAR USUARIO */

$noleidospersonales = 0;

$sql = "SELECT * FROM `usuarios` WHERE id='$user'";
$consulta = mysqli_query($db, $sql);
$usuario = mysqli_fetch_assoc($consulta);

/* ELIMINAR TIPO DE MÚSICA */

if (!empty($_POST['eliminar']) && $_POST['eliminar']=='eliminartipo' && !empty($_POST['tipoparaeliminar'])) {
    $type = $_POST['tipoparaeliminar'];

  // Eliminamos la relación entre el estilo de música y el usuario
  $sql = "DELETE FROM `musictypes` WHERE id='$user' AND type='$type'";
  $consulta = mysqli_query($db, $sql);

  // Eliminamos la relación entre los grupos del estilo de música y el usuario

  $sql = "SELECT * FROM `grupos` WHERE type='$type'";
  $consulta = mysqli_query($db, $sql);
  while ($row = mysqli_fetch_assoc($consulta)) {
      $sql = "DELETE FROM `relacion_usuario_grupo` WHERE iduser='$user' AND grouptitle='{$row['title']}'";
      $consulta2 = mysqli_query($db, $sql);
  }
}

/* AÑADIR TIPO DE MÚSICA */

if (!empty($_POST['añadir']) && $_POST['añadir']=='añadirtipo' && !empty($_POST['tipoparaañadir'])) {
    $type = $_POST['tipoparaañadir'];

  // Añadimos la relación entre el estilo de música y el usuario
  $sql = "INSERT INTO `musictypes`(`type`, `id`) VALUES ('$type', '$user')";
    $consulta = mysqli_query($db, $sql);

  // Añadimos al usuario a los grupos de música relacionados con el nuevo estilo
  $sql="SELECT grupos.title FROM grupos, usuarios, musictypes WHERE usuarios.id = '$user' AND usuarios.age>=minage AND usuarios.age<=maxage AND musictypes.id=usuarios.id AND musictypes.type='$type' AND grupos.type='$type'";
  $consulta = mysqli_query($db, $sql);
  while ($row = mysqli_fetch_assoc($consulta)) {
      $sql="INSERT INTO relacion_usuario_grupo(grouptitle, iduser) VALUES ('{$row['title']}', '$user')";
      $consulta2 = mysqli_query($db, $sql);
  }
}

/* SACAMOS LOS MENSAJES PERSONALES DEL USUARIO PARA LAS NOTIFICACIONES */

$sql = "SELECT * FROM `messages` WHERE iddestination='$user'";
$consulta = mysqli_query($db, $sql);

while ($row = mysqli_fetch_assoc($consulta)) {
    if ($row['leido'] == 0) {
        if (($row['iddestination'] == $user)) {
            ++$noleidospersonales;
        }
    }
}

/* ADMINISTRAR LOS ESTILOS DE MÚSICA */

$sql = "SELECT DISTINCT * FROM `musictypes` WHERE type NOT IN (SELECT type FROM `musictypes` WHERE id='$user') OR id='$user'";
$consulta = mysqli_query($db, $sql);
$addedtypes=[];
$nonaddedtypes=[];

while ($row = mysqli_fetch_assoc($consulta)) {
    // Tipos añadidos
  if ($row['id']==$user) {
      if (!in_array($row['type'], $addedtypes)) {
          $addedtypes[] = $row['type'];
      }
  }
  // Tipos no añadidos
  else {
      if (!in_array($row['type'], $nonaddedtypes)) {
          $nonaddedtypes[]=$row['type'];
      }
  }
}

//Por si hay un estilo de música que no ha añadido nadie
$sql = "SELECT type FROM `typesmusic`";
$consulta = mysqli_query($db, $sql);

while ($row = mysqli_fetch_assoc($consulta)) {
    if (!in_array($row['type'], $nonaddedtypes) && !in_array($row['type'], $addedtypes)) {
        $nonaddedtypes[]=$row['type'];
    }
}
?>

<!DOCTYPE html>
<html lang="es" class="particlesbody">
	<head>
    <?php require('head.html'); ?>
	</head>

	<body id="mainbody" class="particlesbody">
    <!-- BARRA SUPERIOR -->
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
          <li><span class="navbar-element navbar-main-title barraBasica defaultcursor">Home</span></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li  class="iconos-nav"><a data-toggle="collapse" href="#collapseperfil" title="Perfil"><i class="icono-user"></i></a></li>
        <li  class="iconos-nav"><a href="login.php?logout" title="Log out"><i class="icono-power"></i></a></li>
      </ul>
    </div>
    <!-- FINAL BARRA SUPERIOR -->

    <!-- PERFIL -->
    <div id="collapseperfil" class="panel-collapse collapse">
      <div class="panel-heading panel-title">PERFIL - <?php echo $user;?></div>
      <!-- PANEL BODY -->
      <div class="panel-body">
      <!-- ACCORDION -->
        <div class="panel-group" id="accordion">
          <!-- PARTE 1 - ESTILOS DE MÚSICA-->
          <div class="panel-default">
            <!-- CABECERA - ESTILOS DE MÚSICA-->
            <div class="panel-heading">
              <h4 class="panel-title  perfil-panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Estilos de música</a>
              </h4>
            </div>
            <!-- FINAL CABECERA - ESTILOS DE MÚSICA-->
            <!-- CUERPO - ESTILOS DE MÚSICA-->
            <div id="collapse1" class="panel-collapse collapse">
              <div class="panel-body">
                <!-- TABLA AÑADIDOS - ESTILOS DE MÚSICA-->
                <table class="table table-hover table-condensed table-responsive">
                  <thead>
                    <tr>
                      <th class="personaltitles" colspan="2">Tipo añadido</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $i=0;
                      if (empty($addedtypes)) {
                          echo "<tr><td class='musictype_row'>No tienes ningún tipo de música preferido. Si quieres puedes añadir uno haciendo click en los estilos de abajo.</td></tr>";
                      }
                      while (isset($addedtypes[$i])) {
                          echo "<tr>
                            <td class='defaultcursor myModalmessage'>{$addedtypes[$i]}</td>
                            <td class='th_responder pointercursor myModalmessage' onclick='mostrar_eliminartipo(\"{$addedtypes[$i]}\")'><i class='icono-trash pointercursor'></i></td></tr>";
                          ++$i;
                      }
                    ?>
                  </tbody>
                </table>
                <!-- TABLA NO AÑADIDOS - ESTILOS DE MÚSICA-->
                <table class="table table-hover table-condensed table-responsive">
                  <thead>
                      <tr>
                        <th class="personaltitles" colspan="2">Tipos para añadir</th>
                      </tr>
                  </thead>
                  <tbody>
                    <?php
                      $i=0;
                      if (empty($nonaddedtypes)) {
                          echo "<tr >
                            <td class='musictype_row'>No tienes ningún tipo de música sin añadir. Eres todo un melómano!</td>
                          </tr>";
                      }
                      while (isset($nonaddedtypes[$i])) {
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
            <!-- FINAL CUERPO - ESTILOS DE MÚSICA-->
          </div>
          <!-- FINAL PARTE 1 - ESTILOS DE MÚSICA -->

          <!-- PART 2 - EDAD -->
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
         <!-- FINAL PARTE 2 - EDAD -->
        </div>
        <!--FINAL ACCORDION -->
      </div>
      <!-- FINAL PANEL BODY -->
    </div>
    <!-- FINAL PERFIL -->

    <!-- JAVASCRIPT -->
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

    <!-- Modal  ELIMINAR TIPO -->
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
    <!-- FINAL Modal  ELIMINAR TIPO -->

    <!-- Modal AÑADIR TIPO -->
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
    <!-- FINAL Modal  AÑADIR TIPO -->

    <!-- MUNDO IZQUIERDO -> MENSAJES PERSONALES -->
    <a href="mpersonal"><div id ="mainpanelleft" class="panel panelplanet main-panel">
      <div class="login-form">
        <h3 class="panel-content"> Mensajes Personales <span class="label label-default" id="label-mercury"><?php echo $noleidospersonales; ?></span></h3>
      </div>
    </div></a>
    <!-- FINAL MUNDO IZQUIERDO -> MENSAJES PERSONALES -->

    <!-- MUNDO CENTRAL -> MENSAJES GLOBALES -->
    <a href="mglobal"><div class="panel panelplanet main-panel" id ="mainpanelcenter">
      <div class="login-form">
        <h3 class="panel-content"> Mensajes Globales </h3>
      </div>
    </div></a>
    <!-- FINAL MUNDO CENTRAL -> MENSAJES GLOBALES -->

    <!-- MUNDO DERECHO -> MENSAJES GRUPALES -->
    <a href="mgrupal"><div class="panel panelplanet main-panel" id ="mainpanelright">
      <div class="login-form">
        <h3 class="panel-content"> Mensajes Grupales</h3>
      </div>
    </div></a>
    <!-- FINAL MUNDO DERECHO -> MENSAJES GRUPALES -->

    <!-- SI EL USUARIO ES ADMINISTRADOR, MUESTRA EL BOTÓN DE ADMINISTRAR GRUPOS -->
    <?php if ($usuario['rol'] == 'admin'):?>
    <div>
      <a href="admin"><button type="button" class="btn btn-primary btn-lg button-send-message groupsbutton">Administrar grupos y estilos</button></a>
    </div>
  <?php endif; ?>
  </body>
</html>
