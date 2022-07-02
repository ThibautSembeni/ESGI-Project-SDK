<?php

function readDatabase($filename) {
    $data = file($filename);

    return array_map(fn ($line) => json_decode($line, true), $data);
}

function writeDatabase($filename, $data) {
    file_put_contents($filename, implode(
        "\n",
        array_map(
            fn($line) => json_encode($line),
            $data
        )
    ));
}

function insertData($filename, $data) {
    $database = readDatabase($filename);
    $database[] = $data;
    writeDatabase($filename, $database);
}

function insertApp($app) {
    insertData('./data/apps.db', $app);
}

function insertCode($code) {
    insertData('./data/codes.db', $code);
}

function insertToken($token) {
    insertData('./data/tokens.db', $token);
}

function insertUser($user) {
    insertData('./data/users.db', $user);
}

function findBy($filename, $criteria) {
    $database = readDatabase($filename);

    $result = array_values(array_filter(
        $database, 
        fn($app) => count(array_intersect_assoc($app, $criteria)) === count($criteria)
    ));

    return $result[0] ?? null;
}

function findAppBy($criteria) {
    return findBy('./data/apps.db', $criteria);
}

function findCodeBy($criteria) {
    return findBy('./data/codes.db', $criteria);
}

function findTokenBy($criteria) {
    return findBy('./data/tokens.db', $criteria);
}

function findUserBy($criteria) {
    return findBy('./data/users.db', $criteria);
}