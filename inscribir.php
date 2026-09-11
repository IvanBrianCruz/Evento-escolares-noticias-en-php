<?php
// inscribir.php
session_start();
require_once 'includes/funciones.php';

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Capturar datos y sanitizar
$id_evento = htmlspecialchars(trim($_POST['id_evento'] ?? ''), ENT_QUOTES, 'UTF-8');
$nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
$apellido = htmlspecialchars(trim($_POST['apellido'] ?? ''), ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
$curso = htmlspecialchars(trim($_POST['curso'] ?? ''), ENT_QUOTES, 'UTF-8');
$comentarios = htmlspecialchars(trim($_POST['comentarios'] ?? ''), ENT_QUOTES, 'UTF-8');

$errores = [];

// Validar existencia de evento y disponibilidad
$evento = obtener_evento_por_id($id_evento);

if (!$evento) {
    $errores[] = "El evento especificado no existe.";
} else {
    if (!evento_esta_abierto($evento)) {
        $errores[] = "Lo sentimos, el evento ya no tiene cupos disponibles.";
    }
}

// Validaciones en servidor de campos obligatorios
if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
if (empty($apellido)) $errores[] = "El apellido es obligatorio.";
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "Debe ingresar un email válido.";
}
if (empty($curso)) $errores[] = "El curso/año es obligatorio.";

if (empty($errores)) {
    $fecha_inscripcion = date('Y-m-d H:i:s');
    
    // Formato estructurado para la inscripción
    $bloque = "===INSCRIPCION===\n";
    $bloque .= "ID Evento: {$id_evento}\n";
    $bloque .= "Nombre: {$nombre}\n";
    $bloque .= "Apellido: {$apellido}\n";
    $bloque .= "Email: {$email}\n";
    $bloque .= "Curso/Año: {$curso}\n";
    $bloque .= "Comentarios: {$comentarios}\n";
    $bloque .= "Fecha de inscripción: {$fecha_inscripcion}\n";
    $bloque .= "===FIN INSCRIPCION===\n\n";

    // Almacenar inscripción en inscripciones.txt
    if (file_put_contents(ARCHIVO_INSCRIPCIONES, $bloque, FILE_APPEND | LOCK_EX)) {
        $_SESSION['mensaje_exito'] = "Inscripción confirmada exitosamente para $nombre $apellido.";
    } else {
        $_SESSION['mensaje_error'] = "Hubo un error al guardar la inscripción.";
    }
} else {
    $_SESSION['mensaje_error'] = implode("<br>", $errores);
}

// Redirección POST/Redirect/GET a la página del evento
header('Location: ver_evento.php?id=' . urlencode($id_evento));
exit;
?>