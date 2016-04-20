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
    )
);