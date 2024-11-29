<?php
$host = "localhost"; // Ajusta estos valores según tu configuración
$dbname = "hotel_light_plaza";
$username = "root"; // Ajusta según tu usuario de base de datos
$password = ""; // Ajusta según tu contraseña de base de datos

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verificar si se realiza una solicitud GET (obteniendo eventos)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM reservas";
    $result = $conn->query($sql);

    $reservas = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Solo mostramos el inicio (fecha de ingreso)
            $reservas[] = [
                'id' => $row['idReservas'],
                'title' => $row['nombreUsuario'] . " - Habitación: " . $row['roomNumber'],
                'start' => $row['fechaingreso'], // solo la fecha de ingreso
                'telefono' => $row['telefono'],
                'precio' => $row['Precio']
            ];
        }
    }
    echo json_encode($reservas);
}

// Verificar si se realiza una solicitud POST (guardar nueva reserva)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $nombreUsuario = $data['title'];
    $start = $data['start'];
    $telefono = $data['telefono'];
    $precio = $data['precio'];

    $sql = "INSERT INTO reservas (nombreUsuario, fechaingreso, telefono, Precio) 
            VALUES ('$nombreUsuario', '$start', '$telefono', '$precio')";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Error al guardar la reserva"]);
    }
}

// Verificar si se realiza una solicitud DELETE (eliminar reserva)
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    $sql = "DELETE FROM reservas WHERE idReservas = $id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Error al eliminar la reserva"]);
    }
}

$conn->close();
?>
