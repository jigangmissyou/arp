<?php
session_set_cookie_params([
    'lifetime' => 60 * 60 * 24 * 7, // 7 days
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']), // true if using HTTPS
    'httponly' => true,
    'samesite' => 'Lax' // or 'Strict' or 'None'
]);

session_start();
