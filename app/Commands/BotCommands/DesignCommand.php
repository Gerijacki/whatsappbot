<?php

namespace App\Commands\BotCommands;

use App\Contracts\BotCommandInterface;

class DesignCommand implements BotCommandInterface
{
    public function execute(array $webhookData, array $commandParameters = []): array
    {
        return [
            'type' => 'text',
            'text' => "🌐 *Diseño Web Profesional*\n\n" .
                      "Creamos diseños web modernos, responsivos y centrados en la experiencia del usuario.\n\n" .
                      "🎨 *Servicios de diseño:*\n" .
                      "• Diseño de sitios web corporativos\n" .
                      "• Landing pages optimizadas\n" .
                      "• E-commerce y tiendas online\n" .
                      "• Aplicaciones web personalizadas\n" .
                      "• Rediseño de sitios existentes\n\n" .
                      "🛠️ *Herramientas que utilizamos:*\n" .
                      "• Figma, Adobe XD, Sketch\n" .
                      "• Photoshop, Illustrator\n" .
                      "• HTML5, CSS3, JavaScript\n" .
                      "• WordPress, Shopify, WooCommerce\n\n" .
                      "✨ *Características incluidas:*\n" .
                      "• Diseño responsivo (móvil, tablet, desktop)\n" .
                      "• Optimización SEO\n" .
                      "• Integración con redes sociales\n" .
                      "• Formularios de contacto\n" .
                      "• Panel de administración\n" .
                      "• Certificado SSL\n\n" .
                      "📊 *Proceso de diseño:*\n" .
                      "1. Briefing y análisis\n" .
                      "2. Wireframes y mockups\n" .
                      "3. Diseño visual\n" .
                      "4. Desarrollo frontend\n" .
                      "5. Testing y optimización\n\n" .
                      "💰 *Precios desde:* $2,500 USD\n" .
                      "⏱️ *Tiempo estimado:* 2-6 semanas\n\n" .
                      "¿Quieres ver nuestro portafolio? Escribe *portafolio* para ver ejemplos de nuestros trabajos."
        ];
    }

    public function getDescription(): string
    {
        return 'Información sobre nuestros servicios de diseño';
    }

    public function canHandle(string $messageType): bool
    {
        return $messageType === 'text' || $messageType === 'interactive';
    }
} 