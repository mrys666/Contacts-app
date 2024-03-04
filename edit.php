
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

//Verifica si el usuario está auntenticado
if (!isset($_SESSION["user"])) {
  header("Location: login.php");
  exit;
}

//Obtener el ID del contacto desde la URL
$id = $_GET["id"];

//Obtener la información del contacto desde la base de datos
$statement = $conn->prepare("SELECT * FROM contacts WHERE id = :id LIMIT 1");
$statement->execute([":id" => $id]);

//Verifica si el contacto existe
if ($statement->rowCount() == 0) {
  http_response_code(404);
  echo("HTTP 404 NOT FOUND");
  return;
}

$contact = $statement->fetch(PDO::FETCH_ASSOC);

//Verifica si el usuario tiene permiso para editar el contacto
if ($contact["user_id"] !== $_SESSION["user"]["id"]) {
  http_response_code(403);
  echo("HTTP 403 UNAUTHORIZED");
  return;
}

$error = null;

//Verifica si se envió el formulario
// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Verificar si se llenaron todos los campos
  if (empty($_POST["name"]) || empty($_POST["phone_number"])) {
    $error = "Please fill all the fields.";
  } else {
    // Verificar si el número de teléfono contiene solo números enteros
    if (!ctype_digit($_POST["phone_number"])) {
        $error = "Phone number must be an integer.";
    } else {
        // Verificar si la longitud del número de teléfono es menor que 9
        if(strlen($_POST["phone_number"]) < 9){
            $error = "Phone number must be at least 9 digits long.";
        }
    }
  }

  // Verificar si no hay errores antes de procesar la imagen y actualizar la información del contacto
  if (!$error) {
    // Procesar la imagen solo si se ha subido una nueva
    if (!empty($_FILES["imagen"]["name"])) {
                //Obtener la información del archivo de imagen
                $img = $_FILES["imagen"]["tmp_name"];
                $nameImagen = $_FILES["imagen"]["name"];
                $tipoImagen = strtolower(pathinfo($nameImagen, PATHINFO_EXTENSION));
                $directorio = "photos/";
      
                //Verifica el formato de la imagen
                if ($tipoImagen == "jpg" || $tipoImagen == "jpeg" || $tipoImagen == "png") {
                  //Eliminar la imagen anterior
                  try {
                    unlink($contact["photo"]);
                  } catch (\Throwable $th) {
                    //throw $th;
                  }
      
                  // Mover la nueva imagen al directorio de fotos
                  $ruta = $directorio . $id . "." . $tipoImagen;
                  
                  if (move_uploaded_file($img, $ruta)) {
                    // Actualizar la información del contacto con la nueva foto
                    $statement = $conn->prepare("UPDATE contacts SET name = :name, phone_number = :phone_number, photo = :photo WHERE id = :id");
                    $statement->execute([
                      ":id" => $id,
                      ":name" => $_POST["name"],
                      ":phone_number" => $_POST["phone_number"],
                      ":photo" => $ruta,
                    ]);
                  } else {
                    $error = "Error uploading image to the server.";
                  }
      
                } else {
                  $error = "Invalid image format.";
                }
    }else {
       // Actualizar la información del contacto
    $name = $_POST["name"];
    $phoneNumber = $_POST["phone_number"];
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
    exit; // Terminar el script para evitar que se siga ejecutando
  }
}

?>


<?php require "partials/header.php" ?>

<div class="container mt-5 d-flex justify-content-center">

  <div class="card" style="max-width: 30rem;">

    <div class="card-header text-center">
      <h5 class="mb-0">Edit Contact</h5>
    </div>

    <div class="card-body">

      <?php if ($error): ?>
        <p class="text-danger">
          <?= $error ?>
        </p>
      <?php endif ?>

      <form method="POST" action="edit.php?id=<?= $contact['id'] ?>" enctype="multipart/form-data">

        <!-- Foto de perfil -->
        <div class="text-center mb-4">
          <!-- Usar un enlace para abrir la ventana de selección de archivo -->
          <a href="#" onclick="document.getElementById('imagen').click(); return false;" style="text-decoration: none;">
            <img src="<?= $contact['photo'] ?>" class="rounded-circle" alt="Foto de perfil" style="width: 150px; height: 150px;">
            <figcaption class="mt-2" >Cambiar</figcaption>
          </a>
          <!-- <img src="" class="rounded-circle" alt="Foto de perfil" style="width: 150px; height: 150px;">
          <figcaption class="mt-2">Cambiar</figcaption> -->
          <input id="imagen" type="file" class="form-control" name="imagen" autocomplete="imagen" autofocus>
        </div>

        <!-- Nombre del Contacto -->
        <div class="mb-3 row">
          <label for="name" class="col-md-4 col-form-label text-md-end">Name</label>
            <div class="col-md-6">
              <input value="<?= $contact['name'] ?>" id="name" type="text" class="form-control" name="name" autocomplete="name" autofocus>
            </div>
        </div>

        <!-- Número del contacto -->
        <div class="mb-3 row">
          <label for="phone_number" class="col-md-4 col-form-label text-md-end">Phone Number</label>
            <div class="col-md-6">
              <input value="<?= $contact['phone_number'] ?>" id="phone_number" type="tel" class="form-control" name="phone_number" autocomplete="phone_number" autofocus>
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
