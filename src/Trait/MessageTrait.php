<?php

namespace Aatis\HttpFoundation\Trait;

use Aatis\HttpFoundation\Component\Bag\HeaderBag;

trait MessageTrait
{
    public HeaderBag $headers;

    private string $protocol = '1.0';

    private string $content;

    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    public function setProtocolVersion(string $version): static
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $this->protocol = $version;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function __clone()
    {
        $this->headers = clone $this->headers;
    }
}
