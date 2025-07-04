<?php

namespace App\Contracts;

interface BotCommandInterface
{
    /**
     * Ejecutar el comando del bot
     *
     * @param array $webhookData Datos del webhook de WhatsApp
     * @param array $commandParameters Parámetros adicionales del comando
     * @return array Respuesta a enviar
     */
    public function execute(array $webhookData, array $commandParameters = []): array;

    /**
     * Obtener la descripción del comando
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Verificar si el comando puede manejar el tipo de mensaje
     *
     * @param string $messageType Tipo de mensaje (text, image, document, etc.)
     * @return bool
     */
    public function canHandle(string $messageType): bool;
} 