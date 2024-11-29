<<<<<<< HEAD
<?php
include 'conexion.php';

// Obtener los datos del formulario
$idCliente = $_POST['idCliente'];
$Nombre = $_POST['Nombre'];
$Correo = $_POST['Correo'];
$EstadoCliente = 'activo'; // Por defecto, el cliente está activo

// Preparar e insertar los datos
$sql = "INSERT INTO clientes (idCliente, Nombre, Correo, EstadoCliente) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $idCliente, $Nombre, $Correo, $EstadoCliente);

if ($stmt->execute()) {
    echo json_encode(["message" => "Cliente registrado correctamente"]);
} else {
    echo json_encode(["message" => "Error al registrar el cliente"]);
}

$stmt->close();
$conn->close();
?>
