<?php
session_start();

$archivoProductos = "productos.json";
$archivoRegistro = "registro.json";

$productos = json_decode(file_get_contents($archivoProductos), true);
$registroVentas = file_exists($archivoRegistro) ? json_decode(file_get_contents($archivoRegistro), true) : [];

// Inicializar la canasta si no existe
if (!isset($_SESSION['canasta'])) {
    $_SESSION['canasta'] = [];
}

// Agregar producto a la canasta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar'])) {
    $producto = $_POST['producto'];
    $cantidad = intval($_POST['cantidad']);

    if ($productos[$producto]['cantidad'] >= $cantidad && $cantidad > 0) {
        if (isset($_SESSION['canasta'][$producto])) {
            $_SESSION['canasta'][$producto]['cantidad'] += $cantidad;
        } else {
            $_SESSION['canasta'][$producto] = [
                "cantidad" => $cantidad,
                "precio_unitario" => $productos[$producto]['precio']
            ];
        }
    } else {
        echo "<script>alert('Cantidad inválida o stock insuficiente.');</script>";
    }
}

// Eliminar un producto de la canasta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar'])) {
    $producto = $_POST['eliminar'];
    unset($_SESSION['canasta'][$producto]);
}

// Confirmar venta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmar'])) {
    $billete = floatval($_POST['billete']);
    $totalVenta = floatval($_POST['total_venta']);
    $cambio = $billete - $totalVenta;

    if ($billete >= $totalVenta) {
        foreach ($_SESSION['canasta'] as $producto => $datos) {
            $productos[$producto]['cantidad'] -= $datos['cantidad'];

            $registroVentas[] = [
                "fecha" => date("Y-m-d H:i:s"),
                "producto" => $producto,
                "cantidad" => $datos['cantidad'],
                "precio_unitario" => $datos['precio_unitario'],
                "total" => $datos['cantidad'] * $datos['precio_unitario']
            ];
        }

        file_put_contents($archivoProductos, json_encode($productos, JSON_PRETTY_PRINT));
        file_put_contents($archivoRegistro, json_encode($registroVentas, JSON_PRETTY_PRINT));

        $_SESSION['canasta'] = [];

        echo "<script>alert('Venta registrada. Total: $$totalVenta. Cambio: $$cambio'); window.location.href='canasta.php';</script>";
    } else {
        echo "<script>alert('El billete ingresado es insuficiente.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Canasta de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function calcularCambio() {
            let totalVenta = parseFloat(document.getElementById('totalVenta').value) || 0;
            let billete = parseFloat(document.getElementById('billete').value) || 0;
            let cambio = billete - totalVenta;

            let mensaje = cambio >= 0 ? `💰 Cambio: $${cambio.toFixed(2)}` : "⚠️ Dinero insuficiente";
            document.getElementById('cambio').innerText = mensaje;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">🛒 Canasta de Compras</h1>

        <!-- Formulario para agregar productos -->
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Seleccionar Producto</label>
                <select name="producto" class="form-control">
                    <?php foreach ($productos as $nombre => $producto): ?>
                        <option value="<?= htmlspecialchars($nombre) ?>"><?= htmlspecialchars($nombre) ?> (Stock: <?= $producto['cantidad'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Cantidad</label>
                <input type="number" name="cantidad" class="form-control" required min="1">
            </div>
            <button type="submit" name="agregar" class="btn btn-primary">➕ Agregar al Carrito</button>
        </form>

        <hr>

        <!-- Tabla con los productos en la canasta -->
        <h2>📋 Resumen de Canasta</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalGeneral = 0;
                if (!empty($_SESSION['canasta'])):
                    foreach ($_SESSION['canasta'] as $nombre => $item):
                        $total = $item['cantidad'] * $item['precio_unitario'];
                        $totalGeneral += $total;
                ?>
                <tr>
                    <td><?= htmlspecialchars($nombre) ?></td>
                    <td class="text-center"><?= $item['cantidad'] ?></td>
                    <td class="text-center">$<?= number_format($item['precio_unitario'], 2) ?></td>
                    <td class="text-center">$<?= number_format($total, 2) ?></td>
                    <td class="text-center">
                        <form method="post" style="display:inline;">
                            <button type="submit" name="eliminar" value="<?= htmlspecialchars($nombre) ?>" class="btn btn-danger btn-sm">❌</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr class="table-warning">
                    <td colspan="3" class="text-end"><strong>Total Venta:</strong></td>
                    <td class="text-center"><strong>$<?= number_format($totalGeneral, 2) ?></strong></td>
                    <td></td>
                </tr>
                <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No hay productos en la canasta.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Ingreso del billete y cálculo del cambio antes de confirmar la venta -->
        <?php if ($totalGeneral > 0): ?>
        <form method="post">
            <input type="hidden" name="total_venta" id="totalVenta" value="<?= $totalGeneral ?>">
            <div class="mb-3">
                <label class="form-label">💵 Ingresar Monto Pagado</label>
                <input type="number" name="billete" id="billete" class="form-control" required min="<?= $totalGeneral ?>" step="0.01" oninput="calcularCambio()">
            </div>
            <div id="cambio" class="alert alert-info text-center">💰 Cambio: $0.00</div>
            <button type="submit" name="confirmar" class="btn btn-success w-100">✅ Confirmar Venta</button>
        </form>
        <?php endif; ?>

        <br>
        <a href="index.php" class="btn btn-secondary w-100">🔙 Volver al Inventario</a>
    </div>
</body>
</html>
