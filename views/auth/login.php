<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Agenda Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            max-width: 400px;
            margin-top: 100px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            border-radius: 10px;
            padding: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container login-container">
        <div class="card p-4">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Bienvenido</h2>
                <p class="text-muted">Introduce tus datos para entrar</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger p-2 small"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="index.php?action=login" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="nombre@ejemplo.com" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label small fw-bold">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">Iniciar Sesión</button>
            </form>

            <div class="text-center">
                <p class="small">¿No tienes cuenta? <a href="index.php?action=register" class="text-decoration-none">Regístrate</a></p>
            </div>
        </div>
    </div>

</body>

</html>