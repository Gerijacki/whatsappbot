# 🤖 Sistema de Bot para WhatsApp

Este proyecto incluye un sistema completo de bot para WhatsApp que permite procesar mensajes entrantes y responder automáticamente basándose en comandos configurables.

## 🚀 Características

- **Webhook de WhatsApp**: Recibe y procesa mensajes de WhatsApp
- **Sistema de comandos**: Comandos configurables desde la base de datos
- **Mensajes interactivos**: Soporte para botones y listas
- **Panel de administración**: Gestión de comandos desde Filament
- **Múltiples tipos de mensaje**: Texto, imágenes, documentos, audio, video, etc.
- **Logging completo**: Registro detallado de todas las operaciones

## 📋 Requisitos

- Laravel 10+
- Base de datos MySQL/PostgreSQL
- Token de acceso de WhatsApp Business API
- URL pública para el webhook

## 🛠️ Instalación

### 1. Configurar variables de entorno

Añade estas variables a tu archivo `.env`:

```env
# WhatsApp Configuration
WHATSAPP_VERIFY_TOKEN=tu_token_de_verificacion
WHATSAPP_APP_ID=tu_app_id
WHATSAPP_APP_SECRET=tu_app_secret
WHATSAPP_PHONE_NUMBER_ID=tu_phone_number_id
WHATSAPP_ACCESS_TOKEN=tu_access_token
WHATSAPP_WEBHOOK_URL=https://tu-dominio.com/api/v1/webhook
```

### 2. Ejecutar migraciones

```bash
php artisan migrate
```

### 3. Ejecutar seeders

```bash
php artisan db:seed
```

### 4. Configurar webhook en WhatsApp

1. Ve a [Meta for Developers](https://developers.facebook.com/)
2. Configura tu webhook con la URL: `https://tu-dominio.com/api/v1/webhook`
3. Usa el token de verificación configurado en `WHATSAPP_VERIFY_TOKEN`

## 🎯 Uso

### Rutas disponibles

- `GET /api/v1/webhook` - Verificación del webhook
- `POST /api/v1/webhook` - Recepción de mensajes

### Comandos de Artisan

```bash
# Probar el bot con un mensaje
php artisan bot:test "ayuda" --phone=1234567890

# Ver todos los comandos disponibles
php artisan list | grep bot
```

### Panel de administración

Accede a `https://tu-dominio.com/admin` para gestionar:
- Comandos del bot
- Clientes de WhatsApp
- Mensajes enviados

## 📝 Crear nuevos comandos

### 1. Crear la clase del comando

```php
<?php

namespace App\Commands\BotCommands;

use App\Contracts\BotCommandInterface;

class MiComandoCommand implements BotCommandInterface
{
    public function execute(array $webhookData, array $commandParameters = []): array
    {
        return [
            'type' => 'text',
            'text' => '¡Hola! Este es mi comando personalizado.',
        ];
    }

    public function getDescription(): string
    {
        return 'Descripción de mi comando';
    }

    public function canHandle(string $messageType): bool
    {
        return $messageType === 'text';
    }
}
```

### 2. Registrar el comando en la base de datos

Desde el panel de administración o usando el seeder:

```php
BotCommand::create([
    'trigger_text' => 'micomando',
    'class_name' => 'App\Commands\BotCommands\MiComandoCommand',
    'description' => 'Descripción de mi comando',
    'is_active' => true,
    'priority' => 50,
]);
```

## 🔧 Tipos de respuesta

### Mensaje de texto

```php
return [
    'type' => 'text',
    'text' => 'Tu mensaje aquí',
];
```

### Botones interactivos

```php
return [
    'type' => 'interactive',
    'interactive' => [
        'type' => 'button',
        'body' => [
            'text' => 'Texto del mensaje'
        ],
        'action' => [
            'buttons' => [
                [
                    'type' => 'reply',
                    'reply' => [
                        'id' => 'boton1',
                        'title' => 'Botón 1'
                    ]
                ]
            ]
        ]
    ]
];
```

### Lista interactiva

```php
return [
    'type' => 'interactive',
    'interactive' => [
        'type' => 'list',
        'body' => [
            'text' => 'Selecciona una opción:'
        ],
        'action' => [
            'button' => 'Ver opciones',
            'sections' => [
                [
                    'title' => 'Sección 1',
                    'rows' => [
                        [
                            'id' => 'opcion1',
                            'title' => 'Opción 1',
                            'description' => 'Descripción de la opción 1'
                        ]
                    ]
                ]
            ]
        ]
    ]
];
```

## 📊 Estructura de la base de datos

### Tabla `bot_commands`

- `trigger_text`: Texto que activa el comando
- `class_name`: Nombre completo de la clase
- `description`: Descripción del comando
- `is_active`: Si el comando está activo
- `priority`: Prioridad para matching
- `parameters`: Parámetros adicionales (JSON)

## 🔍 Debugging

### Logs

Los logs se guardan en `storage/logs/laravel.log`:

```bash
tail -f storage/logs/laravel.log
```

### Probar comandos

```bash
# Probar comando específico
php artisan bot:test "ayuda"

# Probar con número específico
php artisan bot:test "hola" --phone=1234567890
```

## 🚨 Solución de problemas

### Error: "No active WhatsApp client found"

1. Verifica que tengas un cliente de WhatsApp configurado en la base de datos
2. Asegúrate de que el campo `is_active` esté en `true`

### Error: "Command class not found"

1. Verifica que la clase existe en el namespace correcto
2. Asegúrate de que implementa `BotCommandInterface`
3. Ejecuta `composer dump-autoload`

### Los mensajes interactivos no funcionan

1. Verifica que el `access_token` sea válido
2. Asegúrate de que el `phone_number_id` sea correcto
3. Revisa los logs para errores específicos de la API

## 📚 Comandos predefinidos

- `ayuda` - Muestra todos los comandos disponibles
- `hola` - Mensaje de bienvenida
- `info` - Información sobre servicios con botones
- `support` - Información de soporte técnico
- `development` - Información de desarrollo
- `design` - Información de diseño
- `consulting` - Información de consultoría

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Ver el archivo `LICENSE` para más detalles. 