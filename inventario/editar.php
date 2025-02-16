<?php
$archivo = "productos.json";
$productos = json_decode(file_get_contents($archivo), true);

// Verificar si hay productos
if (!$productos) {
    echo "<script>alert('No hay productos en el inventario.'); window.location.href='index.php';</script>";
    exit;
}

// Producto por defecto
$productoSeleccionado = key($productos);
if (isset($_POST['producto'])) {
    $productoSeleccionado = $_POST['producto'];
}

// Datos del producto seleccionado
$nombre = $productoSeleccionado;
$cantidad = $productos[$productoSeleccionado]['cantidad'];
$precio = $productos[$productoSeleccionado]['precio'];

// Guardar cambios
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    $nuevoNombre = trim($_POST['nombre']);
    $nuevaCantidad = intval($_POST['cantidad']);
    $nuevoPrecio = floatval($_POST['precio']);

    if (!empty($nuevoNombre) && $nuevaCantidad >= 0 && $nuevoPrecio > 0) {
        if ($nuevoNombre !== $nombre && array_key_exists($nuevoNombre, $productos)) {
            echo "<script>alert('Ya existe un producto con ese nombre.'); window.location.href='editar.html';</script>";
        } else {
            unset($productos[$nombre]); // Eliminar el producto antiguo
            $productos[$nuevoNombre] = ["cantidad" => $nuevaCantidad, "precio" => $nuevoPrecio];

            file_put_contents($archivo, json_encode($productos, JSON_PRETTY_PRINT));

            echo "<script>alert('Producto actualizado con éxito.'); window.location.href='editar.html';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Por favor ingresa valores válidos.'); window.location.href='editar.html';</script>";
    }
}

// Generar las opciones de productos
$options = "";
foreach ($productos as $key => $value) {
    $selected = ($key == $productoSeleccionado) ? "selected" : "";
    $options .= "<option value='" . htmlspecialchars($key) . "' $selected>" . htmlspecialchars($key) . "</option>";
}

// Inyectar datos en `editar.html`
$html = file_get_contents("editar.html");
$html = str_replace("<!-- Opciones de productos se llenarán desde PHP -->", $options, $html);
$html = str_replace("<!-- JSON de productos se llenará desde PHP -->", json_encode($productos), $html);

echo $html;
?>
