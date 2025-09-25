<?php

/**
 * Este archivo sirve como punto de entrada para la versión 1 de la API.
 * Se define aquí el código específico de la versión 1 y se incluyen los
 * endpoints definidos en api.php bajo el prefijo v1.
 * 
 * Para mantener la estructura por versiones, no modificamos este archivo directamente
 * sino que vamos al archivo api.php y modificamos las rutas dentro del grupo 'v1'.
 * 
 * Esto nos permite mantener un archivo por versión de API, facilitando el mantenimiento
 * y evolución de la API con el tiempo.
 */

// Indicador que este archivo está siendo cargado correctamente
if (!defined('KODIGO_API_V1_LOADED')) {
    define('KODIGO_API_V1_LOADED', true);
}

// Las rutas específicas de la versión 1 están definidas en api.php dentro del grupo prefix('v1')
