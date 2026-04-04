
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Mis Contactos</h2>
        <a href="index.php?action=add_contact" class="btn btn-primary shadow-sm">+ Nuevo Contacto</a>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="index.php" class="d-flex gap-2 flex-wrap">
                <input type="hidden" name="action" value="home">
                <input type="text" name="search" class="form-control"
                    placeholder="Buscar por nombre, teléfono o email..."
                    value="<?= htmlspecialchars($search) ?>">
                <select name="sort" class="form-select" style="max-width: 200px;">
                    <option value="name_asc"  <?= $sort === 'name_asc'  ? 'selected' : '' ?>>Nombre A-Z</option>
                    <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Nombre Z-A</option>
                    <option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : '' ?>>Más recientes</option>
                    <option value="date_asc"  <?= $sort === 'date_asc'  ? 'selected' : '' ?>>Más antiguos</option>
                </select>
                <button type="submit" class="btn btn-primary">Buscar</button>
                <?php if ($search !== ''): ?>
                    <a href="index.php?action=home&sort=<?= urlencode($sort) ?>" class="btn btn-outline-secondary">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="row" id="contactList">
        <?php if (!empty($contacts)): ?>
            <?php
                $from = $offset + 1;
                $to   = min($offset + $limit, $totalContacts);
            ?>
            <div class="col-12 mb-3 text-muted small">
                Mostrando <?= $from ?>-<?= $to ?> de <?= $totalContacts ?> contacto<?= $totalContacts !== 1 ? 's' : '' ?>
            </div>
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
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">

                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="index.php?action=home&page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>">Anterior</a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="index.php?action=home&page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="index.php?action=home&page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>">Siguiente</a>
                    </li>

                </ul>
            </nav>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">No se encontraron contactos.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

