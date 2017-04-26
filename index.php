<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = mysqli_connect('localhost', 'musicofthespheres', 'musicofthespheres', 'musicofthespheres');
if(!$db){
  die('Error al conectarse con la base de datos');
}

$user=false;

if(!empty($_COOKIE['user'])){
  $user = $_COOKIE['user'];
}
else{
  if(!empty($_POST['name']) && !empty($_POST['password'])){
    $name = $_POST['name'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM usuarios WHERE id='$name'";
    $consulta = mysqli_query($db, $sql);
    $fila = mysqli_fetch_row($consulta);
    if($fila){
      $user = $name;
      setcookie('user', $user);
    }
  }
}
function mostrar_formulario(){
  echo '
  <form method="POST">
      <div class="container-fluid">
  <!--Id - text -->
   <div class="form-group">
     <label for="name">Usuario:</label>
     <input type="text" name="name" class="form-control" id="name" required>
   </div>
   <!--Password - text -->
    <div class="form-group">
      <label for="password">Contraseña:</label>
      <input type="password" name="password" class="form-control" id="password" required>
    </div>
    <button type="submit" class="btn btn-default">Log in</button><br><br>
    </div>
    </form>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>BIBLIOTECA-Log in</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
  <?php
  require 'navbar.php';
  echo '<br><br><br>';
  if(!$user){
      mostrar_formulario();
      if(!empty($_POST['name']) && !empty($_POST['password'])){
          echo '<br><div class="container-fluid"><p class="text-danger">    Lo sentimos, usuario o contraseña incorrecta. Por favor, prueba de nuevo.</p></div>';
      }
  }
  else{

      echo '<div class="container-fluid"> <h2 class="text-center" style="color:powderblue;"><strong>Bienvenido, '.ucfirst($name).'!</strong></h2>
        Estos son los libros que tienes prestados:<br><br>';
        echo '<table style="max-width:1000px" class="table table-striped table-hover table-condensed table-responsive">
          <thead>
              <tr>
                <th>Título</th>
                <th>Autor</th>
                <th>Desde</th>
              </tr>
          </thead>
          <tbody>';
          $sql = "SELECT DISTINCT libros.titulo, libros.autor, prestamos.fechainicio FROM libros, prestamos, usuarios WHERE prestamos.titulo = libros.titulo AND prestamos.idusuario = '$name' ";
          $consulta = mysqli_query($db, $sql);
          while($fila = mysqli_fetch_row($consulta)){
            echo '  <tr>
                <td>'.$fila[0].'</td>
                <td>'.$fila[1].'</td>
                <td>'.$fila[2].'</td>
              </tr>';
            }
        echo '  </tbody>
        </table></div>';
      }
    }
  }
  ?>
 </body>
</html>
