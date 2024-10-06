<?php

namespace Aatis\HttpFoundation\Component;

class JsonResponse extends Response
{
    public function __construct(mixed $data, int $status = 200, array $headers = [])
    {
        $encodedData = json_encode($data);
        if (false === $encodedData) {
            throw new \RuntimeException(sprintf('Failed to encode data to JSON: %s', json_last_error_msg()));
        }

        parent::__construct($encodedData, $status, $headers);

        $this->headers->set('Content-Type', 'application/json');
    }
}
