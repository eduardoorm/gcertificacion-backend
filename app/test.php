<?php
// Datos de conexión a la base de datos
$servername = "localhost"; // Cambia localhost por la dirección del servidor si es necesario
$username = "root"; // Cambia usuario por el nombre de usuario de la base de datos
$password = ""; // Cambia contraseña por la contraseña de la base de datos
$database = "gcercdsq_lms"; // Cambia nombre_base_de_datos por el nombre de la base de datos a la que te quieres conectar
$port = 3306; // Puerto personalizado

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database, $port);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "Conexión exitosaaa";
}

// Cerrar conexión
$conn->close();
?>