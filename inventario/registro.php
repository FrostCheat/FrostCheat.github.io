<?php
$archivoRegistro = "registro.json";
$ventas = file_exists($archivoRegistro) ? json_decode(file_get_contents($archivoRegistro), true) : [];

$totalProductos = 0;
$totalDinero = 0;

// Calcular totales
foreach ($ventas as $venta) {
    $totalProductos += $venta['cantidad'];
    $totalDinero += $venta['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="text-center">📜 Registro de Ventas</h1>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ventas)): ?>
                    <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td><?= htmlspecialchars($venta['fecha']) ?></td>
                        <td><?= htmlspecialchars($venta['producto']) ?></td>
                        <td class="text-center"><?= $venta['cantidad'] ?></td>
                        <td class="text-center">$<?= number_format($venta['precio_unitario'], 2) ?></td>
                        <td class="text-center">$<?= number_format($venta['total'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No hay ventas registradas.</td></tr>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($ventas)): ?>
            <tfoot>
                <tr class="table-warning">
                    <td colspan="2" class="text-end"><strong>Total Productos Vendidos:</strong></td>
                    <td class="text-center"><strong><?= $totalProductos ?></strong></td>
                    <td class="text-end"><strong>Total Dinero Generado:</strong></td>
                    <td class="text-center"><strong>$<?= number_format($totalDinero, 2) ?></strong></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>

        <a href="index.php" class="btn btn-secondary w-100">🔙 Volver al Inventario</a>
    </div>
</body>
</html>
