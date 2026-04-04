
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold">✨ Añadir Nuevo Contacto</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger p-2 small text-center">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="index.php?action=add_contact" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Ej: Juan Pérez"
                                value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Teléfono</label>
                                <input type="text" name="phone" class="form-control" placeholder="600 000 000"
                                    value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Descripción / Notas</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Compañero de trabajo, familia, etc..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?action=home" class="btn btn-light me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Guardar Contacto</button>
                        </div>
                    </form>
                </div>
            </div>
            <p class="text-center text-muted mt-3 small">Los campos marcados con <span class="text-danger">*</span> son obligatorios.</p>
        </div>
    </div>
</div>

