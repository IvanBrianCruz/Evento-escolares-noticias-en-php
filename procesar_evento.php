<?php
// procesar_evento.php
session_start();
require_once 'includes/funciones.php';

// Verificar que el método de envío sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Capturar y sanitizar datos
$titulo = htmlspecialchars(trim($_POST['titulo'] ?? ''), ENT_QUOTES, 'UTF-8');
$fecha = htmlspecialchars(trim($_POST['fecha'] ?? ''), ENT_QUOTES, 'UTF-8');
$hora_inicio = htmlspecialchars(trim($_POST['hora_inicio'] ?? ''), ENT_QUOTES, 'UTF-8');
$hora_fin = htmlspecialchars(trim($_POST['hora_fin'] ?? ''), ENT_QUOTES, 'UTF-8');
$categoria = htmlspecialchars(trim($_POST['categoria'] ?? ''), ENT_QUOTES, 'UTF-8');
$cupo = htmlspecialchars(trim($_POST['cupo'] ?? ''), ENT_QUOTES, 'UTF-8');
$descripcion = htmlspecialchars(trim($_POST['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
$inscripcion = isset($_POST['inscripcion']) ? true : false;

$errores = [];

// Validaciones en el servidor
if (empty($titulo) || strlen($titulo) < 5) {
    $errores[] = "El título debe tener al menos 5 caracteres.";
}

if (empty($fecha)) {
    $errores[] = "La fecha es obligatoria.";
} else {
    $fecha_timestamp = strtotime($fecha);
    $hoy_timestamp = strtotime(date('Y-m-d'));
    if ($fecha_timestamp <= $hoy_timestamp) {
        $errores[] = "La fecha del evento debe ser futura.";
    }
}

$categorias_validas = ['Académico', 'Cultural', 'Deportivo', 'Programación', 'Otro'];
if (!in_array($categoria, $categorias_validas)) {
    $errores[] = "Debe seleccionar una categoría válida.";
}

if (!empty($cupo) && (!is_numeric($cupo) || $cupo < 1 || $cupo > 100)) {
    $errores[] = "El cupo debe ser un valor entre 1 y 100.";
}

if (empty($descripcion) || strlen($descripcion) < 20) {
    $errores[] = "La descripción debe tener al menos 20 caracteres.";
}

// Manejo de éxito o error
if (empty($errores)) {
    $datos = compact('titulo', 'fecha', 'hora_inicio', 'hora_fin', 'categoria', 'cupo', 'descripcion', 'inscripcion');
    
    if (guardar_evento($datos)) {
        $_SESSION['mensaje_exito'] = "El evento '$titulo' se ha publicado correctamente.";
    } else {
        $_SESSION['mensaje_error'] = "Hubo un problema al guardar el evento.";
    }
} else {
    $_SESSION['mensaje_error'] = implode("<br>", $errores);
   
}

// Redirección POST/Redirect/GET
header('Location: index.php');
exit;
?>