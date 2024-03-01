<?php
if (!empty($_POST["btnEditar"])) {
    //echo "<div class='alert alert-info'>boton presionado</div>";
    $id = $_POST["id"];
    $nombre = $_POST["path"];

    //Datos de la imagen
    $img = $_FILES["imagen"]["tmp_name"];
    $nameImagen = $_FILES["imagen"]["name"];
    $tipoImagen = strtolower(pathinfo($nameImagen, PATHINFO_EXTENSION));
    $directorio = "archivos/";

    if (!empty($img)) {
        if ($tipoImagen == "jpg" || $tipoImagen == "jpeg" || $tipoImagen == "png") {
            // Eliminamos la imagen anterior
            try {
                unlink($nombre);
            } catch (\Throwable $th) {
                //throw $th;
            }

            $ruta = $directorio . $id . "." . $tipoImagen;

            if (move_uploaded_file($img, $ruta)) {
                $editar = $conn->query("UPDATE photos SET photo='$ruta' WHERE id = $id");
                if ($editar !== false) {
                    echo "<div class='alert alert-success'>Imagen editada correctamente.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error al editar la imagen.</div>";
                }
            } else {
                echo "<div class='alert alert-info'>Error al subir la imagen al servidor.</div>";
            }
        } else {
            echo "<div class='alert alert-info'>Formato de imagen inválido.</div>";
        }
    } else {
        echo "<div class='alert alert-info'>Seleccione una imagen.</div>";
    }
}
?>
<script>
    history.replaceState(null, null, location.pathname);
</script>
