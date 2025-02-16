<?php
$archivo = "productos.json";
$productos = json_decode(file_get_contents($archivo), true);

// Verificar si hay productos
if (!$productos) {
    echo "<script>alert('No hay productos en el inventario.'); window.location.href='index.php';</script>";
    exit;
}

// Si se selecciona un producto, lo cargamos
$productoSeleccionado = key($productos); // Producto por defecto
if (isset($_POST['producto'])) {
    $productoSeleccionado = $_POST['producto'];
}

$nombre = $productoSeleccionado;
$cantidad = $productos[$productoSeleccionado]['cantidad'];
$precio = $productos[$productoSeleccionado]['precio'];

// Si se envía el formulario, actualizamos el JSON
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    $nuevoNombre = trim($_POST['nombre']);
    $nuevaCantidad = intval($_POST['cantidad']);
    $nuevoPrecio = floatval($_POST['precio']);

    if (!empty($nuevoNombre) && $nuevaCantidad >= 0 && $nuevoPrecio > 0) {
        // Si el nombre cambió y no existe ya en la lista
        if ($nuevoNombre !== $nombre && array_key_exists($nuevoNombre, $productos)) {
            echo "<script>alert('Ya existe un producto con ese nombre.');</script>";
        } else {
            // Actualizar el producto
            unset($productos[$nombre]); // Borrar el antiguo
            $productos[$nuevoNombre] = ["cantidad" => $nuevaCantidad, "precio" => $nuevoPrecio];

            // Guardar cambios
            file_put_contents($archivo, json_encode($productos, JSON_PRETTY_PRINT));

            echo "<script>alert('Producto actualizado con éxito.'); window.location.href='editar.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Por favor ingresa valores válidos.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Producto - Tienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function cargarDatos() {
            var productos = <?= json_encode($productos) ?>;
            var seleccionado = document.getElementById('producto').value;
            document.getElementById('nombre').value = seleccionado;
            document.getElementById('cantidad').value = productos[seleccionado]['cantidad'];
            document.getElementById('precio').value = productos[seleccionado]['precio'];
        }
    </script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 500px;
            margin-top: 50px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            width: 100%;
        }
        h1 {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Editar Producto</h1>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Seleccionar Producto</label>
                <select name="producto" id="producto" class="form-control" onchange="cargarDatos()">
                    <?php foreach ($productos as $key => $value): ?>
                        <option value="<?= htmlspecialchars($key) ?>" <?= $key == $productoSeleccionado ? 'selected' : '' ?>>
                            <?= htmlspecialchars($key) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre del Producto</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required value="<?= htmlspecialchars($nombre) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Cantidad</label>
                <input type="number" name="cantidad" id="cantidad" class="form-control" required min="0" value="<?= htmlspecialchars($cantidad) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Precio</label>
                <input type="number" name="precio" id="precio" class="form-control" required min="1" value="<?= htmlspecialchars($precio) ?>">
            </div>
            <button type="submit" name="guardar" class="btn btn-primary">Guardar Cambios</button>
        </form>
        <br>
        <a href="index.php" class="btn btn-secondary w-100">🔙 Volver al Inventario</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>cargarDatos();</script>
</body>
</html>
