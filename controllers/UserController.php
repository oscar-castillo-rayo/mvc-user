<?php
require_once __DIR__ . '/../models/UserModel.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // Show users
    public function index()
    {
        try {
            $result = $this->userModel->getAll();
            $users = $result->fetchAll(PDO::FETCH_ASSOC);

            require_once __DIR__ . '/../views/users/list.php';
        } catch (Exception $e) {
            echo "Error al obtener la lista de usuarios: " . $e->getMessage();
        }
    }

    public function edit($id)
    {
        try {
            if ($this->userModel->getById($id)) {
                $user = array(
                    'id' => $this->userModel->id,
                    'name' => $this->userModel->name,
                    'email' => $this->userModel->email
                );

                require_once __DIR__ . '/../views/users/edit.php';
            } else {
                echo "Usuario no encontrado";
            }
        } catch (Exception $e) {
            echo "Error al obtener el usuario: " . $e->getMessage();
        }
    }

    public function update()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->userModel->id = $_POST['id'];
                $this->userModel->name = $_POST['name'];
                $this->userModel->email = $_POST['email'];

                if ($this->userModel->update()) {
                    header('Location: index.php?action=users');
                    exit;
                } else {
                    echo "Error al actualizar el usuario";
                }
            }
        } catch (Exception $e) {
            echo "Error al actualizar el usuario: " . $e->getMessage();
        }
    }

    public function create()
    {
        try {
            require_once __DIR__ . '/../views/users/create.php';
        } catch (Exception $e) {
            echo "Error al cargar la vista de creación: " . $e->getMessage();
        }
    }

    public function store()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->userModel->name = $_POST['name'];
                $this->userModel->email = $_POST['email'];

                if ($this->userModel->create()) {
                    header('Location: index.php?action=users');
                    exit;
                } else {
                    echo "Error al crear el usuario";
                }
            }
        } catch (Exception $e) {
            echo "Error al guardar el usuario: " . $e->getMessage();
        }
    }

    // Delete user
    public function delete($id)
    {
        try {
            $this->userModel->id = $id;

            if ($this->userModel->delete()) {
                header('Location: index.php?action=users');
                exit;
            } else {
                echo "Error al eliminar el usuario";
            }
        } catch (Exception $e) {
            echo "Error al eliminar el usuario: " . $e->getMessage();
        }
    }
}
