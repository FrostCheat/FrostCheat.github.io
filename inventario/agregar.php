<?php
$archivo = "productos.json";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $cantidad = intval($_POST['cantidad']);
    $precio = floatval($_POST['precio']);

    if (!empty($nombre) && $cantidad >= 0 && $precio > 0) {
        // Leer el archivo JSON
        $productos = json_decode(file_get_contents($archivo), true) ?? [];

        // Agregar el nuevo producto
        $productos[$nombre] = ["cantidad" => $cantidad, "precio" => $precio];

        // Guardar en JSON
        file_put_contents($archivo, json_encode($productos, JSON_PRETTY_PRINT));

        echo "<script>alert('Producto agregado con éxito.'); window.location.href='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Todos los campos deben ser válidos.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agregar Producto - Tienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <h1>📦 Agregar Producto</h1>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Nombre del Producto</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Cantidad</label>
                <input type="number" name="cantidad" class="form-control" required min="0">
            </div>
            <div class="mb-3">
                <label class="form-label">Precio</label>
                <input type="number" name="precio" class="form-control" required min="1">
            </div>
            <button type="submit" class="btn btn-primary">Agregar Producto</button>
        </form>
        <br>
        <a href="index.php" class="btn btn-secondary w-100">🔙 Volver al Inventario</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
