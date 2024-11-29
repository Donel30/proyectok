<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .input-container {
            margin-bottom: 15px;
            position: relative;
        }
        .input-container label {
            font-size: 14px;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }
        .input-container input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .input-container input:focus {
            border-color: #007BFF;
            outline: none;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            font-size: 14px;
            color: red;
            text-align: center;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Recuperar Contraseña</h2>
        <form method="POST" action="enviarlink.php" id="resetForm">
            <div class="input-container">
                <label for="username">Nombre de usuario o correo electrónico:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="message" id="errorMessage">Por favor, ingresa un nombre de usuario o correo válido.</div>
            <button type="submit">Enviar enlace de recuperación</button>
        </form>
    </div>

    <script>
        // Validación del formulario
        const form = document.getElementById('resetForm');
        const errorMessage = document.getElementById('errorMessage');

        form.addEventListener('submit', function(event) {
            const username = document.getElementById('username').value.trim();

            if (!username) {
                event.preventDefault(); // Evitar el envío del formulario
                errorMessage.style.display = 'block';
            }
        });
    </script>
</body>
</html>
