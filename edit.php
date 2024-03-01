
<style>
  .foto {
    text-align: center;
    margin-bottom: -1rem;
  }

  .foto img {
    width: 160px;
    height: 160px;
    border-radius: 50%;
  }

  .foto figcaption {
  font-size: 1rem;
  color: #4997F4;
  }

  .foto input[type="file"]{
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

$id = $_GET["id"];

$statement = $conn->prepare("SELECT * FROM contacts WHERE id = :id LIMIT 1");
$statement->execute([":id" => $id]);

if ($statement->rowCount() == 0) {
  http_response_code(404);
  echo("HTTP 404 NOT FOUND");
  return;
}

$contact = $statement->fetch(PDO::FETCH_ASSOC);

if ($contact["user_id"] !== $_SESSION["user"]["id"]) {
  http_response_code(403);
  echo("HTTP 403 UNAUTHORIZED");
  return;
}

$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST["name"];
  $phoneNumber = $_POST["phone_number"];

  if (empty($_POST["name"]) || empty($_POST["phone_number"]) || empty($imagen)) {
    $error = "Please fill all the fields.";
  } else if (strlen($_POST["phone_number"]) < 9) {
    $error = "Phone number must be at least 9 characters.";
  }else
  // Verificar si se ha subido una nueva imagen
  if (!empty($_FILES["imagen"]["name"])) {
    $img = $_FILES["imagen"]["tmp_name"];
    $nameImagen = $_FILES["imagen"]["name"];
    $tipoImagen = strtolower(pathinfo($nameImagen, PATHINFO_EXTENSION));
    $directorio = "photos/";

    if ($tipoImagen == "jpg" || $tipoImagen == "jpeg" || $tipoImagen == "png") {
      // Eliminamos la imagen anterior
      try {
        unlink($contact["photo"]);
      } catch (\Throwable $th) {
        //throw $th;
      }

      $ruta = $directorio . $id . "." . $tipoImagen;

      if (move_uploaded_file($img, $ruta)) {
        // Actualizar la información del contacto con la nueva foto
        $statement = $conn->prepare("UPDATE contacts SET name = :name, phone_number = :phone_number, photo = :photo WHERE id = :id");
        $statement->execute([
          ":id" => $id,
          ":name" => $name,
          ":phone_number" => $phoneNumber,
          ":photo" => $ruta,
        ]);
      } else {
        $error = "Error al subir la imagen al servidor.";
      }
    } else {
      $error = "Formato inválido.";
    }
  } else {
    // No se ha subido una nueva imagen, actualizar solo la información del contacto
    $statement = $conn->prepare("UPDATE contacts SET name = :name, phone_number = :phone_number WHERE id = :id");
    $statement->execute([
      ":id" => $id,
      ":name" => $name,
      ":phone_number" => $phoneNumber,
    ]);
  }

  // Redireccionar a la página de inicio después de la actualización
  $_SESSION["flash"] = ["message" => "Contact $name updated."];
  header("Location: home.php");
  return;
}
?>


<?php require "partials/header.php" ?>

<div class="container pt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Edit Contact</div>
          <div class="card-body">
            <?php if ($error): ?>
              <p class="text-danger">
                <?= $error ?>
              </p>
            <?php endif ?>
            <form method="POST" action="edit.php?id=<?= $contact['id'] ?>" enctype="multipart/form-data">

            <div class="mb-3 row">
                <!-- <label for="imagen" class="col-md-4 col-form-label text-md-end">Photo</label> -->

                <div class="foto col-md-6">
                  <label for="imagen">
                    <img src="<?= $contact['photo']?>" alt="Foto de perfil">
                    <figcaption>Cambiar</figcaption>
                  </label>
                  <input id="imagen" type="file" class="form-control" name="imagen" autocomplete="imagen" autofocus>
                </div>
            </div>

              <div class="mb-3 row">
                <label for="name" class="col-md-4 col-form-label text-md-end">Name</label>

                <div class="col-md-6">
                  <input value="<?= $contact['name'] ?>" id="name" type="text" class="form-control" name="name" autocomplete="name" autofocus>
                </div>
              </div>

              <div class="mb-3 row">
                <label for="phone_number" class="col-md-4 col-form-label text-md-end">Phone Number</label>

                <div class="col-md-6">
                  <input value="<?= $contact['phone_number'] ?>" id="phone_number" type="tel" class="form-control" name="phone_number" autocomplete="phone_number" autofocus>
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
