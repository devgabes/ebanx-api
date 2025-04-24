<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/reset') {
    file_put_contents(__DIR__ . '/data.json', json_encode([]));
    http_response_code(200);
    echo "OK";
}