<?php 
 
// routes/Routes.php 
 
// Incluimos el Controller de productos porque 
// las rutas deberán indicar qué método del Controller 
// se debe ejecutar según la solicitud recibida. 
require_once __DIR__ . "/../controllers/ProductoController.php"; 
 
// Obtenemos el método HTTP utilizado en la solicitud. 
// Puede ser GET, POST, PUT o DELETE. 
$metodo = $_SERVER["REQUEST_METHOD"]; 
 
// Obtenemos la URL que fue solicitada. 
// trim() elimina las barras "/" que aparecen 
// al principio y al final de la URL. 
$uri = trim($_SERVER["REQUEST_URI"], "/"); 
 
// Dividimos la URL en diferentes partes utilizando "/" 
// como separador.


// Por ejemplo: 
// api_proyecto/public/productos/3 
// 
// se convierte en: 
// ["api_proyecto", "public", "productos", "3"] 
$partes = explode("/", $uri); 
 
// Ejemplo esperado: 
// api_proyecto/public/productos 
// o 
// api_proyecto/public/productos/3 
 
// Obtenemos la parte anterior al último elemento de la URL. 
// En el ejemplo productos/3, obtenemos "productos". 
$recurso = $partes[count($partes) - 2] ?? null; 
 
// Obtenemos el último elemento de la URL. 
// En productos/3, obtenemos "3". 
// Si no existe, se utiliza null. 
$idOParametro = $partes[count($partes) - 1] ?? null; 
 
// Si idOParametro no es numérico, 
// significa que probablemente no tenemos un ID. 
// En ese caso, el último elemento corresponde al nombre 
// del recurso. 
if (!is_numeric($idOParametro)) { 
 
    // Guardamos el nombre del recurso. 
    $recurso = $idOParametro; 
 
    // Como no tenemos un ID numérico, 
    // establecemos el valor como null. 
    $idOParametro = null; 
 
}

// Comprobamos si el recurso solicitado 
// corresponde a "productos". 
if ($recurso === "productos") { 
 
    // Creamos un objeto del Controller de productos. 
    // A través de este objeto podremos ejecutar 
    // los métodos correspondientes. 
    $controller = new ProductoController(); 
 
    // Si el método HTTP es GET y no se recibió un ID, 
    // significa que se están solicitando todos los productos. 
    if ($metodo === "GET" && $idOParametro === null) { 
 
        // Ejecutamos el método listar() del Controller. 
        $controller->listar(); 
 
    // Si el método HTTP es GET y existe un ID, 
    // significa que se está solicitando un producto específico. 
    } elseif ($metodo === "GET") { 
 
        // Ejecutamos verUno() enviando el ID recibido. 
        $controller->verUno($idOParametro); 
 
    // Si el método HTTP es POST, 
    // significa que se quiere crear un nuevo producto.

     } elseif ($metodo === "POST") { 
 
        // Obtenemos los datos enviados por el cliente 
        // en el cuerpo de la solicitud. 
        // 
        // file_get_contents("php://input") obtiene 
        // el contenido enviado. 
        // 
        // json_decode(..., true) convierte el JSON recibido 
        // en un arreglo asociativo de PHP. 
        $datos = json_decode(file_get_contents("php://input"), true); 
 
        // Enviamos los datos al método crear() 
        // del Controller. 
        $controller->crear($datos); 
 
    // Si el método HTTP es PUT, 
    // significa que se quiere modificar un producto. 
    } elseif ($metodo === "PUT") { 
 
        // Obtenemos los datos enviados por el cliente 
        // y los convertimos de JSON a un arreglo de PHP. 
        $datos = json_decode(file_get_contents("php://input"), true); 
 
        // Ejecutamos el método editar() del Controller. 
        // Le enviamos el ID del producto y los nuevos datos. 
        $controller->editar($idOParametro, $datos); 
 
    // Si el método HTTP es DELETE, 
    // significa que se quiere eliminar un producto. 
    } elseif ($metodo === "DELETE") { 
 
        // Ejecutamos el método eliminar() 
        // enviando el ID del producto. 
        $controller->eliminar($idOParametro); 
 
    } 
 
} else { 
 
    // Si el recurso solicitado no coincide con "productos", 
    // enviamos un mensaje de error. 
    // 
    // El código 404 indica que la ruta o recurso 
    // no fue encontrado. 
    Response::json(["error" => "Ruta no válida"], 404); 
 
} 




 
// Dentro de routes/Routes.php 
 
// Función auxiliar reutilizable que comprueba si una solicitud 
// tiene un token JWT válido. 
// Si el token es válido, devuelve los datos que contiene. 
function usuarioAutenticado() { 
 
    // Obtiene los encabezados HTTP enviados en la solicitud. 
    // Entre ellos puede encontrarse Authorization. 
    $headers = getallheaders(); 
 
    // Busca el encabezado Authorization. 
    // Si no existe, guarda una cadena vacía en $auth. 
    $auth = $headers["Authorization"] ?? ""; 
 
 
    // Comprueba que el encabezado comience con "Bearer ". 
    // Bearer es el formato utilizado para enviar el token 
    // de autenticación en una solicitud HTTP. 
    if (!str_starts_with($auth, "Bearer ")) { 
 
        // Devuelve un mensaje de error en formato JSON. 
        // El código HTTP 401 indica que falta la autenticación. 
        Response::json(["error" => "Falta el token"], 401); 
 
        // Detiene la ejecución del programa para impedir 
        // que continúe con una solicitud no autenticada. 
        exit; 
 
    } 
 
 
    // Elimina los primeros 7 caracteres del encabezado, 
    // que corresponden al texto "Bearer ". 
    // De esta manera, obtiene solamente el token JWT. 
    $token = substr($auth, 7); 
 
    // Envía el token a Jwt::verificar() para comprobar 
    // si su firma es correcta y si no venció. 
    // Si es válido, devuelve los datos del token. 
    // Si no es válido, devuelve false. 
    $payload = Jwt::verificar($token); 
 
 
    // Comprueba si la verificación del token falló. 
    if (!$payload) { 
 
        // Devuelve un mensaje de error en formato JSON 
        // con el código HTTP 401. 
        Response::json(["error" => "Token inválido o vencido"], 401); 
 
        // Detiene la ejecución para impedir el acceso. 
        exit; 
 
    } 
 
 
    // Si el token es válido, devuelve los datos que contiene. 
    // Por ejemplo, el ID, el email y el rol del usuario. 
    return $payload; 
 
} 
 
 
// Uso de la función en una ruta protegida: 
 
// Comprueba si la solicitud corresponde al recurso usuarios 
// y si utiliza el método GET. 
if ($recurso === "usuarios" && $metodo === "GET") { 
 
    // Ejecuta la función que verifica el token. 
    // Si falta o es inválido, la función detiene la ejecución. 
    // Si es válido, devuelve los datos del usuario autenticado. 
    $usuario = usuarioAutenticado(); 
 
    // Crea un objeto UsuarioController y llama a su método perfil(). 
    // Le pasa los datos del usuario obtenidos del token 
    // para que pueda buscar su perfil en la base de datos. 
    (new UsuarioController())->perfil($usuario); 
     
}
 
?>

