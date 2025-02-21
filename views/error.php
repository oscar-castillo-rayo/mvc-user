<!-- views/error.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-danger">
            <h4>Ha ocurrido un error</h4>
            <p><?php echo isset($errorMessage) ? $errorMessage : 'Error desconocido'; ?></p>
            <a href="index.php" class="btn btn-primary mt-3">Volver al inicio</a>
        </div>
    </div>
</body>
</html>