<?php

use Aatis\HttpFoundation\Component\Response;

$response = new Response('ZEBI', 418);

$response->headers
    ->set('Content-Type', 'application/json')
    ->add('Content-Type', 'text/html')
    ->add('Content_Type', 'other')
    ->set('X-Powered-By', 'PHP/8.0')
    ->add('X-PowereD_By', 'Symfony')
    ->add('x_Powered-By', 'Docker');

$all = $response->headers->all();
$ct = $response->headers->get('Content-Type');
$inline = $response->headers->getInline('Content-Type');
$paramHas = $response->headers->has('x_powered_by');
$has = $response->headers->hasHeader('x_powered_by');
$removed = $response->headers->remove('x_powered_by');

echo $response;

dd([
    'all' => $all,
    'ct' => $ct,
    'inline' => $inline,
    'paramHas' => $paramHas,
    'has' => $has,
    'removed' => $removed->all(),
    'response' => $response,
]);
