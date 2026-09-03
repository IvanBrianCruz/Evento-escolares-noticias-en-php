<?php
// index.php
session_start();
require_once 'includes/funciones.php';
$eventos = obtener_eventos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Eventos Escolares</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Instituto Tecnológico del Conocimiento</h1>
            <p>Sistema de Gestión de Eventos Escolares</p>
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
            <!-- Formulario de Publicación -->
            <section class="card">
                <h2>Publicar nuevo evento</h2>
                <form action="procesar_evento.php" method="POST">
                    
                    <fieldset>
                        <legend>Datos generales</legend>
                        <div class="form-group">
                            <label for="titulo">Título del evento</label>
                            <input type="text" id="titulo" name="titulo" required minlength="5" placeholder="Ej: Jornada de Programación">
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea id="descripcion" name="descripcion" required minlength="20" placeholder="Describí el evento (mínimo 20 caracteres)"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="categoria">Categoría</label>
                            <select id="categoria" name="categoria" required>
                                <option value="" disabled selected>-- Seleccionar --</option>
                                <option value="Académico">Académico</option>
                                <option value="Cultural">Cultural</option>
                                <option value="Deportivo">Deportivo</option>
                                <option value="Programación">Programación</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Fecha y horario</legend>
                        <div class="form-group">
                            <label for="fecha">Fecha del evento</label>
                            <!-- Validación básica de HTML5 para fecha futura usando min -->
                            <input type="date" id="fecha" name="fecha" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>
                        <div class="form-row">
                            <div class="form-group half">
                                <label for="hora_inicio">Hora de inicio</label>
                                <input type="time" id="hora_inicio" name="hora_inicio" required>
                            </div>
                            <div class="form-group half">
                                <label for="hora_fin">Hora de fin</label>
                                <input type="time" id="hora_fin" name="hora_fin" required>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Inscripción</legend>
                        <div class="form-group">
                            <label for="cupo">Cupo máximo (opcional)</label>
                            <input type="number" id="cupo" name="cupo" min="1" max="100" placeholder="Ej: 30">
                        </div>
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="inscripcion" name="inscripcion">
                            <label for="inscripcion">Requiere inscripción previa</label>
                        </div>
                    </fieldset>

                    <button type="submit" class="btn-submit">Publicar evento</button>
                </form>
            </section>
<!-- Listado de Eventos -->
            <section class="card mt-2">
                <h2>Eventos publicados</h2>
                <?php if (empty($eventos)): ?>
                    <p class="empty-state">No hay eventos publicados aún.</p>
                <?php else: ?>
                    <div class="eventos-list">
                        <?php foreach ($eventos as $evento): ?>
                            <article class="evento-item-completo">
                                
                                <div class="evento-header">
                                    <h3><?= htmlspecialchars($evento['Título'] ?? '') ?></h3>
                                    <?php 
                                        $cat = htmlspecialchars($evento['Categoría'] ?? '');
                                        $clase_badge = strtolower(str_replace('ó', 'o', $cat)); 
                                    ?>
                                    <span class="badge badge-<?= $clase_badge ?>"><?= $cat ?></span>
                                </div>
                                
                                <div class="evento-body">
                                    <p class="evento-descripcion">
                                        <?= nl2br(htmlspecialchars($evento['Descripción'] ?? '')) ?>
                                    </p>
                                    
                                    <div class="evento-detalles">
                                        <span>📅 <strong>Fecha:</strong> <?= htmlspecialchars($evento['Fecha'] ?? '') ?></span>
                                        <span>🕒 <strong>Horario:</strong> <?= htmlspecialchars($evento['Hora'] ?? '') ?></span>
                                        <span>👥 <strong>Cupo:</strong> <?= htmlspecialchars($evento['Cupo'] ?? 'Sin límite') ?></span>
                                        <span>📝 <strong>Inscripción previa:</strong> <?= htmlspecialchars($evento['Requiere inscripción'] ?? 'No') ?></span>
                                    </div>
                                </div>

                                <div class="evento-footer">
                                    <span><strong>ID:</strong> <?= htmlspecialchars($evento['ID'] ?? '') ?></span>
                                    <span><em>Publicado el <?= htmlspecialchars($evento['Creado'] ?? '') ?></em></span>
                                </div>

                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>