<?php

require_once 'src/Store.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/reset') {
    file_put_contents(__DIR__ . '/data.json', json_encode([]));
    http_response_code(200);
    echo "OK";
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], '/balance') === 0) {
    parse_str($_SERVER['QUERY_STRING'], $query);
    $data = Store::load();
    $accountId = $query['account_id'] ?? '';
    if (!isset($data[$accountId])) {
        http_response_code(404);
        echo 0;
    } else {
        http_response_code(200);
        echo $data[$accountId];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/event') {
    $input = json_decode(file_get_contents('php://input'), true);

    $type = $input['type'] ?? null;
    $amount = $input['amount'] ?? 0;
    $destination = $input['destination'] ?? null;

    if ($type === 'deposit' && $destination) {
        $data = Store::load();
        if (!isset($data[$destination])) {
            $data[$destination] = 0;
        }
        $data[$destination] += $amount;
        Store::save($data);
        http_response_code(201);
        echo json_encode(['destination' => ['id' => $destination, 'balance' => $data[$destination]]]);
    }

    if ($type === 'withdraw' && isset($input['origin'])) {
        $origin = $input['origin'];
        $data = Store::load();

        if (!isset($data[$origin])) {
            http_response_code(404);
            echo 0;
            return;
        }

        $data[$origin] -= $amount;
        Store::save($data);
        http_response_code(201);
        echo json_encode(['origin' => ['id' => $origin, 'balance' => $data[$origin]]]);
    }

    if ($type === 'transfer' && isset($input['origin'], $input['destination'])) {
        $origin = $input['origin'];
        $destination = $input['destination'];
        $data = Store::load();

        if (!isset($data[$origin])) {
            http_response_code(404);
            echo 0;
            return;
        }

        $data[$origin] -= $amount;
        if (!isset($data[$destination])) {
            $data[$destination] = 0;
        }
        $data[$destination] += $amount;

        Store::save($data);
        http_response_code(201);
        echo json_encode([
            'origin' => ['id' => $origin, 'balance' => $data[$origin]],
            'destination' => ['id' => $destination, 'balance' => $data[$destination]]
        ]);
    }
}