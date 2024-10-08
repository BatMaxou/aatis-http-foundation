<?php

use Aatis\HttpFoundation\Component\JsonResponse;

$response = new JsonResponse([
    'test1' => 'value1',
    'test2' => 'value2',
    'test3' => 'value3',
], 418, );

echo $response;

dd([
    'response' => $response,
]);
