@echo off
REM Script para probar los endpoints de la API de Kanban

setlocal EnableDelayedExpansion

echo Iniciando pruebas de API Kanban...
echo ==================================
echo.

REM Iniciar el servidor en segundo plano
start "Laravel Server" php artisan serve

REM Esperar a que el servidor inicie
timeout /t 3 > nul

set BASE_URL=http://localhost:8000/api/v1

REM Crear una variable para almacenar el token
set TOKEN=

echo 1. Intentando registrar un nuevo usuario...
curl -s -X POST %BASE_URL%/auth/register ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"name\":\"Nuevo Usuario\",\"email\":\"nuevo@ejemplo.com\",\"password\":\"password123\",\"password_confirmation\":\"password123\"}" > registro_response.json

echo Respuesta guardada en registro_response.json
echo.

echo 2. Intentando iniciar sesión con usuario administrador...
curl -s -X POST %BASE_URL%/auth/login ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"email\":\"test@example.com\",\"password\":\"password\"}" > login_admin_response.json

echo Respuesta guardada en login_admin_response.json
echo.

REM Extraer token (requiere jq o powershell)
echo Extrayendo token de administrador...
powershell -Command "$json = Get-Content -Raw login_admin_response.json | ConvertFrom-Json; $token = $json.access_token; $token" > token.txt
set /p ADMIN_TOKEN=<token.txt
echo Token de administrador obtenido.
echo.

echo 3. Obteniendo información del usuario admin...
curl -s -X GET %BASE_URL%/auth/me ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer %ADMIN_TOKEN%" > admin_info_response.json

echo Respuesta guardada en admin_info_response.json
echo.

echo 4. Accediendo al dashboard de admin...
curl -s -X GET %BASE_URL%/admin/dashboard ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer %ADMIN_TOKEN%" > admin_dashboard_response.json

echo Respuesta guardada en admin_dashboard_response.json
echo.

echo 5. Cerrando sesión de administrador...
curl -s -X POST %BASE_URL%/auth/logout ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer %ADMIN_TOKEN%" > logout_response.json

echo Respuesta guardada en logout_response.json
echo.

echo 6. Intentando iniciar sesión con usuario común...
curl -s -X POST %BASE_URL%/auth/login ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -d "{\"email\":\"nuevo@ejemplo.com\",\"password\":\"password123\"}" > login_user_response.json

echo Respuesta guardada en login_user_response.json
echo.

echo Pruebas completadas. Revisa los archivos de respuesta para ver los resultados.
echo Para importar la colección en Postman, utiliza el archivo postman_collection.json

del token.txt

endlocal