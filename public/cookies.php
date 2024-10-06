<?php

use Aatis\HttpFoundation\Component\Request;

$request = Request::createFromGlobals();

dd($request->cookies);
