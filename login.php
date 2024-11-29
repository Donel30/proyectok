<?php
session_start();

// Incluir conexión a la base de datos
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    try {
        // Consultar al administrador en la base de datos
        $stmt = $conn->prepare("SELECT * FROM Administrador WHERE Correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el administrador existe y la contraseña coincide
        if ($admin && $admin['Contraseña'] === $password) {
            if ($admin['Estado_Administrador'] === 'activo') {
                // Guardar datos en la sesión
                $_SESSION['idAdministrador'] = $admin['idAdministrador'];
                $_SESSION['Nombre'] = $admin['Nombre'];

                // Redirigir al panel principal
                header('Location: index.html');
                exit;
            } else {
                echo "La cuenta está inactiva. Contacte al soporte.";
            }
        } else {
            echo "Correo o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        echo "Error en la conexión: " . $e->getMessage();
    }
}
?>
