<?php
include '../config/config.php';
session_start();

// Verificar si ya hay usuarios admin
$query = "SELECT COUNT(*) as total FROM usuarios WHERE es_admin = 1";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$hayAdmin = ($row['total'] > 0);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $hayAdmin ? 'Login' : 'Crear Administrador'; ?></title>
    <style>
        body {
            background: linear-gradient(135deg, #f8bbd0 0%, #f48fb1 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: #fff0f6;
            border-radius: 18px;
            box-shadow: 0 6px 32px rgba(244,143,177,0.18);
            padding: 40px 32px 32px 32px;
            max-width: 350px;
            width: 100%;
            text-align: center;
        }
        .logo {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 16px;
            border: 3px solid #f06292;
        }
        h2 {
            color: #ad1457;
            margin-bottom: 24px;
        }
        input {
            width: 90%;
            padding: 10px 12px;
            margin: 10px 0;
            border: 1px solid #f8bbd0;
            border-radius: 8px;
            font-size: 16px;
        }
        button, .btn {
            background: linear-gradient(90deg, #f06292 0%, #ec407a 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            margin-top: 16px;
        }
        .error { color: #d32f2f; }
        .info { color: #388e3c; }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="../img/vamates.jpg" alt="Logo" class="logo">
        
        <?php if ($hayAdmin): ?>
            <h2>Iniciar Sesión</h2>
            <form action="procesar_login.php" method="POST">
                <input type="text" name="nombre" placeholder="Usuario" required>
                <input type="password" name="contraseña" placeholder="Contraseña" required>
                <button type="submit">Ingresar</button>
            </form>
            <?php if (isset($_GET['error'])): ?>
                <p class="error">Usuario o contraseña incorrectos</p>
            <?php endif; ?>
        <?php else: ?>
            <h2>Crear Administrador</h2>
            <p class="info">No hay administradores registrados. Crea el primero:</p>
            <form action="crear_admin.php" method="POST">
                <input type="text" name="nombre" placeholder="Usuario" required>
                <input type="password" name="contraseña" placeholder="Contraseña" required>
                <input type="password" name="confirmar_contraseña" placeholder="Confirmar Contraseña" required>
                <button type="submit">Crear Administrador</button>
            </form>
            <?php if (isset($_GET['error'])): ?>
                <p class="error">
                    <?php 
                    switch($_GET['error']) {
                        case 'campos_vacios': echo 'Todos los campos son requeridos'; break;
                        case 'contraseñas_no_coinciden': echo 'Las contraseñas no coinciden'; break;
                        case 'contraseña_corta': echo 'La contraseña debe tener al menos 6 caracteres'; break;
                        default: echo 'Error al crear administrador';
                    }
                    ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>