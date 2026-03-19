<?php 
    $pageTitle = "Mis Contactos";
    include __DIR__ . '/../layout/header.php'; 
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Mis Contactos</h2>
        <a href="index.php?action=add_contact" class="btn btn-primary">+ Nuevo Contacto</a>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <input type="text" class="form-control" placeholder="Buscar por nombre o teléfono...">
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Juan Pérez</h5>
                    <p class="card-text text-muted mb-1 small">📞 600 123 456</p>
                    <p class="card-text text-muted small">📧 juan@correo.com</p>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="#" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <a href="#" class="btn btn-sm btn-outline-danger">Eliminar</a>
                    </div>
                </div>
            </div>
        </div>
        </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>