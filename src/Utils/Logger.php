<?php

namespace App\Utils;

class Logger
{
    private static string $logPath = __DIR__ . '/../../Logs/';

    /**
     * Escribe un mensaje en un archivo de log específico.
     * * @param string $filename Nombre del archivo (ej: 'db_errors', 'auth')
     * @param mixed $data El mensaje o array de datos a guardar
     */
    public static function log(string $filename, $data): void
    {
        // 1. Asegurarnos de que la carpeta existe
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0777, true);
        }

        // 2. Formatear el mensaje
        $date = date('Y-m-d H:i:s');
        
        // Si nos pasan un array u objeto, lo convertimos a JSON para que sea legible
        if (is_array($data) || is_object($data)) {
            $message = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            $message = $data;
        }

        $logEntry = "[$date] " . $message . PHP_EOL . str_repeat("-", 30) . PHP_EOL;

        // 3. Escribir en el archivo (se crea si no existe)
        $filePath = self::$logPath . $filename . '.log';
        file_put_contents($filePath, $logEntry, FILE_APPEND);
    }
}