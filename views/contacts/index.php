<?php 
    $pageTitle = "Mis Contactos";
    include __DIR__ . '/../layout/header.php'; 
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Mis Contactos</h2>
        <a href="index.php?action=add_contact" class="btn btn-primary shadow-sm">+ Nuevo Contacto</a>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre o teléfono...">
        </div>
    </div>

    <div class="row" id="contactList">
        <?php if (!empty($contacts)): ?>
            <?php foreach ($contacts as $contact): ?>
                <div class="col-md-4 mb-4 contact-item">
                    <div class="card h-100 shadow-sm border-0 position-relative">
                        <?php if (isset($contact['is_example'])): ?>
                            <span class="position-absolute top-0 start-0 badge rounded-pill bg-warning text-dark m-2" style="z-index: 1;">
                                Example
                            </span>
                        <?php endif; ?>

                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-weight: bold;">
                                    <?php echo strtoupper(substr($contact['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0 fw-bold"><?php echo htmlspecialchars($contact['name']); ?></h5>
                                    <small class="text-muted"><?php echo htmlspecialchars($contact['phone'] ?? 'Sin teléfono'); ?></small>
                                </div>
                            </div>

                            <p class="card-text mb-1 small">
                                <strong>Email:</strong> <?php echo htmlspecialchars($contact['email'] ?? 'N/A'); ?>
                            </p>
                            
                            <?php if (!empty($contact['description'])): ?>
                                <p class="card-text small text-truncate" title="<?php echo htmlspecialchars($contact['description']); ?>">
                                    <strong>Nota:</strong> <?php echo htmlspecialchars($contact['description']); ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!isset($contact['is_example'])): ?>
                                <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top">
                                    <a href="index.php?action=edit_contact&id=<?php echo $contact['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        Editar
                                    </a>
                                    <a href="index.php?action=delete_contact&id=<?php echo $contact['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('¿Estás seguro de que quieres eliminar a este contacto?');" title="Eliminar">
                                        Eliminar
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">No se encontraron contactos.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>