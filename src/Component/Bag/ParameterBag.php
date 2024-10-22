<?php

namespace Aatis\HttpFoundation\Component\Bag;

class ParameterBag
{
    /** @var array<string, mixed> */
    protected array $parameters;

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function has(string $key): bool
    {
        return isset($this->parameters[$key]);
    }

    public function get(string $key): mixed
    {
        return $this->parameters[$key] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->parameters;
    }

    public function set(string $key, mixed $value): static
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    public function add(string $key, mixed $value): static
    {
        if (!$this->has($key)) {
            return $this->set($key, $value);
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $initialValue = $this->get($key);
        if (!is_array($initialValue)) {
            $initialValue = [$initialValue];
        }

        return $this->set($key, [...$initialValue, ...$value]);
    }

    public function remove(string $key): static
    {
        unset($this->parameters[$key]);

        return $this;
    }
}
