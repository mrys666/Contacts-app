<?php

  if (!empty($_GET["id"]) and !empty($_GET["nombre"])) {
    $id = $_GET["id"];
    $nombre = $_GET["nombre"];

    try {
      unlink($nombre);
    } catch (\Throwable $th) {
      //throw $th;
    }

    $delete = $conn->query("DELETE FROM photos WHERE id = $id");

    if ($delete !== false) {
      echo "<div class='alert alert-success'>Imagen eliminada.</div>";

    } else {
      echo "<div class='alert alert-danger'>Error al eliminar la imagen.</div>";
    }
    
  }?>

  <script>
    history.replaceState(null,null,location.pathname);
  </script>

  <?php

?>
