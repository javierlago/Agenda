
<div class="container mt-5">
    <h2>Editar Contacto</h2>
    <hr>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php foreach ($error as $e): echo "<p>$e</p>"; endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=edit_contact&id=<?= $contact['id'] ?>" method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" name="name" id="name" class="form-control"
                   value="<?= htmlspecialchars($_POST['name'] ?? $contact['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Teléfono</label>
            <input type="text" name="phone" id="phone" class="form-control"
                   value="<?= htmlspecialchars($_POST['phone'] ?? $contact['phone'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control"
                   value="<?= htmlspecialchars($_POST['email'] ?? $contact['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea name="description" id="description" class="form-control"><?= htmlspecialchars($_POST['description'] ?? $contact['description'] ?? '') ?></textarea>   

        </div>

        <button type="submit" class="btn btn-primary">Actualizar Cambios</button>
        <a href="index.php?action=home" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

