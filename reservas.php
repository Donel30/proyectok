<?php
// Información de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel_light_plaza";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $nombreUsuario = $_POST['nombre'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $habitacion = $_POST['habitacion'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $fechaingreso = $_POST['fechaingreso'] ?? '';
    $fechasalida = $_POST['fechasalida'] ?? '';

    // Validar que todos los campos están llenos
    if (
        empty($nombreUsuario) || empty($telefono) || empty($habitacion) ||
        empty($precio) || empty($fechaingreso) || empty($fechasalida)
    ) {
        echo "Error: Todos los campos son obligatorios.";
        exit;
    }

    // Preparar la consulta para insertar los datos en la base de datos
    $stmt = $conn->prepare("INSERT INTO reservas (nombreUsuario, roomNumber, telefono, precio, fechaingreso, fechasalida, estadoReserva) VALUES (?, ?, ?, ?, ?, ?, 'activo')");
    $stmt->bind_param("ssssss", $nombreUsuario, $habitacion, $telefono, $precio, $fechaingreso, $fechasalida);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "La reserva ha sido guardada con éxito.";
        header("Location: index.html");
        exit;
    } else {
        echo "Error al guardar la reserva: " . $stmt->error;
    }

    // Cerrar la declaración
    $stmt->close();
}

// Cerrar la conexión
$conn->close();
?>
