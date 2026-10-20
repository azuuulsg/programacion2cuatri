<?php 
 
// controllers/ProductoController.php 
 
// Incluimos el Model Producto porque el Controller 
// necesita utilizar sus métodos para trabajar con los productos. 
require_once __DIR__ . 
"/../models/Producto.php"; 
 
// Incluimos la clase Response para poder enviar las respuestas de la API en 
//formato JSON. 
require_once __DIR__ . 
"/../utils/Response.php"; 
 
class ProductoController { 
 
    // Variable donde se almacena un objeto del Model Producto. 
    // A través de esta variable podemos utilizar 
    // los métodos definidos en el Model. 
    private $modelo; 
 
    // Constructor de la clase. 
    // Se ejecuta automáticamente cuando se crea un objeto ProductoController. 
    public function __construct() { 
 
        // Creamos un objeto de la clase Producto. 
        // Producto hereda de Model y por eso puede utilizar 
        // la conexión con la base de datos. 
        $this->modelo = new Producto(); 
 
    } 
 
    // GET /productos 
    // 
    // Endpoint utilizado para obtener todos los productos. 
    public function listar() { 
 
        // Le pedimos al Model que obtenga todos los productos 
        // mediante el método getAll(). 
        // 
        // Luego enviamos los datos al cliente en formato JSON. 
        // El código 200 indica que la solicitud fue exitosa. 
        Response::json($this->modelo->getAll(), 200); 
 
    } 
 
    // GET /productos/{id} 
    // 
    // Endpoint utilizado para obtener un producto específico. 
    // Recibe el ID del producto como parámetro. 
    public function verUno($id) { 
 
        // Le pedimos al Model que busque el producto 
        // utilizando el ID recibido. 
        $producto = $this->modelo->getById($id); 
 
        // Comprobamos si el producto fue encontrado. 
        // 
        // Si existe, lo enviamos con código 200. 
        // Si no existe, enviamos un mensaje de error 
        // con código 404 (recurso no encontrado). 
        $producto ? Response::json($producto, 200): Response::json(["error" 
=> "Producto no encontrado"], 404); 
 
    } 
 
    // POST /productos 
    // 
    // Endpoint utilizado para crear un nuevo producto. 
    // Esta operación estará disponible solamente para administradores. 
    public function crear($datos) { 
 
        // Enviamos al Model los datos necesarios 
        // para crear el nuevo producto. 
        $this->modelo->create( 
 
            $datos["nombre"], 
$datos["descripcion"], 
 
            $datos["precio"], 
$datos["imagen"] 
 
        ); 
 
        // Informamos al cliente que el producto 
        // fue creado correctamente. 
        // 
        // El código 201 significa "creado". 
        Response::json(["mensaje" 
=> "Producto creado"], 201); 
 
    } 
 
    // PUT /productos/{id} 
    // 
    // Endpoint utilizado para modificar un producto existente. 
    // Recibe el ID del producto y los nuevos datos. 
    public function editar($id, $datos) { 
 
        // Le pedimos al Model que actualice el producto 
        // utilizando el ID y los nuevos datos.
        $this->modelo->update($id, 
$datos); 
 
        // Informamos al cliente que el producto 
        // fue actualizado correctamente. 
        Response::json(["mensaje" 
=> "Producto actualizado"], 200); 
 
    } 
 
    // DELETE /productos/{id} 
    // 
    // Endpoint utilizado para eliminar un producto. 
    // Recibe como parámetro el ID del producto. 
    public function eliminar($id) { 
 
        // Le pedimos al Model que elimine 
        // el producto indicado. 
        $this->modelo->delete($id); 
 
        // Informamos al cliente que el producto 
        // fue eliminado correctamente. 
        Response::json(["mensaje" 
=> "Producto eliminado"], 200); 
 
    } 
 
  } 
?>