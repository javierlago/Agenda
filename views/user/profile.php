
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Mi Perfil</h3>
                </div>
                <div class="card-body">
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error) echo "<p class='mb-0'>$error</p>"; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted">Nombre Completo:</div>
                        <div class="col-sm-8 fw-bold"><?= htmlspecialchars($user['username']) ?></div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted">Email:</div>
                        <div class="col-sm-8 fw-bold"><?= htmlspecialchars($user['email']) ?></div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted">Miembro desde:</div>
                        <div class="col-sm-8"><?= date('d/m/Y', strtotime($user['created_at'])) ?></div>
                    </div>

                    <hr>

                    <h4 class="mb-3">Actualizar datos</h4>
                    <form action="index.php?action=profile" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nuevo Nombre</label>
                            <input type="text" name="username" class="form-control" 
                                   value="<?= htmlspecialchars($user['username']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nuevo Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($user['email']) ?>">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">Guardar Cambios</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

