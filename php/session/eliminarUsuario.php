<?php
header('Content-Type: application/json');
require_once("../conexion.php");

try {
    $conexion = new Conexion();
    $conectar = $conexion->conectar();

    if (!isset($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID no recibido']);
        exit;
    }
    $id = intval($_POST['id']);

    $conectar->beginTransaction();

    // Obtener tipo de usuario
    $stmtTipo = $conectar->prepare("SELECT tipo_usuario FROM usuarios WHERE id = ?");
    $stmtTipo->execute([$id]);
    $tipo = $stmtTipo->fetchColumn();

    if (!$tipo) {
        throw new Exception("Usuario no encontrado");
    }

    if ($tipo === 'trabajador') {
        // BORRAR PERFIL de trabajador
        $stmtBorrarPerfil = $conectar->prepare("DELETE FROM perfiles WHERE usuario_id = ?");
        $stmtBorrarPerfil->execute([$id]);

        // BORRAR USUARIO
        $stmtBorrarUsuario = $conectar->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmtBorrarUsuario->execute([$id]);

    } elseif ($tipo === 'empresa') {
        // Obtener id empresa
        $stmtEmpresa = $conectar->prepare("SELECT id FROM empresas WHERE usuario_id = ?");
        $stmtEmpresa->execute([$id]);
        $empresaId = $stmtEmpresa->fetchColumn();

        if ($empresaId) {
            // Comprobar si tiene ofertas
            $stmtOfertas = $conectar->prepare("SELECT COUNT(*) FROM ofertas_empleo WHERE empresa_id = ?");
            $stmtOfertas->execute([$empresaId]);
            $numOfertas = $stmtOfertas->fetchColumn();

            if ($numOfertas > 0) {
                // Borrar ofertas
                $stmtBorrarOfertas = $conectar->prepare("DELETE FROM ofertas_empleo WHERE empresa_id = ?");
                $stmtBorrarOfertas->execute([$empresaId]);
            }

            // Borrar perfil (asumiendo que el perfil también existe para empresa)
            $stmtBorrarPerfil = $conectar->prepare("DELETE FROM perfiles WHERE usuario_id = ?");
            $stmtBorrarPerfil->execute([$id]);

            // Borrar empresa
            $stmtBorrarEmpresa = $conectar->prepare("DELETE FROM empresas WHERE id = ?");
            $stmtBorrarEmpresa->execute([$empresaId]);
        }

        // Borrar usuario
        $stmtBorrarUsuario = $conectar->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmtBorrarUsuario->execute([$id]);
    } else {
        // Si hay otros tipos de usuario, manejar aquí o lanzar error
        throw new Exception("Tipo de usuario desconocido");
    }

    $conectar->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($conectar && $conectar->inTransaction()) {
        $conectar->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()]);
}
