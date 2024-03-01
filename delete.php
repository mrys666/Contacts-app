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

// Guardar el nombre de la foto antes de eliminar el contacto
$photoToDelete = $contact['photo'];

// Eliminar el contacto
$deleteStatement = $conn->prepare("DELETE FROM contacts WHERE id = :id");
$deleteStatement->execute([":id" => $id]);

// Verificar si se eliminó correctamente el contacto
if ($deleteStatement->rowCount() > 0) {
  // Eliminar la foto del contacto si existe
  if (!empty($photoToDelete) && file_exists($photoToDelete)) {
    unlink($photoToDelete);
  }

  $_SESSION["flash"] = ["message" => "Contact {$contact['name']} deleted."];
} else {
  $_SESSION["flash"] = ["error" => "Failed to delete contact {$contact['name']}."];
}

header("Location: home.php");
?>
