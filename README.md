## 1. Introducción

Este proyecto consume las APIs de la NASA (DONKI) y expone varios endpoints en Laravel para:
- Listar instrumentos usados.
- Listar IDs de actividades.
- Calcular porcentajes de uso de instrumentos.
- Calcular porcentajes de uso por actividad para un instrumento específico.

## 2. Requerimientos

- **PHP** 8.3.17.  
- **Composer** 2.8.5.  
- **Extensión fileinfo** habilitada en PHP (para evitar errores al instalar dependencias).  
- **Extensión cURL** habilitada en PHP (normalmente incluida por defecto).  
- **Laravel** 11.42.1

## 3. Tecnologías y librerías utilizadas

- **Laravel Framework**: `11.42.1`.  
- **HTTP Client** 

## 4. Instalación

1. **Clonar el repositorio**  
   ```bash
   git clone https://github.com/usuario/laravel-api-nasa.git
   ```
   Ingresa al directorio del proyecto:
   ```bash
   cd laravel-api-nasa
   ```

2. **Instalar dependencias**  
   ```bash
   composer install
   ```
   Esto descargará e instalará todas las librerías que el proyecto necesita.

3. **Configurar variables de entorno**  
   - Copia el archivo de ejemplo `.env.example` a `.env`:  
     ```bash
     cp .env.example .env
     ```
   - Abre el archivo `.env` y ajusta la configuración, por ejemplo:  
     ```env
     APP_NAME=Laravel
     APP_ENV=local
     APP_KEY= # (se generará más adelante)
     APP_DEBUG=true
     APP_URL=http://localhost

     NASA_API_KEY=TU_API_KEY_DE_LA_NASA
     NASA_BASE_URL=https://api.nasa.gov/DONKI
     ```
   - Genera la clave de aplicación:  
     ```bash
     php artisan key:generate
     ```

4. **(Opcional) Configurar el archivo cacert.pem**  
   Si te da error de SSL en Windows, descarga el certificado de autoridades desde [https://curl.se/docs/caextract.html](https://curl.se/docs/caextract.html), configura `curl.cainfo` y `openssl.cafile` en tu `php.ini`.

## 5. Uso / Ejecución local

Para levantar el servidor de desarrollo de Laravel, ejecuta:

```bash
php artisan serve
```

Por defecto, se abrirá en [http://127.0.0.1:8000](http://127.0.0.1:8000).  
Si usas un servidor local (XAMPP, Laragon, etc.), configura el virtual host según tus necesidades.

## 6. Endpoints principales

Ejemplo de endpoints (pueden variar según tu configuración de rutas en `routes/api.php`):

1. **Listar instrumentos**  
   `GET /api/instruments`

2. **Listar IDs de actividades**  
   `GET /api/activityIds`

3. **Porcentaje de uso de cada instrumento**  
   `GET /api/instrument_use`

4. **Porcentaje de uso por actividad para un instrumento específico**  
   `POST /api/instrument_activity`  
   Cuerpo / Body en cada petición (JSON):  
   ```json
   {
     "instrument": "MODEL: SWMF"
   }
   ```

## 7. Colección de Postman

- Dentro de la carpeta raíz del proyecto, encontrarás una carpeta llamada Postman Collection, dentro estará el archivo `Desafio_Laravel_NASA.postman_collection.json`.
- Importa ese archivo en Postman para probar directamente cada endpoint.
- Recuerda primero iniciar el proyecto con `php artisan serve`, para que los endpoint estén disponibles localmente.

## 9. Notas adicionales

- Si necesitas aumentar el tiempo máximo de ejecución, puedes usar `set_time_limit(0)` o configurar `max_execution_time` en `php.ini`, ya que, se consulta a varias URLs.
- Si tu rango de fechas en las consultas a la NASA es muy amplio, puede tardar en responder o generar errores de timeout (cURL error 28). Considera acotar el rango.
---