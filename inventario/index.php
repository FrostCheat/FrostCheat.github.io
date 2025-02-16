<?php
$productos = json_decode(file_get_contents("productos.json"), true);

// Calcular totales
$totalProductos = 0;
$totalPrecio = 0;
foreach ($productos as $producto) {
    $totalProductos += $producto["cantidad"];
    $totalPrecio += $producto['cantidad'] * $producto['precio'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventario de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
        .table th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }
        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }
        .btn-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">📦 Inventario de Productos</h1>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Precio Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $nombre => $producto): ?>
                <tr>
                    <td><?= htmlspecialchars($nombre) ?></td>
                    <td class="text-center"><?= htmlspecialchars($producto['cantidad']) ?></td>
                    <td class="text-center">$<?= number_format($producto['precio'], 2) ?></td>
                    <td class="text-center">$<?= number_format($producto['cantidad'] * $producto['precio'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td>Total Productos:</td>
                    <td class="text-center"><?= $totalProductos ?></td>
                    <td>Total Precio:</td>
                    <td class="text-center">$<?= number_format($totalPrecio, 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="btn-container">
            <a href="agregar.php" class="btn btn-primary">➕ Agregar Producto</a>
            <a href="editar.php" class="btn btn-warning">✏️ Editar Producto</a>
            <a href="canasta.php" class="btn btn-success">🛒 Ir a la Canasta</a>
            <a href="registro.php" class="btn btn-dark">📜 Ver Registro de Ventas</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
