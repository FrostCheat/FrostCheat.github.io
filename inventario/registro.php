<?php
$archivoRegistro = "registro.json";
$ventas = file_exists($archivoRegistro) ? json_decode(file_get_contents($archivoRegistro), true) : [];

// Cargar HTML y reemplazar datos dinámicamente
$html = file_get_contents("registro.html");
$html = str_replace("<!-- JSON de ventas se llenará desde PHP -->", json_encode($ventas), $html);

echo $html;
?>
