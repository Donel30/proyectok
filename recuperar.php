<?php
// reset_password.php
include 'db_connection.php';
// Incluyendo el archivo de conexión


// Ahora puedes usar $conn para interactuar con la base de datos.
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = :username");
$stmt->bindParam(':username', $username);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);


// Validar el token
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $query = "SELECT email, expiry FROM password_resets WHERE token = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $expiry = $row['expiry'];

        // Verificar si el token ha expirado
        if (strtotime($expiry) > time()) {
            // Mostrar formulario para nueva contraseña
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $email = $row['email'];

                // Actualizar la contraseña en la base de datos
                $query = "UPDATE usuarios SET password = ? WHERE email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $new_password, $email);
                $stmt->execute();

                // Eliminar el token después de usarlo
                $query = "DELETE FROM password_resets WHERE email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $email);
                $stmt->execute();

                echo "Contraseña restablecida correctamente.";
            } else {
                // Formulario para la nueva contraseña
                echo '<form method="POST">
                        <label for="new_password">Nueva Contraseña:</label>
                        <input type="password" id="new_password" name="new_password" required>
                        <button type="submit">Cambiar Contraseña</button>
                      </form>';
            }
        } else {
            echo "El enlace ha expirado.";
        }
    } else {
        echo "Token no válido.";
    }
} else {
    echo "Token no proporcionado.";
}
?>
