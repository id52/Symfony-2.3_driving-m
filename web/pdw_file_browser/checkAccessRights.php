<?php

$save_path = __DIR__ . '/../../app/cache/prod/sessions/';
if (!is_readable($save_path)) {
    exit;
}

$loader = require __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

$storage = new NativeSessionStorage(array(), new NativeFileSessionHandler($save_path));
$session = new Session($storage);
$session->start();

if (!isset($_SESSION['_sf2_attributes']['_security_main'])) {
    exit;
}

$token = unserialize($_SESSION['_sf2_attributes']['_security_main']);
$is_mod = false;
$roles = $token->getUser()->getRoles();
foreach ($roles as $role) {
    $pos1 = strpos($role, 'ROLE_MOD');
    $pos2 = strpos($role, 'ROLE_ADMIN');
    if ($pos1 !== false || $pos2 !== false) {
        $is_mod = true;
        break;
    }
}

if (!$is_mod) {
    die('ERROR ACCESS DENIED');
}

