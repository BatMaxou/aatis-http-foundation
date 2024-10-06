<?php

use Aatis\HttpFoundation\Component\JsonResponse;
use Aatis\HttpFoundation\Component\Request;

$request = Request::createFromGlobals();

$content = ['message' => 'ZEBI, you are a teapot!'];
$response = (new JsonResponse($content, 418))->prepare($request);

$response->send();
