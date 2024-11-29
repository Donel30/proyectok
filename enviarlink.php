<?php
// send_reset_link.php

// Conexión a la base de datos
include 'db_connection.php';
// Incluyendo el archivo de conexión
require 'PHP_M/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];

    // Consulta para encontrar el usuario y el correo
    $query = "SELECT Correo FROM administrador WHERE Correo = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([$username]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $email = $row['Correo'];
        // Generar un token de recuperación
        $token = bin2hex(random_bytes(50));

        // Guardar el token y su expiración en la tabla administrador
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));
        $query = "UPDATE administrador SET token_recuperacion = ?, token_expiry = ? WHERE Correo = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$token, $expiry, $email]);

        // Enviar el correo electrónico
        $resetLink = "https://tu-dominio.com/reset_password.php?token=$token";
        $subject = "Restablecimiento de contraseña - Hotel Light Plaza";
        $message = "Haz clic en el siguiente enlace para restablecer tu contraseña: $resetLink";
        
        if (Enviar_Correo($email, $token, $message)) {
            // Redirigir al inicio después de enviar el correo
            header("Location: inicio.html");
            exit(); // Importante para detener el script después de la redirección
        } else {
            echo "Error al enviar el correo. Inténtalo más tarde.";
        }
    } else {
        echo "Usuario no encontrado.";
    }
}

function Enviar_Correo($correo, $token, $message) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'benoriega@unicesar.edu.co';  // Correo Gmail
        $mail->Password   = 'ebmnzpevnzgqpvuc';          // Contraseña Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Configuración del correo
        $mail->setFrom('benoriega@unicesar.edu.co', 'Hotel Light Plaza');
        $mail->addAddress($correo);
        $mail->Subject = "Recuperación de contraseña";
        $mail->Body    = $message;

        $mail->send();
        return true; // Éxito al enviar
    } catch (Exception $e) {
        return false; // Error al enviar
    }
}
?>

