<?php
// Incluir TCPDF
require_once __DIR__ . '/vendor/autoload.php';

// Conexión a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'hotel_light_plaza');
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener las fechas del formulario y validarlas
$fecha_inicio = DateTime::createFromFormat('Y-m-d', $_POST['fecha_inicio']);
$fecha_final = DateTime::createFromFormat('Y-m-d', $_POST['fecha_final']);

// Verificar si las fechas son válidas
if (!$fecha_inicio || !$fecha_final) {
    die("Las fechas no tienen el formato correcto.");
}

// Convertir las fechas al formato adecuado para la base de datos
$fecha_inicio = $fecha_inicio->format('Y-m-d');
$fecha_final = $fecha_final->format('Y-m-d');

// Validar que las fechas no estén vacías
if (empty($fecha_inicio) || empty($fecha_final)) {
    die("Ambas fechas (inicio y final) son obligatorias.");
}

// Consulta para obtener los datos del rango de fechas utilizando sentencias preparadas
$sql = "SELECT * FROM cliente WHERE fecha_registro BETWEEN ? AND ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('ss', $fecha_inicio, $fecha_final);
$stmt->execute();
$resultado = $stmt->get_result();

// Verificar si hay registros
if ($resultado->num_rows == 0) {
    die("No se encontraron registros para el rango de fechas seleccionado.");
}

// Variables para totales
$total_precios = 0;
$total_habitaciones = 0;

// Tabla HTML para el reporte con la información centrada
$tabla_datos = '
    <style>
        table { 
            border-collapse: collapse; 
            width: 100%; 
            font-size: 12px; 
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        th, td { 
            padding: 12px; 
            text-align: center;
            border-bottom: 2px solid #ddd; 
        }
        th { 
            background-color: #4CAF50; 
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) { 
            background-color: #f2f2f2; 
        }
        tr:hover {
            background-color: #ddd;
        }
        .precio { 
            font-size: 14px; 
            font-weight: bold; 
            color: #fff; 
            background-color: #FF5733; 
            border-radius: 5px; 
            text-align: center;
            padding: 8px;
        }
        .total { 
            font-weight: bold;
            font-size: 14px; 
            color: #333;
        }
        .total-section {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
        }
    </style>
    <table>
        <thead>
            <tr>
                <th>CEDULA</th>
                <th>NOMBRE</th>
                <th>CORREO</th>
                <th>PRECIO</th>
                <th>HABITACION</th>
            </tr>
        </thead>
        <tbody>
';

while ($fila = $resultado->fetch_assoc()) {
    $tabla_datos .= "<tr>
        <td>{$fila['idCliente']}</td>
        <td>{$fila['Nombre']}</td>
        <td>{$fila['Correo']}</td>
        <td class='precio'>$" . number_format($fila['precio'], 2) . "</td>
        <td>{$fila['roomNumber']}</td>
    </tr>";
    $total_precios += $fila['precio'];
    $total_habitaciones++;
}

$tabla_datos .= '
        </tbody>
    </table>
';

// Crear el objeto TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Hotel Light Plaza');
$pdf->SetTitle('Reporte');
$pdf->SetSubject('Reporte generado con TCPDF');

// Verificar y cargar la imagen
$logo_path = __DIR__ . '/img/logo2.png'; // Ruta al logo
if (file_exists($logo_path)) {
    $pdf->Image($logo_path, 15, 10, 40, 40, 'PNG'); // Ajusta las coordenadas si es necesario
} else {
    // Si no se encuentra la imagen, imprime un mensaje de error
    $pdf->Cell(0, 10, 'Logo no encontrado', 0, 1, 'C');
}


$pdf->SetHeaderData('', 0, 'Hotel Light Plaza', "Fechas: $fecha_inicio a $fecha_final", [0, 64, 128], [255, 255, 255]);
$pdf->SetHeaderFont(['helvetica', '', 12]);
$pdf->SetFooterFont(['helvetica', 'I', 8]);
$pdf->SetMargins(15, 40, 15);
$pdf->SetAutoPageBreak(TRUE, 25);

// Establecer la fuente
$pdf->SetFont('helvetica', '', 12);

// Agregar una página
$pdf->AddPage();

// Título del reporte
$pdf->SetFont('helvetica', 'B', 18);
$pdf->Cell(0, 10, 'Reporte General', 0, 1, 'C');
$pdf->Ln(5);

// Mostrar rango de fechas
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, "Rango de Fechas: $fecha_inicio a $fecha_final", 0, 1, 'C');
$pdf->Ln(10);

// Insertar la tabla de datos
$pdf->writeHTML($tabla_datos, true, false, true, false, '');

// Totales
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Totales del Reporte General', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, "Total de Habitaciones: $total_habitaciones", 0, 1, 'C');
$pdf->Cell(0, 10, "Total de Precios: $" . number_format($total_precios, 2), 0, 1, 'C');

// Pie de página
$pdf->SetY(-15);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 10, 'Gracias por confiar en Hotel Light Plaza', 0, 0, 'C');

// Salida del PDF
$pdf->Output('reporte.pdf', 'I');

// Cerrar la conexión
$conexion->close();
?>
