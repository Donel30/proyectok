<?php
$host = 'localhost';  // El host de tu servidor MySQL (puede ser una IP si no es local)
$dbname = 'hotel_light_plaza';  // Nombre de la base de datos
$username = 'root';  // Tu usuario de MySQL
$password = '';  // Contrase�a de MySQL (reemplaza con la tuya)

try {
    // Crear una conexion PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Establecer el modo de error de PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexi�n: " . $e->getMessage());
}
?>
