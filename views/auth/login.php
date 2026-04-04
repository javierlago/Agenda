
<style>
    .login-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-login {
        width: 100%;
        max-width: 400px;
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="login-wrapper">
    <div class="card card-login p-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Bienvenido</h2>
            <p class="text-muted small">Introduce tus credenciales</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger p-2 small text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success p-2 small text-center">
                ¡Registro completado! Ya puedes iniciar sesión.
            </div>
        <?php endif; ?>
        <form action="index.php?action=login" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="usuario@ejemplo.com" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Entrar</button>
        </form>

        <div class="text-center mt-4">
            <p class="small mb-0">¿No tienes cuenta? <a href="index.php?action=register" class="text-decoration-none fw-bold">Regístrate</a></p>
        </div>
    </div>
</div>

