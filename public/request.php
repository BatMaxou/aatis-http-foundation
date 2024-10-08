<?php

use Aatis\HttpFoundation\Component\Request;

$request = Request::createFromGlobals();
$request->headers->set('Content-Type', $request::FORMAT['txt'][0]);

echo $request;
dd($request);
