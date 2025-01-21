<?php

function init_session()
{
    session_set_cookie_params([
        'lifetime' => 3600,
        'path' => '/~s5145768/',
        'domain' => 'saw.dibris.unige.it',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();

    ob_clean();

    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: https://saw.dibris.unige.it");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");
}
