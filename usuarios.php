<?php
header('Content-Type: application/json');

// Conectar a la base de datos
$conn = new mysqli("localhost", "root", "", "hotel_light_plaza");

if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}

// Obtener las fechas desde los parámetros GET
$startDate = $_GET['startDate'] ?? null;
$endDate = $_GET['endDate'] ?? null;

// Validar el formato de las fechas (YYYY-MM-DD)
if ($startDate && $endDate) {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
        die(json_encode(["error" => "Las fechas deben tener el formato YYYY-MM-DD."]));
    }

    // Consulta con fechas
    $sql = "SELECT idCliente, Nombre, Correo, precio, roomNumber 
            FROM cliente 
            WHERE fecha_registro BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
} else {
    // Consulta sin fechas (todos los registros)
    $sql = "SELECT idCliente, Nombre, Correo, precio, roomNumber FROM cliente";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

// Array para almacenar los resultados
$cliente = [];

while ($row = $result->fetch_assoc()) {
    $cliente[] = $row;
}

// Cerrar la conexión
$stmt->close();
$conn->close();

// Enviar los resultados como JSON
echo json_encode($cliente);
?>
