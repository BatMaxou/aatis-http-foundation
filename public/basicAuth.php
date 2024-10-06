<?php

use Aatis\HttpFoundation\Component\Request;
use Aatis\HttpFoundation\Component\Response;

$request = Request::createFromGlobals();

$account = [
    'username' => 'admin',
    'password' => 'admin',
];

$response = (new Response())->prepare($request);

$isAuthenticated = false;

if ($request->headers->hasHeader('Authorization')) {
    $auth = $request->headers->get('Authorization')[0];
    $decoded = base64_decode(str_replace('Basic ', '', $auth));
    [$username, $password] = explode(':', $decoded);

    if ($username === $account['username'] && $password === $account['password']) {
        $isAuthenticated = true;
    }
}

$isAuthenticated
    ? $response->setContent('You are authenticated!')
    : $response
        ->setContent('Invalid credentials')
        ->setStatusCode(401)
        ->headers
            ->set('WWW-Authenticate', 'Basic realm="Access to Index"')
            ->set('Test', 'Test')
;

$response->send();
