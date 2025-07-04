<?php

namespace Database\Seeders;

use App\Models\BotCommand;
use Illuminate\Database\Seeder;

class BotCommandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commands = [
            [
                'trigger_text' => 'ayuda',
                'class_name' => 'App\Commands\BotCommands\HelpCommand',
                'description' => 'Muestra todos los comandos disponibles del bot',
                'is_active' => true,
                'priority' => 100,
            ],
            [
                'trigger_text' => 'hola',
                'class_name' => 'App\Commands\BotCommands\WelcomeCommand',
                'description' => 'Mensaje de bienvenida para nuevos usuarios',
                'is_active' => true,
                'priority' => 90,
            ],
            [
                'trigger_text' => 'info',
                'class_name' => 'App\Commands\BotCommands\InfoCommand',
                'description' => 'Muestra información sobre nuestros servicios con botones interactivos',
                'is_active' => true,
                'priority' => 80,
            ],
            [
                'trigger_text' => 'support',
                'class_name' => 'App\Commands\BotCommands\SupportCommand',
                'description' => 'Información sobre nuestro servicio de soporte técnico',
                'is_active' => true,
                'priority' => 70,
            ],
            [
                'trigger_text' => 'development',
                'class_name' => 'App\Commands\BotCommands\DevelopmentCommand',
                'description' => 'Información sobre nuestros servicios de desarrollo',
                'is_active' => true,
                'priority' => 70,
            ],
            [
                'trigger_text' => 'design',
                'class_name' => 'App\Commands\BotCommands\DesignCommand',
                'description' => 'Información sobre nuestros servicios de diseño',
                'is_active' => true,
                'priority' => 70,
            ],
            [
                'trigger_text' => 'consulting',
                'class_name' => 'App\Commands\BotCommands\ConsultingCommand',
                'description' => 'Información sobre nuestros servicios de consultoría',
                'is_active' => true,
                'priority' => 70,
            ],
        ];

        foreach ($commands as $command) {
            BotCommand::updateOrCreate(
                ['trigger_text' => $command['trigger_text']],
                $command
            );
        }
    }
} 