<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$host = "207.244.235.97"; 
$user = "u6_mAJYbTlz80"; 
$password = "0!tW=2krH!q3BB=vVt3MmEas"; 
$dbname = "s6_inv"; 

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'listar':
        listarProductos();
        break;
    case 'agregar':
        agregarProducto();
        break;
    case 'editar':
        editarProducto();
        break;
    case 'eliminar':
        eliminarProducto();
        break;
    case 'venta':
        registrarVenta();
        break;
    case 'ventas':
        listarVentas();
        break;
    default:
        echo json_encode(["error" => "Acción no válida"]);
}

$conn->close();

function listarProductos() {
    global $conn;
    $result = $conn->query("SELECT * FROM productos");
    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
    echo json_encode($productos);
}

function agregarProducto() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['nombre'], $data['cantidad'], $data['precio'])) {
        echo json_encode(["error" => "Datos inválidos"]);
        return;
    }

    $nombre = $conn->real_escape_string($data['nombre']);
    $cantidad = intval($data['cantidad']);
    $precio = floatval($data['precio']);

    $stmt = $conn->prepare("INSERT INTO productos (nombre, cantidad, precio) VALUES (?, ?, ?)");
    $stmt->bind_param("sid", $nombre, $cantidad, $precio);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Producto agregado correctamente"]);
    } else {
        echo json_encode(["error" => "Error al agregar el producto"]);
    }
}

function editarProducto() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'], $data['cantidad'], $data['precio'])) {
        echo json_encode(["error" => "Datos inválidos"]);
        return;
    }

    $id = intval($data['id']);
    $cantidad = intval($data['cantidad']);
    $precio = floatval($data['precio']);

    $stmt = $conn->prepare("UPDATE productos SET cantidad = ?, precio = ? WHERE id = ?");
    $stmt->bind_param("idi", $cantidad, $precio, $id);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Producto actualizado"]);
    } else {
        echo json_encode(["error" => "Error al actualizar"]);
    }
}

function eliminarProducto() {
    global $conn;
    
    if (!isset($_GET['id'])) {
        echo json_encode(["error" => "ID inválido"]);
        return;
    }

    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Producto eliminado correctamente"]);
    } else {
        echo json_encode(["error" => "Error al eliminar el producto"]);
    }
}

function registrarVenta() {
    global $conn;
    $ventas = json_decode(file_get_contents("php://input"), true);

    if (!$ventas || !is_array($ventas)) {
        echo json_encode(["error" => "Datos inválidos"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO ventas (producto, cantidad, total) VALUES (?, ?, ?)");
    foreach ($ventas as $venta) {
        $stmt->bind_param("sid", $venta['nombre'], $venta['cantidad'], $venta['total']);
        $stmt->execute();
    }

    echo json_encode(["mensaje" => "Venta registrada"]);
}

function listarVentas() {
    global $conn;
    $result = $conn->query("SELECT * FROM ventas ORDER BY fecha DESC");
    $ventas = [];
    while ($row = $result->fetch_assoc()) {
        $ventas[] = $row;
    }
    echo json_encode($ventas);
}
?>
