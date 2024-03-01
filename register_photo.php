<?php
  if (!empty($_POST["btnRegistrar"])) {
    //echo "<div class='alert alert-info'>Boton Presionado</div>";
    $imagen = $_FILES["imagen"]["tmp_name"];
    $nombreImagen = $_FILES["imagen"]["name"];
    $tipoImagen = strtolower(pathinfo($nombreImagen, PATHINFO_EXTENSION));
    $sizeImagen = $_FILES["imagen"]["size"];
    $directorio = "archivos/";
    //echo "<div class='alert alert-info'>$sizeImagen</div>";

    if ($tipoImagen == "jpg" or $tipoImagen == "jpeg" or $tipoImagen == "png") {
      $registro = $conn->query("INSERT INTO photos(photo) VALUES('')");
      $idRegistro = $conn->lastInsertId();
      $path = $directorio.$idRegistro.".".$tipoImagen;
      //UPDATE photos SET photo='path' WHERE id=3;
      $actualizarImagen = $conn->query("UPDATE photos SET photo='$path' WHERE id=$idRegistro");
      //echo "<div class='alert alert-info'>$idRegistro</div>";

      //ALMACENADO LA IMG
      if (move_uploaded_file($imagen, $path)) {
        echo "<div class='alert alert-info'>Imagen guardada</div>";
      } else{
        echo "<div class='alert alert-info'>Error al guardar la imagen</div>";
      }

    } else {
      echo "<div class='alert alert-info'>Formato invalido</div>";
    }?>

    <script>
      history.replaceState(null, null, location.pathname);
    </script>

    <?php
    
  }
?>
