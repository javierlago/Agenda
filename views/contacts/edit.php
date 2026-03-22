<?php 
    $pageTitle = "Edición de contacto - Agenda Pro";
    include __DIR__ . '/../layout/header.php'; 
?>

<div class="container mt-5">
    <h2>Editar Contacto</h2>
    <hr">

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): echo "<p>$error</p>"; endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=edit_contact&id=<?= $contact['id'] ?>" method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" 
                   value="<?= htmlspecialchars($contact['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Teléfono</label>
            <input type="text" name="phone" id="id" class="form-control" 
                   value="<?= htmlspecialchars($contact['phone'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" 
                   value="<?= htmlspecialchars($contact['email'] ?? '') ?>">
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Cambios</button>
        <a href="index.php?action=home" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>