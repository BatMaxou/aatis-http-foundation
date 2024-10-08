<?php

namespace Aatis\HttpFoundation\Component\Bag;

class ServerBag extends ParameterBag
{
    /**
     * @return array<string, mixed>
     */
    public function getHeaders(): array
    {
        $headers = [];
        foreach ($this->parameters as $key => $value) {
            if (str_starts_with($key, 'HTTP_') && !str_contains($key, 'HTTP_COOKIE')) {
                $headers[substr($key, 5)] = $value;
            }
        }

        return $headers;
    }
}
