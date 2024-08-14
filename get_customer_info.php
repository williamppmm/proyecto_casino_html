<?php
session_start();

// Verifica el contenido de la sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

// Obtener el ID del usuario de la sesión
$user_id = $_SESSION['user_id'];

// Obtener el tiempo de inicio de sesión
$session_start_time = $_SESSION['session_start_time'] ?? time();

// Calcular el tiempo de sesión
$current_time = time();
$session_duration = $current_time - $session_start_time;

// Datos de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "casinolafortuna";

// Crear una conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos: ' . $conn->connect_error]);
    exit();
}

// Consultar la información del cliente
$sql = "SELECT primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, correo_electronico FROM clientes WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $correo_electronico);
    $stmt->fetch();
    $full_name = trim($primer_nombre . ' ' . $segundo_nombre . ' ' . $primer_apellido . ' ' . $segundo_apellido);
    echo json_encode([
        'success' => true,
        'full_name' => $full_name,
        'email' => $correo_electronico,
        'session_time_seconds' => $session_duration
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontró información del usuario']);
}

$stmt->close();
$conn->close();
?>