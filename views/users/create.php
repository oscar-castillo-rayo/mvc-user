<!-- views/users/create.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nuevo Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Crear Nuevo Usuario</h1>
        
        <form action="index.php?action=storeUser" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="index.php?action=users" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>