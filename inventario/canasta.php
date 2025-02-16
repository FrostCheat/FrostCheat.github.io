<?php
session_start();

$archivoProductos = "productos.json";
$archivoRegistro = "registro.json";

$productos = json_decode(file_get_contents($archivoProductos), true);
$registroVentas = file_exists($archivoRegistro) ? json_decode(file_get_contents($archivoRegistro), true) : [];

if (!isset($_SESSION['canasta'])) {
    $_SESSION['canasta'] = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['agregar'])) {
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
            echo "<script>alert('Cantidad inválida o stock insuficiente.'); window.location.href='canasta.html';</script>";
            exit;
        }
    }

    if (isset($_POST['eliminar'])) {
        $producto = $_POST['eliminar'];
        unset($_SESSION['canasta'][$producto]);
    }

    if (isset($_POST['confirmar'])) {
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

            echo "<script>alert('Venta registrada. Total: $$totalVenta. Cambio: $$cambio'); window.location.href='canasta.html';</script>";
        } else {
            echo "<script>alert('El billete ingresado es insuficiente.'); window.location.href='canasta.html';</script>";
        }
    }
}

// Cargar opciones de productos para canasta.html
$options = "";
foreach ($productos as $nombre => $producto) {
    $options .= "<option value='" . htmlspecialchars($nombre) . "'>" . htmlspecialchars($nombre) . " (Stock: " . $producto['cantidad'] . ")</option>";
}

// Cargar productos en la tabla
$rows = "";
$totalGeneral = 0;
foreach ($_SESSION['canasta'] as $nombre => $item) {
    $total = $item['cantidad'] * $item['precio_unitario'];
    $totalGeneral += $total;
    $rows .= "<tr>
                <td>" . htmlspecialchars($nombre) . "</td>
                <td class='text-center'>" . $item['cantidad'] . "</td>
                <td class='text-center'>$" . number_format($item['precio_unitario'], 2) . "</td>
                <td class='text-center'>$" . number_format($total, 2) . "</td>
                <td class='text-center'>
                    <form method='post' action='canasta.php' style='display:inline;'>
                        <button type='submit' name='eliminar' value='" . htmlspecialchars($nombre) . "' class='btn btn-danger btn-sm'>❌</button>
                    </form>
                </td>
            </tr>";
}

$rows .= "<tr class='table-warning'>
            <td colspan='3' class='text-end'><strong>Total Venta:</strong></td>
            <td class='text-center'><strong>$" . number_format($totalGeneral, 2) . "</strong></td>
            <td></td>
          </tr>";

echo str_replace(
    ["<!-- Opciones de productos se llenarán desde PHP -->", "<!-- Filas de productos se llenarán desde PHP -->", "value=\"0\""],
    [$options, $rows, "value=\"$totalGeneral\""],
    file_get_contents("canasta.html")
);
?>
