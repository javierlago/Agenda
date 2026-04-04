<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Agenda Pro'; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .navbar { margin-bottom: 2rem; }
    </style>
</head>
<body>

<?php 
if (isset($_SESSION['user_id']) && (!isset($hideNavbar) || $hideNavbar === false)): 
?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">📔 Mi Agenda</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=home">Contactos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=profile">Mi Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="index.php?action=logout">Salir</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<?php endif; ?>

<?php
$successMessages = [
    '1'       => 'Contacto creado correctamente.',
    'updated' => 'Contacto actualizado correctamente.',
    'deleted' => 'Contacto eliminado correctamente.',
];
$errorMessages = [
    'notfound'    => 'El contacto no existe o no tienes permiso para accederlo.',
    'deletefailed' => 'No se pudo eliminar el contacto. Inténtalo de nuevo.',
];

$alertType = null;
$alertText = null;

if (isset($_GET['success']) && array_key_exists($_GET['success'], $successMessages)) {
    $alertType = 'success';
    $alertText = $successMessages[$_GET['success']];
} elseif (isset($_GET['error']) && array_key_exists($_GET['error'], $errorMessages)) {
    $alertType = 'danger';
    $alertText = $errorMessages[$_GET['error']];
}
?>

<?php if ($alertType): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= $alertType ?> alert-dismissible fade show" role="alert" id="flashAlert">
            <?= htmlspecialchars($alertText) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const alert = document.getElementById('flashAlert');
            if (alert) bootstrap.Alert.getOrCreateInstance(alert).close();
        }, 4000);
    </script>
<?php endif; ?>