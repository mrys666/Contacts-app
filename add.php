
<style>
  figcaption {
  font-size: 1rem;
  color: #4997F4;
  }

  input[type="file"]{
    display: none;
  }
</style>

<?php

  require "database.php";

  session_start();

  if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
  }

  $error = null;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $imagen = $_FILES["imagen"]["tmp_name"];
    if (empty($_POST["name"]) || empty($_POST["phone_number"]) || empty($imagen)) {
      $error = "Please fill all the fields.";
    } else if (strlen($_POST["phone_number"]) < 9) {
      $error = "Phone number must be at least 9 characters.";
    } else {
      $nombreImagen = $_FILES["imagen"]["name"];
      $tipoImagen = strtolower(pathinfo($nombreImagen, PATHINFO_EXTENSION));
      $directorio = "photos/";
      $name = $_POST["name"];
      $phoneNumber = $_POST["phone_number"];

      if ($tipoImagen == "jpg" || $tipoImagen == "jpeg" || $tipoImagen == "png") {
        $statement = $conn->prepare("INSERT INTO contacts (user_id, name, phone_number, photo) 
      VALUES ({$_SESSION['user']['id']}, :name, :phone_number, '')");
      $statement->bindParam(":name", $_POST["name"]);
      $statement->bindParam(":phone_number", $_POST["phone_number"]);
      $statement->execute();

       $idRegistro = $conn->lastInsertId();
       $path = $directorio.$idRegistro.".".$tipoImagen;
       $actualizarImagen = $conn->query("UPDATE contacts SET photo = '$path' WHERE id=$idRegistro");

       if (move_uploaded_file($imagen, $path)) {
        $_SESSION["flash"] = ["message" => "Contact {$_POST['name']} added."];
        header("Location: home.php");
        return;
       } else {
        $error = "Error al guardar la imagen";
       }
       
      
      } else {
        $error = "Formato invalido";
      }
      

      
    }
  }
?>

<?php require "partials/header.php" ?>

<div class="container mt-5 d-flex justify-content-center">

  <div class="card" style="max-width: 30rem;">

    <!-- Titulo -->
    <div class="card-header text-center">
      <h5 class="mb-0">Add New Contact</h5>
    </div>

    <!-- Cuerpo -->
    <div class="card-body">

      <?php if ($error): ?>
        <p class="text-danger">
          <?= $error ?>
        </p>
      <?php endif ?>

      <form enctype="multipart/form-data" method="POST" action="add.php">

        <!-- Foto de Perfil   -->
        <div class="text-center mb-4">
          <a href="#" onclick="document.getElementById('imagen').click(); return false;" style="text-decoration: none;">
            <img src="photos/defect.jpg" class="rounded-circle" alt="Foto de perfil" style="width: 150px; height: 150px;">
            <figcaption class="mt-2" >Add</figcaption>
          </a>
          <input id="imagen" type="file" class="form-control" name="imagen" autocomplete="imagen" autofocus>
        </div>

        <!-- Nombre de Contacto -->
        <div class="mb-3 row">
          <label for="name" class="col-md-4 col-form-label text-md-end">Name</label>

          <div class="col-md-6">
            <input id="name" type="text" class="form-control" name="name" autocomplete="name" autofocus>
          </div>
        </div>

        <!-- Número de Contacto -->
        <div class="mb-3 row">
          <label for="phone_number" class="col-md-4 col-form-label text-md-end">Phone Number</label>

          <div class="col-md-6">
            <input id="phone_number" type="tel" class="form-control" name="phone_number" autocomplete="phone_number" autofocus>
          </div>
        </div>

        <!-- Guardar Cambios -->
        <div class="mb-3 row">
          <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </div>

      </form>
    </div>
  </div>
  
</div>

<?php require "partials/footer.php" ?>
