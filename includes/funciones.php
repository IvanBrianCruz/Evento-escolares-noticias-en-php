<?php
// includes/funciones.php

define('ARCHIVO_EVENTOS', __DIR__ . '/../eventos.txt');
define('ARCHIVO_INSCRIPCIONES', __DIR__ . '/../inscripciones.txt'); // Nuevo archivo de almacenamiento de inscripciones

function obtener_eventos() {
    $eventos = [];
    if (file_exists(ARCHIVO_EVENTOS)) {
        $contenido = file_get_contents(ARCHIVO_EVENTOS);
        $bloques = explode('===FIN EVENTO===', $contenido);
        
        foreach ($bloques as $bloque) {
            if (trim($bloque) === '') continue;
            
            $lineas = explode("\n", trim(str_replace('===EVENTO===', '', $bloque)));
            $evento = [];
            foreach ($lineas as $linea) {
                if (strpos($linea, ': ') !== false) {
                    list($clave, $valor) = explode(': ', $linea, 2);
                    $evento[trim($clave)] = trim($valor);
                }
            }
            if (!empty($evento)) {
                $eventos[] = $evento;
            }
        }
        
        usort($eventos, function($a, $b) {
            return strtotime($a['Fecha']) - strtotime($b['Fecha']);
        });
    }
    return $eventos;
}

function obtener_evento_por_id($id) {
    $eventos = obtener_eventos();
    foreach ($eventos as $evento) {
        if (isset($evento['ID']) && $evento['ID'] === $id) {
            return $evento;
        }
    }
    return null;
}

function generar_id_evento() {
    $año_actual = date('Y');
    $eventos = obtener_eventos();
    $numero = count($eventos) + 1;
    return 'EVT-' . $año_actual . sprintf('%03d', $numero);
}

function guardar_evento($datos) {
    $id = generar_id_evento();
    $fecha_creacion = date('Y-m-d H:i:s');
    
    $bloque = "===EVENTO===\n";
    $bloque .= "ID: {$id}\n";
    $bloque .= "Título: {$datos['titulo']}\n";
    $bloque .= "Fecha: {$datos['fecha']}\n";
    $bloque .= "Hora: {$datos['hora_inicio']} - {$datos['hora_fin']}\n";
    $bloque .= "Categoría: {$datos['categoria']}\n";
    $bloque .= "Cupo: " . ($datos['cupo'] ?: 'Sin límite') . "\n";
    $bloque .= "Descripción: {$datos['descripcion']}\n";
    $bloque .= "Requiere inscripción: " . ($datos['inscripcion'] ? 'Sí' : 'No') . "\n";
    $bloque .= "Creado: {$fecha_creacion}\n";
    $bloque .= "===FIN EVENTO===\n\n";

    return file_put_contents(ARCHIVO_EVENTOS, $bloque, FILE_APPEND | LOCK_EX);
}



function obtener_inscripciones_por_evento($id_evento) {
    $inscripciones = [];
    if (file_exists(ARCHIVO_INSCRIPCIONES)) {
        $contenido = file_get_contents(ARCHIVO_INSCRIPCIONES);
        $bloques = explode('===FIN INSCRIPCION===', $contenido);
        
        foreach ($bloques as $bloque) {
            if (trim($bloque) === '') continue;
            
            $lineas = explode("\n", trim(str_replace('===INSCRIPCION===', '', $bloque)));
            $inscripcion = [];
            foreach ($lineas as $linea) {
                if (strpos($linea, ': ') !== false) {
                    list($clave, $valor) = explode(': ', $linea, 2);
                    $inscripcion[trim($clave)] = trim($valor);
                }
            }
            if (isset($inscripcion['ID Evento']) && $inscripcion['ID Evento'] === $id_evento) {
                $inscripciones[] = $inscripcion;
            }
        }
    }
    return $inscripciones;
}

function evento_esta_abierto($evento) {
    if ($evento['Requiere inscripción'] === 'No') {
        return true; 
    }
    if ($evento['Cupo'] === 'Sin límite') {
        return true;
    }
    
    $inscritos = count(obtener_inscripciones_por_evento($evento['ID']));
    return $inscritos < (int)$evento['Cupo'];
}
?>