<?php
return array(
    'serverName' => 'yourdomain.com',
    'database' => array(
        'host' => 'localhost',
        'username' => '',
        'password' => '',
        'database' => ''
    ),
    'jwt' => array(
        'key' => '', //Secret key, I suggest generate it with base64_encode(openssl_random_pseudo_bytes(64))
        'algorithm' => 'HS512'
    ),
    'email' => array(
        'host' => '',
        'username' => '',
        'password' => '',
        'use_secure_connection' => true,
        'secure' => 'tls',               // tls and ssl supported
        'port' => 587,
        'from_address' => '',
        'from_name' => '',
        'answer_address' => '',
        'answer_name' => '',
        'html' => true
    )
);