<?php
// ver_evento.php
session_start();
require_once 'includes/funciones.php';

$id_evento = $_GET['id'] ?? '';
$evento = obtener_evento_por_id($id_evento);

if (!$evento) {
    $_SESSION['mensaje_error'] = "El evento solicitado no existe.";
    header('Location: index.php');
    exit;
}

$inscripciones = obtener_inscripciones_por_evento($id_evento);
$esta_abierto = evento_esta_abierto($evento);
$requiere_inscripcion = ($evento['Requiere inscripción'] ?? 'No') === 'Sí';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($evento['Título']) ?> - Detalles</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Instituto Tecnológico del Conocimiento</h1>
            <p><a href="index.php" class="btn-volver">← Volver al listado de eventos</a></p>
        </header>

        <!-- Mensajes Flash -->
        <?php if (isset($_SESSION['mensaje_exito'])): ?>
            <div class="alerta exito"><?= $_SESSION['mensaje_exito'] ?></div>
            <?php unset($_SESSION['mensaje_exito']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje_error'])): ?>
            <div class="alerta error"><?= $_SESSION['mensaje_error'] ?></div>
            <?php unset($_SESSION['mensaje_error']); ?>
        <?php endif; ?>

        <main>
            <!-- Detalle completo del evento -->
            <section class="card">
                <div class="evento-header">
                    <h2><?= htmlspecialchars($evento['Título']) ?></h2>
                    <?php 
                        $cat = htmlspecialchars($evento['Categoría'] ?? '');
                        $clase_badge = strtolower(str_replace('ó', 'o', $cat)); 
                    ?>
                    <span class="badge badge-<?= $clase_badge ?>"><?= $cat ?></span>
                </div>
                
                <div class="evento-body mt-2">
                    <p class="evento-descripcion"><?= nl2br(htmlspecialchars($evento['Descripción'])) ?></p>
                    <div class="evento-detalles">
                        <span>📅 <strong>Fecha:</strong> <?= htmlspecialchars($evento['Fecha']) ?></span>
                        <span>🕒 <strong>Horario:</strong> <?= htmlspecialchars($evento['Hora']) ?></span>
                        <span>👥 <strong>Cupo total:</strong> <?= htmlspecialchars($evento['Cupo']) ?></span>
                        <span>✅ <strong>Inscriptos actuales:</strong> <?= count($inscripciones) ?></span>
                    </div>
                </div>
            </section>

            <!-- Formulario de inscripción -->
            <?php if ($requiere_inscripcion): ?>
                <section class="card mt-2">
                    <?php if ($esta_abierto): ?>
                        <h2>Formulario de inscripción</h2>
                        <form action="inscribir.php" method="POST">
                            <input type="hidden" name="id_evento" value="<?= htmlspecialchars($id_evento) ?>">
                            
                            <div class="form-row">
                                <div class="form-group half">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" required placeholder="Nombre del estudiante">
                                </div>
                                <div class="form-group half">
                                    <label for="apellido">Apellido</label>
                                    <input type="text" id="apellido" name="apellido" required placeholder="Apellido">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group half">
                                    <label for="email">Email</label>
                            
                                    <input type="email" id="email" name="email" required placeholder="correo@ejemplo.com">
                                </div>
                                <div class="form-group half">
                                    <label for="curso">Curso/Año</label>
                                    <input type="text" id="curso" name="curso" required placeholder="Ej: 4to A">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="comentarios">Comentarios adicionales (opcional)</label>
                                <textarea id="comentarios" name="comentarios" placeholder="Alguna observación..."></textarea>
                            </div>

                            <button type="submit" class="btn-submit">Confirmar Inscripción</button>
                        </form>
                    <?php else: ?>
                        <div class="alerta error">
                            <strong>Cupo Completo.</strong> Ya no se aceptan más inscripciones para este evento.
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <!-- Visualización de participantes -->
            <section class="card mt-2">
                <h2>Participantes inscriptos (<?= count($inscripciones) ?>)</h2>
                <?php if (empty($inscripciones)): ?>
                    <p class="empty-state">Aún no hay participantes inscriptos.</p>
                <?php else: ?>
                    <div class="tabla-responsive">
                        <table class="tabla-participantes">
                            <thead>
                                <tr>
                                    <th>NOMBRE</th>
                                    <th>APELLIDO</th>
                                    <th>CURSO/AÑO</th>
                                    <th>FECHA DE INSCRIPCIÓN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inscripciones as $inscrito): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($inscrito['Nombre'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($inscrito['Apellido'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($inscrito['Curso/Año'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($inscrito['Fecha de inscripción'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>