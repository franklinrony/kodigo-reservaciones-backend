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

REM Extraer token de usuario (requiere jq o powershell)
echo Extrayendo token de usuario...
powershell -Command "$json = Get-Content -Raw login_user_response.json | ConvertFrom-Json; $token = $json.access_token; $token" > token.txt
set /p USER_TOKEN=<token.txt
echo Token de usuario obtenido.
echo.

REM ==== PRUEBAS DEL SISTEMA KANBAN ====

echo 7. Creando un nuevo tablero...
curl -s -X POST %BASE_URL%/boards ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer %USER_TOKEN%" ^
  -d "{\"title\":\"Mi Primer Tablero\",\"description\":\"Un tablero para organizar mis tareas\",\"is_public\":false}" > board_create_response.json

echo Respuesta guardada en board_create_response.json
echo.

REM Extraer ID del tablero
echo Extrayendo ID del tablero...
powershell -Command "$json = Get-Content -Raw board_create_response.json | ConvertFrom-Json; $boardId = $json.data.id; $boardId" > board_id.txt
set /p BOARD_ID=<board_id.txt
echo ID del tablero obtenido: %BOARD_ID%
echo.

echo 8. Obteniendo los tableros del usuario...
curl -s -X GET %BASE_URL%/boards ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer %USER_TOKEN%" > boards_list_response.json

echo Respuesta guardada en boards_list_response.json
echo.

echo 9. Creando una lista en el tablero...
curl -s -X POST %BASE_URL%/boards/%BOARD_ID%/lists ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer %USER_TOKEN%" ^
  -d "{\"title\":\"Por hacer\",\"position\":0}" > list_create_response.json

echo Respuesta guardada en list_create_response.json
echo.

REM Extraer ID de la lista
echo Extrayendo ID de la lista...
powershell -Command "$json = Get-Content -Raw list_create_response.json | ConvertFrom-Json; $listId = $json.data.id; $listId" > list_id.txt
set /p LIST_ID=<list_id.txt
echo ID de la lista obtenido: %LIST_ID%
echo.

echo 10. Creando una tarjeta en la lista...
curl -s -X POST %BASE_URL%/lists/%LIST_ID%/cards ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer %USER_TOKEN%" ^
  -d "{\"title\":\"Mi primera tarea\",\"description\":\"Descripción de la tarea\",\"due_date\":\"2025-10-15\"}" > card_create_response.json

echo Respuesta guardada en card_create_response.json
echo.

REM Extraer ID de la tarjeta
echo Extrayendo ID de la tarjeta...
powershell -Command "$json = Get-Content -Raw card_create_response.json | ConvertFrom-Json; $cardId = $json.data.id; $cardId" > card_id.txt
set /p CARD_ID=<card_id.txt
echo ID de la tarjeta obtenido: %CARD_ID%
echo.

echo 11. Creando una etiqueta en el tablero...
curl -s -X POST %BASE_URL%/boards/%BOARD_ID%/labels ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer %USER_TOKEN%" ^
  -d "{\"name\":\"Urgente\",\"color\":\"#FF0000\"}" > label_create_response.json

echo Respuesta guardada en label_create_response.json
echo.

echo 12. Obteniendo información detallada del tablero...
curl -s -X GET %BASE_URL%/boards/%BOARD_ID% ^
  -H "Accept: application/json" ^
  -H "Authorization: Bearer %USER_TOKEN%" > board_detail_response.json

echo Respuesta guardada en board_detail_response.json
echo.

echo Pruebas completadas. Revisa los archivos de respuesta para ver los resultados.
echo Para importar la colección en Postman, utiliza el archivo postman_collection.json

del token.txt
del board_id.txt
del list_id.txt
del card_id.txt

endlocal