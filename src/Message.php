<?php

namespace Aatis\HttpFoundation;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\MessageInterface;

class Message implements MessageInterface
{
    /**
     * @var array<string, string|string[]>
     */
    private array $headers;

    /**
     * @var array<string, string>
     */
    private array $headersMap;

    private string $protocol = '1.1';

    private StreamInterface $body;

    public function __construct()
    {
    }

    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    public function withProtocolVersion(string $version): static
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $new = clone $this;
        $new->protocol = $version;

        return $new;
    }

    /**
     * @return array<string, string|string[]>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return null !== $this->checkKeyPresence($name);
    }

    /**
     * @return string[]
     */
    public function getHeader(string $name): array
    {
        $key = $this->checkKeyPresence($name);

        if ($key) {
            $value = $this->headers[$key];

            return is_array($value) ? $value : [$value];
        }

        return [];
    }

    public function getHeaderLine(string $name): string
    {
        $key = $this->checkKeyPresence($name);

        if ($key) {
            $value = $this->headers[$key];

            return is_array($value) ? implode(', ', $value) : $value;
        }

        return '';
    }

    public function withHeader(string $name, $value): static
    {
        $new = clone $this;
        $new->headers[$name] = $value;
        $new->headersMap[strtolower($name)] = $name;

        return $new;
    }

    /**
     * @param string|string[] $value
     */
    public function withAddedHeader(string $name, mixed $value): static
    {
        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $initialValue = $this->headers[$name];
        if (!is_array($initialValue)) {
            $initialValue = [$initialValue];
        }

        $new = clone $this;
        $new->headers[$name] = [...$initialValue, ...$value];

        return $new;
    }

    public function withoutHeader(string $name): static
    {
        if ($this->hasHeader($name)) {
            $new = clone $this;
            unset($new->headers[$name], $new->headersMap[strtolower($name)]);

            return $new;
        }

        return $this;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): static
    {
        $new = clone $this;
        $new->body = $body;

        return $new;
    }

    private function checkKeyPresence(string $key): ?string
    {
        return $this->headersMap[$key] ?? null;
    }
}
