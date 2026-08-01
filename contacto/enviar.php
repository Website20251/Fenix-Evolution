<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function clean_input(string $value): string
{
    $value = trim($value);
    $value = str_replace(["\r", "\n"], ' ', $value);
    return filter_var($value, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
}

function response_payload(bool $success, string $message, array $extra = []): array
{
    return array_merge([
        'success' => $success,
        'message' => $message,
    ], $extra);
}

function text_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
}

$nombre = clean_input($_POST['nombre'] ?? '');
$correo = clean_input($_POST['correo'] ?? '');
$celular = clean_input($_POST['celular'] ?? '');
$servicio = clean_input($_POST['servicio'] ?? '');
$mensaje = trim((string)($_POST['mensaje'] ?? ''));
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$expectsJson = $isAjax || (isset($_SERVER['HTTP_ACCEPT']) && strpos((string)$_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

$errors = [];

if ($nombre === '' || text_length($nombre) < 3) {
    $errors[] = 'El nombre es obligatorio.';
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'El correo no es válido.';
}

if ($celular === '' || !preg_match('/^[0-9+\s()-]{7,20}$/', $celular)) {
    $errors[] = 'El celular debe contener entre 7 y 20 caracteres válidos.';
}

$allowedServices = ['Fisioterapia Deportiva', 'Entrenamiento Personal', 'Nutrición Deportiva'];
if (!in_array($servicio, $allowedServices, true)) {
    $errors[] = 'Selecciona un servicio válido.';
}

if (text_length($mensaje) < 10) {
    $errors[] = 'El mensaje debe tener al menos 10 caracteres.';
}

if ($errors !== []) {
    http_response_code(422);
    $payload = response_payload(false, implode(' ', $errors), ['errors' => $errors]);
    if ($expectsJson) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    echo '<!doctype html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Error al enviar</title><style>body{font-family:Arial,sans-serif;background:#07111f;color:#fff;display:grid;place-items:center;min-height:100vh;margin:0;padding:2rem}main{max-width:720px;background:#0f1a29;border:1px solid rgba(255,255,255,.1);padding:2rem;border-radius:24px}a{color:#9aff2f}</style></head><body><main><h1>No pudimos enviar tu mensaje</h1><p>' . htmlspecialchars(implode(' ', $errors), ENT_QUOTES, 'UTF-8') . '</p><p><a href="../index.html">Volver al sitio</a></p></main></body></html>';
    exit;
}

$fullMessage = "Nombre: {$nombre}\nCorreo: {$correo}\nCelular: {$celular}\nServicio: {$servicio}\n\nMensaje:\n{$mensaje}";
$mailSent = false;
$mailError = null;

$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;

    if (class_exists(PHPMailer::class)) {
        try {
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = getenv('FISIOTRAINING_SMTP_HOST') ?: 'smtp.example.com';
            $mail->SMTPAuth = true;
            $mail->Username = getenv('FISIOTRAINING_SMTP_USER') ?: 'usuario@example.com';
            $mail->Password = getenv('FISIOTRAINING_SMTP_PASS') ?: 'cambia-esta-clave';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int)(getenv('FISIOTRAINING_SMTP_PORT') ?: 587);
            $mail->setFrom('no-reply@fisiotraining.pe', 'Fisiotraining');
            $mail->addAddress('contacto@fisiotraining.pe', 'Fisiotraining');
            $mail->addReplyTo($correo, $nombre);
            $mail->Subject = 'Nuevo contacto desde Fisiotraining';
            $mail->Body = $fullMessage;
            $mail->send();
            $mailSent = true;
        } catch (Exception $exception) {
            $mailError = $exception->getMessage();
        }
    }
}

if (!$mailSent) {
    $fallback = @mail(
        'contacto@fisiotraining.pe',
        'Nuevo contacto desde Fisiotraining',
        $fullMessage,
        "From: Fisiotraining <no-reply@fisiotraining.pe>\r\nReply-To: {$correo}\r\nContent-Type: text/plain; charset=UTF-8"
    );

    $mailSent = $fallback;
}

if (!$mailSent) {
    http_response_code(500);
    $message = $mailError ? 'No se pudo enviar el mensaje. ' . $mailError : 'No se pudo enviar el mensaje en este momento.';
    if ($expectsJson) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(response_payload(false, $message), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    echo '<!doctype html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Error al enviar</title></head><body><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p><p><a href="../index.html">Volver al sitio</a></p></body></html>';
    exit;
}

$successMessage = 'Mensaje enviado correctamente. Te contactaremos en breve.';
if ($expectsJson) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(response_payload(true, $successMessage), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

echo '<!doctype html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Mensaje enviado</title><style>body{font-family:Arial,sans-serif;background:#07111f;color:#fff;display:grid;place-items:center;min-height:100vh;margin:0;padding:2rem}main{max-width:720px;background:#0f1a29;border:1px solid rgba(255,255,255,.1);padding:2rem;border-radius:24px}a{color:#9aff2f}</style></head><body><main><h1>Gracias por escribirnos</h1><p>Tu mensaje fue enviado correctamente.</p><p><a href="../index.html">Volver al sitio</a></p></main></body></html>';
