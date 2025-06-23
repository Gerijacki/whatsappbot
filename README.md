# WhatsApp Bot - Sistema de Envío de Mensajes WhatsApp

## 📋 Descripción

WhatsApp Bot es una aplicación web desarrollada en Laravel que permite enviar mensajes de WhatsApp a través de la API oficial de Meta (Facebook). El sistema incluye un panel de administración con Filament, gestión de clientes de WhatsApp, colas de procesamiento y una API REST completa.

## 🚀 Tecnologías Utilizadas

### Backend
- **PHP 8.1+** - Lenguaje de programación principal
- **Laravel 10.x** - Framework PHP
- **Laravel Jetstream** - Autenticación y gestión de usuarios
- **Laravel Sanctum** - Autenticación API
- **Laravel Filament 3.x** - Panel de administración
- **Laravel Telescope** - Debugging y monitoreo
- **Laravel Queue** - Sistema de colas para procesamiento asíncrono
- **MySQL 8.0** - Base de datos principal
- **Redis** - Cache y colas (opcional)

### Frontend
- **Tailwind CSS** - Framework CSS
- **Alpine.js** - JavaScript reactivo
- **Vite** - Build tool
- **Livewire 3.x** - Componentes dinámicos

### DevOps & Herramientas
- **Docker** - Containerización
- **Composer** - Gestión de dependencias PHP
- **NPM** - Gestión de dependencias JavaScript
- **Git** - Control de versiones

## 📦 Instalación

### Requisitos Previos
- PHP 8.1 o superior
- Composer
- Node.js 18+ y NPM
- MySQL 8.0
- Redis (opcional, para colas)

### Instalación Local

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd whatsappbot
```

2. **Instalar dependencias PHP**
```bash
composer install
```

3. **Instalar dependencias JavaScript**
```bash
npm install
npm run build
```

4. **Configurar variables de entorno**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configurar la base de datos en `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=whatsappbot
DB_USERNAME=root
DB_PASSWORD=your_password
```

6. **Ejecutar migraciones**
```bash
php artisan migrate
```

7. **Ejecutar seeders**
```bash
php artisan db:seed
```

8. **Configurar usuario administrador**
```bash
php artisan db:seed --class=AdminUserSeeder
```

9. **Iniciar el servidor de desarrollo**
```bash
php artisan serve
```

### Instalación con Docker

1. **Configurar variables de entorno**
```bash
cp .env.example .env
```

2. **Configurar docker-compose**
```bash
cp docker-compose-example.yml docker-compose.yml
```

3. **Crear red de Docker**
```bash
docker network create app_whatsapp_bot
```

4. **Ejecutar con Docker Compose**
```bash
docker-compose up -d
```

5. **Instalar dependencias dentro del contenedor**
```bash
docker-compose exec app_whatsapp_bot composer install
docker-compose exec app_whatsapp_bot npm install
docker-compose exec app_whatsapp_bot npm run build
```

6. **Ejecutar migraciones**
```bash
docker-compose exec app_whatsapp_bot php artisan migrate
docker-compose exec app_whatsapp_bot php artisan db:seed
```

## ⚙️ Configuración

### Variables de Entorno Importantes

```env
# Aplicación
APP_NAME="WhatsApp Bot"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=whatsappbot
DB_USERNAME=root
DB_PASSWORD=your_password

# Colas
QUEUE_CONNECTION=database
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Usuario administrador
ADMIN_NAME=Admin
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=password

# WhatsApp API (configurar según tus credenciales)
WHATSAPP_API_VERSION=v19.0
```

### Configuración de WhatsApp Business API

1. **Crear una aplicación en Meta for Developers**
2. **Configurar WhatsApp Business API**
3. **Obtener credenciales:**
   - Phone Number ID
   - Business Account ID
   - Access Token

### Configuración de Colas

El sistema utiliza colas para procesar mensajes de forma asíncrona:

```bash
# Procesar colas
php artisan queue:work

# Procesar colas con reintentos
php artisan queue:work --tries=3 --backoff=15

# Monitorear colas fallidas
php artisan queue:failed
```

## 🔧 Funcionamiento del Sistema

### Arquitectura

El sistema sigue una arquitectura basada en eventos y colas:

1. **API Endpoint** → Recibe solicitud de envío de mensaje
2. **Validación** → Valida datos con `SendMessageRequest`
3. **Creación de Mensaje** → Guarda en base de datos con estado "pending"
4. **Evento MessageQueued** → Dispara el evento
5. **Listener DispatchMessageJob** → Encola el job de procesamiento
6. **ProcessMessageJob** → Procesa el mensaje con WhatsApp API
7. **WhatsAppService** → Envía mensaje a Meta API
8. **Actualización** → Actualiza estado del mensaje

### Tipos de Mensajes Soportados

1. **Text** - Mensajes de texto simples
2. **Template** - Mensajes de plantilla con parámetros
3. **Interactive** - Mensajes interactivos (botones, listas)

### Estados de Mensajes

- `pending` - Mensaje en cola
- `sent` - Mensaje enviado exitosamente
- `failed` - Mensaje fallido
- `retrying` - Mensaje en reintento

## 📡 API REST

### Autenticación

La API utiliza Laravel Sanctum para autenticación. Todas las rutas requieren un token Bearer.

### Endpoints

#### Base URL
```
http://localhost:8000/api/v1
```

#### 1. Obtener Mensajes
```http
GET /messages
Authorization: Bearer {token}
```

**Respuesta:**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "whatsapp_client_id": 1,
    "type": "text",
    "phone_number": "34000000000",
    "text": "Hola mundo",
    "status": "sent",
    "attempts": 1,
    "created_at": "2025-01-20T10:00:00.000000Z",
    "updated_at": "2025-01-20T10:00:05.000000Z"
  }
]
```

#### 2. Enviar Mensaje
```http
POST /messages
Authorization: Bearer {token}
Content-Type: application/json
```

**Body para mensaje de texto:**
```json
{
  "whatsapp_client_id": 1,
  "type": "text",
  "phone_number": "34000000000",
  "text": "Hola desde WhatsApp Bot"
}
```

**Body para mensaje de plantilla:**
```json
{
  "whatsapp_client_id": 1,
  "type": "template",
  "phone_number": "34000000000",
  "template_name": "hello_world",
  "language_code": "en_US",
  "parameters": ["John", "Doe"]
}
```

**Body para mensaje interactivo:**
```json
{
  "whatsapp_client_id": 1,
  "type": "interactive",
  "phone_number": "34000000000",
  "interactive_content": {
    "type": "button",
    "body": {
      "text": "Selecciona una opción"
    },
    "action": {
      "buttons": [
        {
          "type": "reply",
          "reply": {
            "id": "btn_1",
            "title": "Opción 1"
          }
        }
      ]
    }
  }
}
```

**Respuesta:**
```json
{
  "status": "queued"
}
```

### Códigos de Error

- `400` - Parámetros inválidos
- `401` - No autenticado
- `422` - Validación fallida
- `429` - Demasiadas solicitudes
- `500` - Error interno del servidor

## 🎛️ Panel de Administración (Filament)

### Acceso
```
http://localhost:8000/admin
```

### Credenciales por defecto
- **Email:** admin@example.com
- **Password:** password

### Funcionalidades

#### Gestión de Clientes WhatsApp
- Crear, editar y eliminar clientes de WhatsApp
- Configurar credenciales de API
- Asociar clientes a usuarios

#### Gestión de Mensajes
- Ver todos los mensajes enviados
- Filtrar por estado (pending, sent, failed)
- Ver detalles de intentos y respuestas
- Crear mensajes manualmente

#### Dashboard
- Estadísticas de mensajes
- Estado de colas
- Actividad reciente

## 🔄 Sistema de Colas

### Configuración

El sistema utiliza colas de base de datos por defecto:

```env
QUEUE_CONNECTION=database
```

### Comandos Útiles

```bash
# Procesar colas
php artisan queue:work

# Procesar colas con reintentos
php artisan queue:work --tries=3 --backoff=15

# Ver colas fallidas
php artisan queue:failed

# Reintentar colas fallidas
php artisan queue:retry all

# Limpiar colas fallidas
php artisan queue:flush
```

### Monitoreo

Usar Laravel Telescope para monitorear colas:
```
http://localhost:8000/telescope
```

## 🧪 Testing

### Ejecutar Tests
```bash
# Tests unitarios
php artisan test --testsuite=Unit

# Tests de características
php artisan test --testsuite=Feature

# Todos los tests
php artisan test
```

### Tests Disponibles
- Autenticación
- API endpoints
- Gestión de usuarios
- Envío de mensajes

## 🚀 Despliegue

### Producción

1. **Configurar variables de entorno**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

2. **Optimizar aplicación**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

3. **Configurar supervisor para colas**
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
```

4. **Configurar cron para tareas programadas**
```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### Docker en Producción

```bash
# Construir imagen
docker build -f docker/Dockerfile -t whatsappbot .

# Ejecutar contenedor
docker run -d \
  --name whatsappbot \
  -p 80:80 \
  -e APP_ENV=production \
  -e DB_HOST=your-db-host \
  whatsappbot
```

## 📊 Monitoreo y Logs

### Logs de Aplicación
```bash
tail -f storage/logs/laravel.log
```

### Logs de Colas
```bash
tail -f storage/logs/worker.log
```

### Laravel Telescope
```
http://localhost:8000/telescope
```

## 🔧 Comandos Artisan Útiles

```bash
# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Optimizar
php artisan optimize

# Ver rutas
php artisan route:list

# Ver colas
php artisan queue:work

# Crear usuario admin
php artisan db:seed --class=AdminUserSeeder

# Verificar estado de la aplicación
php artisan about
```

## 📝 Estructura del Proyecto

```
whatsappbot/
├── app/
│   ├── Actions/           # Acciones de Jetstream/Fortify
│   ├── Console/           # Comandos Artisan
│   ├── Constants/         # Constantes de la aplicación
│   ├── Events/            # Eventos
│   ├── Exceptions/        # Manejo de excepciones
│   ├── Filament/          # Panel de administración
│   ├── Helpers/           # Clases helper
│   ├── Http/              # Controladores, Requests, Middleware
│   ├── Jobs/              # Jobs de colas
│   ├── Listeners/         # Listeners de eventos
│   ├── Mail/              # Plantillas de email
│   ├── Models/            # Modelos Eloquent
│   ├── Policies/          # Políticas de autorización
│   ├── Providers/         # Service Providers
│   ├── Services/          # Servicios de negocio
│   └── Traits/            # Traits reutilizables
├── config/                # Archivos de configuración
├── database/              # Migraciones, seeders, factories
├── docker/                # Configuración Docker
├── public/                # Archivos públicos
├── resources/             # Vistas, assets, lang
├── routes/                # Definición de rutas
├── storage/               # Logs, cache, uploads
└── tests/                 # Tests automatizados
```

## 🤝 Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 🆘 Soporte

Para soporte técnico o preguntas:
- Crear un issue en el repositorio
- Contactar al equipo de desarrollo
- Revisar la documentación de Laravel y Filament

## 🔄 Changelog

### v1.0.0
- Implementación inicial del sistema
- API REST completa
- Panel de administración con Filament
- Sistema de colas para procesamiento asíncrono
- Soporte para mensajes de texto, plantillas e interactivos
- Autenticación con Laravel Sanctum
- Monitoreo con Laravel Telescope

---

**Desarrollado con ❤️ por Gerijacki usando Laravel y Filament**
