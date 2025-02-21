<?php
require_once __DIR__ . '/../config/database.php';

class UserModel
{
    private $conn;
    private $table = "users";

    // User properties
    public $id;
    public $name;
    public $email;
    public $created_at;
    
    // Error handling property
    private $last_error;

    public function __construct()
    {
        try {
            $this->conn = Database::getConnection();
            if (!$this->conn) {
                throw new Exception("No se pudo establecer conexión con la base de datos");
            }
        } catch (PDOException $e) {
            $this->last_error = "Error de conexión: " . $e->getMessage();
            error_log("Database connection error: " . $e->getMessage());
            // Opcionalmente, relanzar la excepción si es crítica
            // throw new Exception("Error de conexión con la base de datos");
        } catch (Exception $e) {
            $this->last_error = "Error: " . $e->getMessage();
            error_log("General error in constructor: " . $e->getMessage());
        }
    }

    // Get the last error
    public function getLastError() 
    {
        return $this->last_error;
    }

    // Get all users
    public function getAll()
    {
        try {
            // Verificar que la conexión esté activa
            if (!$this->conn) {
                throw new Exception("La conexión a la base de datos no está disponible");
            }
            
            $query = "SELECT id, name, email, created_at FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt;
        } catch (PDOException $e) {
            $this->last_error = "Error al obtener usuarios: " . $e->getMessage();
            error_log("Database error in getAll(): " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->last_error = "Error general: " . $e->getMessage();
            error_log("General error in getAll(): " . $e->getMessage());
            return false;
        }
    }

    // Get User by id
    public function getById($id)
    {
        try {
            // Validar ID
            if (!is_numeric($id) || $id <= 0) {
                throw new InvalidArgumentException("El ID debe ser un número positivo");
            }
            
            $query = "SELECT id, name, email, created_at FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->email = $row['email'];
                $this->created_at = $row['created_at'];
                return true;
            }

            $this->last_error = "Usuario no encontrado";
            return false;
        } catch (PDOException $e) {
            $this->last_error = "Error al buscar usuario: " . $e->getMessage();
            error_log("Database error in getById(): " . $e->getMessage());
            return false;
        } catch (InvalidArgumentException $e) {
            $this->last_error = $e->getMessage();
            error_log("Validation error in getById(): " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->last_error = "Error general: " . $e->getMessage();
            error_log("General error in getById(): " . $e->getMessage());
            return false;
        }
    }

    // Create a new user
    public function create()
    {
        try {
            // Validar datos requeridos
            if (empty($this->name) || empty($this->email)) {
                throw new InvalidArgumentException("Nombre y email son obligatorios");
            }
            
            // Validar formato de email
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Formato de email inválido");
            }
            
            $query = "INSERT INTO " . $this->table . " (name, email) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);

            // Evitar SQL injection
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->email = htmlspecialchars(strip_tags($this->email));

            // Vincular parámetros
            $stmt->bindParam(1, $this->name);
            $stmt->bindParam(2, $this->email);

            if ($stmt->execute()) {
                // Obtener el ID insertado
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            
            $this->last_error = "Error al ejecutar la consulta de creación";
            return false;
        } catch (PDOException $e) {
            // Manejar error específico de email duplicado
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $this->last_error = "El email ya está registrado";
            } else {
                $this->last_error = "Error en la base de datos: " . $e->getMessage();
            }
            error_log("Database error in create(): " . $e->getMessage());
            return false;
        } catch (InvalidArgumentException $e) {
            $this->last_error = $e->getMessage();
            error_log("Validation error in create(): " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->last_error = "Error general: " . $e->getMessage();
            error_log("General error in create(): " . $e->getMessage());
            return false;
        }
    }

    // Update user
    public function update()
    {
        try {
            // Validar ID
            if (empty($this->id) || !is_numeric($this->id) || $this->id <= 0) {
                throw new InvalidArgumentException("ID de usuario inválido");
            }
            
            // Validar datos requeridos
            if (empty($this->name) || empty($this->email)) {
                throw new InvalidArgumentException("Nombre y email son obligatorios");
            }
            
            // Validar formato de email
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Formato de email inválido");
            }
            
            $query = "UPDATE " . $this->table . " SET name = ?, email = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);

            // Evitar SQL injection
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Vincular parámetros
            $stmt->bindParam(1, $this->name);
            $stmt->bindParam(2, $this->email);
            $stmt->bindParam(3, $this->id);

            if ($stmt->execute()) {
                // Verificar si se actualizó algún registro
                if ($stmt->rowCount() > 0) {
                    return true;
                } else {
                    $this->last_error = "No se encontró el usuario o no se realizaron cambios";
                    return false;
                }
            }
            
            $this->last_error = "Error al ejecutar la consulta de actualización";
            return false;
        } catch (PDOException $e) {
            // Manejar error específico de email duplicado
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $this->last_error = "El email ya está registrado por otro usuario";
            } else {
                $this->last_error = "Error en la base de datos: " . $e->getMessage();
            }
            error_log("Database error in update(): " . $e->getMessage());
            return false;
        } catch (InvalidArgumentException $e) {
            $this->last_error = $e->getMessage();
            error_log("Validation error in update(): " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->last_error = "Error general: " . $e->getMessage();
            error_log("General error in update(): " . $e->getMessage());
            return false;
        }
    }

    // Delete a user
    public function delete()
    {
        try {
            // Validar ID
            if (empty($this->id) || !is_numeric($this->id) || $this->id <= 0) {
                throw new InvalidArgumentException("ID de usuario inválido");
            }
            
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);

            // Sanitizar input
            $this->id = htmlspecialchars(strip_tags($this->id));

            $stmt->bindParam(1, $this->id);

            if ($stmt->execute()) {
                // Verificar si se eliminó algún registro
                if ($stmt->rowCount() > 0) {
                    return true;
                } else {
                    $this->last_error = "No se encontró el usuario a eliminar";
                    return false;
                }
            }
            
            $this->last_error = "Error al ejecutar la consulta de eliminación";
            return false;
        } catch (PDOException $e) {
            $this->last_error = "Error en la base de datos: " . $e->getMessage();
            error_log("Database error in delete(): " . $e->getMessage());
            return false;
        } catch (InvalidArgumentException $e) {
            $this->last_error = $e->getMessage();
            error_log("Validation error in delete(): " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->last_error = "Error general: " . $e->getMessage();
            error_log("General error in delete(): " . $e->getMessage());
            return false;
        }
    }
}