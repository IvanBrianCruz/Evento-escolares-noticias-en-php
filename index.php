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
                            <?php 
                                $id_evento = htmlspecialchars($evento['ID'] ?? '');
                                $titulo = htmlspecialchars($evento['Título'] ?? '');
                                $esta_abierto = evento_esta_abierto($evento);
                            ?>
                            <article class="evento-item-completo">
                                
                                <div class="evento-header">
                                    <!-- El título ahora es un enlace a ver_evento.php-->
                                    <h3 class="empty-state">
                                        <a href="ver_evento.php?id=<?= $id_evento ?>" class="enlace-titulo">
                                            <?= $titulo ?>
                                        </a>
                                    </h3>
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
                                    </div>
                                </div>

                                <div class="evento-footer evento-acciones">
                                    <div class="estado-evento">
                                        <!-- Mostrar estado Abierto o Completo[cite: 2] -->
                                        <?php if ($esta_abierto): ?>
                                            <span class="badge badge-abierto">Abierto</span>
                                        <?php else: ?>
                                            <span class="badge badge-completo">Completo</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="botones-evento">
                                        <!-- Botón Inscribirse si está abierto[cite: 2] -->
                                        <?php if ($esta_abierto && ($evento['Requiere inscripción'] ?? 'No') === 'Sí'): ?>
                                            <a href="ver_evento.php?id=<?= $id_evento ?>" class="btn-inscribirse">Inscribirse</a>
                                        <?php endif; ?>
                                    </div>
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