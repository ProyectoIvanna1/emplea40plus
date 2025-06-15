<?php
require_once("../conexion.php");
$conexion = new Conexion();
$conectar = $conexion->conectar();

// Sesión y validación admin omitida para simplificar

// Obtener ayudas y cursos
$ayudas = $conectar->query("SELECT * FROM ayudas ORDER BY fecha_publicacion DESC")->fetchAll(PDO::FETCH_ASSOC);
$cursos = $conectar->query("SELECT * FROM cursos ORDER BY fecha_publicacion DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- metadatos, CSS, JS -->
    <script src="../../js/jquery/jquery-3.7.1.js"></script>
    <script>
    $(document).ready(function () {
        $(".btn-eliminar").click(function () {
            const id = $(this).data("id");
            const tipo = $(this).data("tipo");
            if (confirm("¿Seguro que quieres eliminar este registro?")) {
                $.ajax({
                    url: "eliminarCursos.php", // Aquí se llama a otro archivo
                    method: "POST",
                    dataType: "json",
                    data: { id: id, tipo: tipo },
                    success: function (res) {
                        if (res.success) {
                            alert("Eliminado correctamente.");
                            $(`tr[data-id='${id}']`).remove();
                        } else {
                            alert("Error al eliminar: " + (res.message || "Error desconocido"));
                        }
                    },
                    error: function () {
                        alert("Error de conexión con el servidor.");
                    }
                });
            }
        });
    });
    </script>

    <meta charset="UTF-8">
    <title>Ofertas Publicadas</title>
    <link rel="stylesheet" href="../../css/estiloGeneral.css">
    <link rel="stylesheet" href="../../css/estilosRevCursosAyudas.css">
</head>
<body>
    <header>
        <nav>
            <a href="indexLog.php"><img src="../../img/logoEmplea40plus.png" alt="Logo de Emplea40+" id="logo"></a>
            <button class="menu-toggle" onclick="document.getElementById('menu').classList.toggle('active');document.getElementById('esquina').classList.toggle('active');">☰</button>
            <ul id="menu">
                <li><a href="admin.php">Panel de Administración</a></li>
            </ul>
        </nav>
    </header>
<main>
    <h1>Ayudas y Cursos</h1>
    <a href="crearNuevaAC.php?tipo=ayuda" class="boton-crear">+ Crear nueva ayuda</a>
    <a href="crearNuevaAC.php?tipo=curso

" class="boton-crear">+ Crear nuevo curso</a>

    <h2>Ayudas</h2>
    <?php if (count($ayudas) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Enlace</th>
                    <th>Imagen</th>
                    <th>Fecha</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ayudas as $ayuda): ?>
                    <tr data-id="<?= $ayuda['id'] ?>">
                        <td><?= htmlspecialchars($ayuda['nombre_ayuda']) ?></td>
                        <td><?= htmlspecialchars($ayuda['descripcion']) ?></td>
                        <td><a href="<?= htmlspecialchars($ayuda['enlace']) ?>" target="_blank">Ver</a></td>
                        <td><img src="<?= htmlspecialchars($ayuda['imagen']) ?>" alt="Imagen" width="50"></td>
                        <td><?= $ayuda['fecha_publicacion'] ?></td>
                        <td><button class="btn-eliminar" data-id="<?= $ayuda['id'] ?>" data-tipo="ayuda">Eliminar</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay ayudas registradas actualmente.</p>
    <?php endif; ?>

    <h2>Cursos</h2>
    <?php if (count($cursos) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Enlace</th>
                    <th>Imagen</th>
                    <th>Fecha</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cursos as $curso): ?>
                    <tr data-id="<?= $curso['id'] ?>">
                        <td><?= htmlspecialchars($curso['nombre_curso']) ?></td>
                        <td><?= htmlspecialchars($curso['descripcion']) ?></td>
                        <td><a href="<?= htmlspecialchars($curso['enlace_curso']) ?>" target="_blank">Ver</a></td>
                        <td><img src="<?= htmlspecialchars($curso['imagen']) ?>" alt="Imagen" width="50"></td>
                        <td><?= $curso['fecha_publicacion'] ?></td>
                        <td><button class="btn-eliminar" data-id="<?= $curso['id'] ?>" data-tipo="curso">Eliminar</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay cursos registrados actualmente.</p>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; 2025 Emplea40+. Todos los derechos reservados.</p>
</footer>
</body>
</html>
