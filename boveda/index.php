<?php
session_start();
require_once 'conn.php';

$msg_ok = '';
$msg_err = '';
$pass_descifrada = null;
$id_descifrado = null;

// check si ya hay master pass
$q = $mysqli->query("SELECT * FROM configuracion LIMIT 1");
$hay_config = ($q && $q->num_rows > 0);
$cfg = $hay_config ? $q->fetch_assoc() : null;

$accion = $_POST['accion'] ?? '';

// guardar master pass (solo primera vez)
if ($accion == 'configurarMaster') {
    $mp = $_POST['masterPassword'] ?? '';
    $mp2 = $_POST['masterPasswordConfirm'] ?? '';

    if (strlen($mp) < 6) {
        $msg_err = "La contraseña maestra debe tener al menos 6 caracteres.";
    } elseif ($mp != $mp2) {
        $msg_err = "Las contraseñas no coinciden.";
    } else {
        $salt = bin2hex(random_bytes(16));
        $hash = password_hash($mp . $salt, PASSWORD_BCRYPT);

        $st = $mysqli->prepare("INSERT INTO configuracion (masterHash, masterSalt) VALUES (?, ?)");
        $st->bind_param("ss", $hash, $salt);
        if ($st->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $msg_err = "No se pudo guardar la configuración.";
        }
    }
}

// guardar credencial nueva
if ($accion == 'guardarCredencial') {
    $servicio = trim($_POST['servicio'] ?? '');
    $usuario  = trim($_POST['usuario'] ?? '');
    $pass     = $_POST['passwordPlana'] ?? '';
    $mp       = $_POST['masterPasswordVerificar'] ?? '';

    if ($servicio == '' || $usuario == '' || $pass == '' || $mp == '') {
        $msg_err = "Faltan campos.";
    } elseif (!$hay_config) {
        $msg_err = "Primero configura la contraseña maestra.";
    } elseif (!password_verify($mp . $cfg['masterSalt'], $cfg['masterHash'])) {
        $msg_err = "Contraseña maestra incorrecta.";
    } else {
        // cifrado AES-256-CBC, clave derivada con PBKDF2
        $salt = bin2hex(random_bytes(16));
        $key  = hash_pbkdf2("sha256", $mp, $salt, 100000, 32, true);
        $iv   = random_bytes(16);

        $enc = openssl_encrypt($pass, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $pass_enc = base64_encode($enc);
        $iv_hex = bin2hex($iv);

        $st = $mysqli->prepare("INSERT INTO credenciales (servicio, usuario, passwordCifrada, vectorInicializacion, saltDerivacion) VALUES (?, ?, ?, ?, ?)");
        $st->bind_param("sssss", $servicio, $usuario, $pass_enc, $iv_hex, $salt);

        if ($st->execute()) {
            $msg_ok = "Credencial guardada.";
        } else {
            $msg_err = "Error al guardar en la BD.";
        }
    }
}

// descifrar credencial
if ($accion == 'descifrarCredencial') {
    $cid = intval($_POST['credencialId'] ?? 0);
    $mp  = $_POST['masterPasswordDescifrar'] ?? '';

    if ($cid <= 0 || $mp == '') {
        $msg_err = "Ingresa la contraseña maestra.";
    } else {
        $st = $mysqli->prepare("SELECT * FROM credenciales WHERE id = ?");
        $st->bind_param("i", $cid);
        $st->execute();
        $res = $st->get_result();

        if ($row = $res->fetch_assoc()) {
            $key = hash_pbkdf2("sha256", $mp, $row['saltDerivacion'], 100000, 32, true);
            $iv  = hex2bin($row['vectorInicializacion']);
            $enc = base64_decode($row['passwordCifrada']);

            $dec = openssl_decrypt($enc, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

            if (!password_verify($mp . $cfg['masterSalt'], $cfg['masterHash']) || $dec === false) {
                $msg_err = "Contraseña maestra incorrecta.";
            } else {
                $pass_descifrada = $dec;
                $id_descifrado = $cid;
                $msg_ok = "Credencial descifrada.";
            }
        } else {
            $msg_err = "Credencial no encontrada.";
        }
    }
}

$lista = $mysqli->query("SELECT id, servicio, usuario, passwordCifrada, vectorInicializacion, saltDerivacion, fechaCreacion FROM credenciales ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bóveda de Contraseñas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .codigo-cifrado { font-family: monospace; font-size: 0.85rem; word-break: break-all; }
    </style>
</head>
<body class="container py-4">

    <h2 class="text-center mb-4">Bóveda de Contraseñas</h2>

    <?php if ($msg_ok): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($msg_ok) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($msg_err): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($msg_err) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!$hay_config): ?>
        <div class="card mb-4 border-warning">
            <div class="card-header bg-warning text-dark fw-bold">Configuración Inicial</div>
            <div class="card-body">
                <p>Define la <strong>contraseña maestra</strong> que protegerá toda la bóveda.</p>
                <form method="POST">
                    <input type="hidden" name="accion" value="configurarMaster">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Contraseña maestra:</label>
                            <input type="password" name="masterPassword" class="form-control" required minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar:</label>
                            <input type="password" name="masterPasswordConfirm" class="form-control" required minlength="6">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning mt-3">Guardar</button>
                </form>
            </div>
        </div>
    <?php else: ?>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white fw-bold">Nueva Credencial</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="accion" value="guardarCredencial">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Servicio:</label>
                            <input type="text" name="servicio" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Usuario:</label>
                            <input type="text" name="usuario" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Contraseña:</label>
                            <input type="password" name="passwordPlana" class="form-control" required>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label fw-bold">Contraseña maestra:</label>
                            <input type="password" name="masterPasswordVerificar" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Guardar</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white fw-bold">Credenciales</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Servicio</th>
                                <th>Usuario</th>
                                <th>Password (cifrado)</th>
                                <th>IV / Salt</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($lista && $lista->num_rows > 0): ?>
                                <?php while ($row = $lista->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><strong><?= htmlspecialchars($row['servicio']) ?></strong></td>
                                        <td><?= htmlspecialchars($row['usuario']) ?></td>
                                        <td class="codigo-cifrado text-danger"><?= htmlspecialchars($row['passwordCifrada']) ?></td>
                                        <td class="codigo-cifrado text-muted">
                                            IV: <?= htmlspecialchars(substr($row['vectorInicializacion'], 0, 10)) ?>...<br>
                                            Salt: <?= htmlspecialchars(substr($row['saltDerivacion'], 0, 10)) ?>...
                                        </td>
                                        <td>
                                            <?php if ($id_descifrado === intval($row['id'])): ?>
                                                <div class="alert alert-success p-2 mb-0">
                                                    <code><?= htmlspecialchars($pass_descifrada) ?></code>
                                                </div>
                                            <?php else: ?>
                                                <form method="POST" class="d-flex gap-2">
                                                    <input type="hidden" name="accion" value="descifrarCredencial">
                                                    <input type="hidden" name="credencialId" value="<?= $row['id'] ?>">
                                                    <input type="password" name="masterPasswordDescifrar" class="form-control form-control-sm" required>
                                                    <button type="submit" class="btn btn-sm btn-outline-success">Ver</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Sin credenciales guardadas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>