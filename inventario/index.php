<?php
$archivo = "productos.json";
$productos = json_decode(file_get_contents($archivo), true) ?? [];

// Calcular totales
$totalProductos = 0;
$totalPrecio = 0;
foreach ($productos as $producto) {
    $totalProductos += $producto["cantidad"];
    $totalPrecio += $producto['cantidad'] * $producto['precio'];
}

// Cargar HTML y reemplazar datos dinámicamente
$html = file_get_contents("index.html");
$html = str_replace("<!-- JSON de productos se llenará desde PHP -->", json_encode($productos), $html);

echo $html;
?>
