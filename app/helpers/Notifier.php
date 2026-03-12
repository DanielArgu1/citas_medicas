<?php

class Notifier
{
    public static function enviarCorreo($para, $asunto, $mensaje)
    {
        $logDir = __DIR__ . '/../logs';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $texto = "[" . date('Y-m-d H:i:s') . "] PARA: {$para} | ASUNTO: {$asunto}\n{$mensaje}\n\n";
        file_put_contents($logDir . '/notificaciones.log', $texto, FILE_APPEND);

        @mail($para, $asunto, $mensaje);
        return true;
    }
}