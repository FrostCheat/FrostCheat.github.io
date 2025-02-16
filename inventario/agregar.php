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

        echo "<script>alert('Producto agregado con éxito.'); window.location.href='agregar.html';</script>";
        exit;
    } else {
        echo "<script>alert('Todos los campos deben ser válidos.'); window.location.href='agregar.html';</script>";
    }
}
?>
