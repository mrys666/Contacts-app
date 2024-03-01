<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <!-- Bootstrap -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/darkly/bootstrap.min.css"
        integrity="sha512-ZdxIsDOtKj2Xmr/av3D/uo1g15yxNFjkhrcfLooZV5fW0TT7aF7Z3wY1LOA16h0VgFLwteg14lWqlYUQK3to/w=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <script
        defer
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"
    ></script>
    <title>Crud de Imagenes</title>
</head>
<body>
    <h1 class="text-center text-secondary font-weight-bold p-3">CRUD DE IMAGENES EN PHP Y MYSQL</h1>

    <?php
        require "database.php";
        require "register_photo.php";
        require "edit_photo.php";
        require "delete_photo.php";
        $imagenes = $conn->query("SELECT*FROM photos");
    ?>

    <script>
        function eliminar() {
            let res = confirm("¿Esta seguro de eliminar esta imagen?");
            return res;
        }
    </script>

    <div class="p-3 table-responsive">

         <!-- Button trigger modal -->
         <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Registrar
        </button>

        <!-- Modal Agregar-->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">ADD PHOTO</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" enctype="multipart/form-data" method="POST">
                    <input type="file" class="form-control mb-3" name="imagen" id="imagen">
                    <input type="submit" value="Registrar" name="btnRegistrar" class="form-control btn btn-success">
                </form>
            </div>
            
            </div>
        </div>
        </div>
        
        <table class="table table-hover table-striped">
            <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">PHOTO</th>
                    <th scope="col">CONTROLLER</th>
                </tr>
            </thead>
            <tbody>

            <?php foreach ($imagenes as $date): ?>
                    <tr>
                        <th scope="row"><?php echo $date["id"] ?></th>
                        <td>
                            <img width="80" src="<?php echo $date["photo"] ?>" alt="">
                        </td>
                        <td>
                            <a data-bs-toggle="modal" data-bs-target="#editarModal<?= $date["id"] ?>" class="btn btn-warning">Editar</a>
                            <a href="imagenes.php?id=<?= $date["id"] ?>&nombre=<?= $date["photo"] ?>" class="btn btn-danger" onclick="return eliminar()">Eliminar</a>
                        </td>
                    </tr>
        <!-- Modal -->
        <div class="modal fade" id="editarModal<?= $date["id"] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">EDIT PHOTO</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" enctype="multipart/form-data" method="POST">
                    <input type="hidden" value="<?php echo $date["id"] ?>" name="id">
                    <input type="hidden" value="<?php echo $date["photo"] ?>" name="path">
                    <input type="file" class="form-control mb-3" name="imagen" id="imagen">
                    <?php
                    if (condition) {
                        # code...
                    } else {
                        # code...
                    }
                    
                    ?>
                    <input type="submit" value="Modificar" name="btnEditar" class="form-control btn btn-success" >
                </form>
            </div>
            
            </div>
        </div>
        </div>

                    <?php endforeach ?>
            </tbody>
        </table>
    </div>
    </body>
</html>
