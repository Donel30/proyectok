const express = require('express');
const mysql = require('mysql');
const app = express();
const PORT = 3000;
const cors = require('cors');
app.use(cors({
    origin: 'http://localhost', // Permite solo solicitudes desde http://localhost
}));


// Middleware para manejar datos JSON
app.use(express.json());

// Configuración de la base de datos
const connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'hotel_light_plaza',
    port: 3306 // Puerto para MySQL
});

// Conectar a la base de datos
connection.connect((err) => {
    if (err) {
        console.error('Error conectando a la base de datos:', err.stack);
        return;
    }
    console.log('Conexión a MySQL exitosa');
});

// Ruta para obtener habitaciones
app.get('/api/habitaciones', (req, res) => {
    const sql = 'SELECT roomNumber, precio, estadohabitacion, descripcion FROM habitacion';
    connection.query(sql, (error, results) => {
        if (error) {
            console.error('Error al obtener habitaciones:', error);
            res.status(500).json({ message: 'Error al obtener habitaciones' });
            return;
        }
        res.json(results);
    });
});


// Ruta para registrar cliente y actualizar estado de habitación
app.post('/api/clientes', (req, res) => {
    const { idCliente, Nombre, Correo, precio, roomNumber } = req.body;
    const fecha_registro = new Date(); // Genera la fecha y hora actual

    // Validaciones
    if (!idCliente || !Nombre || !precio || !roomNumber) {
        return res.status(400).json({ message: 'Todos los campos son obligatorios' });
    }

    if (isNaN(precio) || precio <= 0) {
        return res.status(400).json({ message: 'El precio debe ser un número mayor a 0' });
    }
    // Validación de correo (opcional, si está presente)
    if (Correo && !/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(Correo)) {
        return res.status(400).json({ message: 'El correo electrónico no tiene un formato válido' });
    }

    // Verificar si el roomNumber ya está ocupado
    const checkHabitacionQuery = 'SELECT estadohabitacion FROM habitacion WHERE roomNumber = ?';
    connection.query(checkHabitacionQuery, [roomNumber], (error, results) => {
        if (error) {
            console.error('Error al verificar estado de la habitación:', error);
            return res.status(500).json({ message: 'Error al verificar estado de la habitación' });
        }

        if (results.length === 0 || results[0].estadohabitacion === 'Ocupada') {
            return res.status(400).json({ message: 'La habitación ya está ocupada' });
        }

        // Si la habitación está disponible, se actualiza el estado a "Ocupada"
        const updateHabitacionQuery = 'UPDATE habitacion SET estadohabitacion = "Ocupada" WHERE roomNumber = ?';
        connection.query(updateHabitacionQuery, [roomNumber], (error, results) => {
            if (error) {
                console.error('Error al actualizar estado de la habitación:', error);
                return res.status(500).json({ message: 'Error al actualizar estado de la habitación' });
            }

            // Insertar cliente
            const sql = 'INSERT INTO cliente (idCliente, Nombre, Correo, precio, roomNumber, fecha_registro) VALUES (?, ?, ?, ?, ?, ?)';
            connection.query(sql, [idCliente, Nombre, Correo, precio, roomNumber, fecha_registro], (error, results) => {
                if (error) {
                    console.error('Error al registrar cliente:', error);
                    return res.status(500).json({ message: 'Error al registrar Cliente' });
                }

                res.status(201).json({ message: 'Cliente y habitación registrados correctamente' });
            });
        });
    });
});



// Ruta para desocupar una habitación
// Ruta para desocupar la habitación
app.put('/api/desocupar/:roomNumber', (req, res) => {
    const roomNumber = req.params.roomNumber;

    // Actualizar el estado de la habitación a "Disponible"
    const sql = 'UPDATE habitacion SET estadohabitacion = "Disponible" WHERE roomNumber = ?';
    connection.query(sql, [roomNumber], (error, results) => {
        if (error) {
            console.error('Error al desocupar la habitación:', error);
            return res.status(500).json({ message: 'Error al desocupar la habitación' });
        }

        if (results.affectedRows === 0) {
            return res.status(404).json({ message: 'Habitación no encontrada' });
        }

        res.status(200).json({ message: 'Habitación desocupada correctamente' });
    });
});


// Manejo de rutas no encontradas
app.use((req, res) => {
    res.status(404).json({ message: 'Ruta no encontrada' });
});

// Iniciar el servidor
app.listen(PORT, () => {
    console.log(`Servidor corriendo en http://localhost:${PORT}`);
});
