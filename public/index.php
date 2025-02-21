<?php
// Archivo principal que actúa como Front Controller

// Incluir controladores
require_once '../controllers/UserController.php';

// Instanciar controladores
$userController = new UserController();

// Enrutamiento básico
$action = isset($_GET['action']) ? $_GET['action'] : 'users';

try {
    switch ($action) {
            // Usuarios
        case 'users':
            $userController->index();
            break;
        case 'createUser':
            $userController->create();
            break;
        case 'storeUser':
            $userController->store();
            break;
        case 'editUser':
            $id = isset($_GET['id']) ? $_GET['id'] : die('ID requerido');
            $userController->edit($id);
            break;
        case 'updateUser':
            $userController->update();
            break;
        case 'deleteUser':
            $id = isset($_GET['id']) ? $_GET['id'] : die('ID requerido');
            $userController->delete($id);
            break;
        default:
            // Página no encontrada
            http_response_code(404);
            echo "Página no encontrada";
            break;
    }
} catch (Exception $e) {
    // Manejo global de excepciones
    http_response_code(500);
    $errorMessage = $e->getMessage();
    include_once __DIR__ . '/../views/error.php';
}
