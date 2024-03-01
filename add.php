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

<div class="container pt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Add New Contact</div>
        <div class="card-body">
          <?php if ($error): ?>
            <p class="text-danger">
              <?= $error ?>
            </p>
          <?php endif ?>
          <form enctype="multipart/form-data" method="POST" action="add.php">
            <div class="mb-3 row">
              <label for="name" class="col-md-4 col-form-label text-md-end">Name</label>

              <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name" autocomplete="name" autofocus>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="phone_number" class="col-md-4 col-form-label text-md-end">Phone Number</label>

              <div class="col-md-6">
                <input id="phone_number" type="tel" class="form-control" name="phone_number" autocomplete="phone_number" autofocus>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="imagen" class="col-md-4 col-form-label text-md-end">Photo</label>

              <div class="col-md-6">
                <input id="imagen" type="file" class="form-control" name="imagen" autocomplete="imagen" autofocus>
              </div>
            </div>

            <div class="mb-3 row">
              <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require "partials/footer.php" ?>
