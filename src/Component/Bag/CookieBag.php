<?php

namespace Aatis\HttpFoundation\Component\Bag;

class CookieBag extends ParameterBag
{
    public function getInline(): string
    {
        $inline = '';
        foreach ($this->parameters as $key => $values) {
            if (is_array($values)) {
                $values = implode(';', $values);
            }

            if (is_string($values)) {
                $inline .= sprintf('%s=%s; ', $key, $values);
            }
        }

        return $inline;
    }
}
