
<style>
    .register-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 0;
    }

    .card-register {
        width: 100%;
        max-width: 500px;
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="register-wrapper">
    <div class="card card-register p-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">Nueva Cuenta</h2>
            <p class="text-muted small">Regístrate para empezar a organizar tu agenda</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="index.php?action=register" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Nombre Completo</label>
                <input type="text" name="name" class="form-control" placeholder="Ej: Juan Pérez" required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold">Confirmar Contraseña</label>
                <input type="password" name="password_confirm" class="form-control" placeholder="Repite tu contraseña" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Crear mi cuenta</button>
        </form>

        <div class="text-center mt-4">
            <p class="small mb-0">¿Ya tienes cuenta? <a href="index.php?action=login" class="text-decoration-none fw-bold">Inicia sesión aquí</a></p>
        </div>
    </div>
</div>

