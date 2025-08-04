<?php

namespace Aatis\HttpFoundation\Component\Bag;

use Aatis\ParameterBag;

class HeaderBag extends ParameterBag
{
    protected const UPPER = '_ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected const LOWER = '-abcdefghijklmnopqrstuvwxyz';

    /**
     * @var array<string, string[]>
     */
    private array $headersMap = [];

    /**
     * @param array<string, mixed> $headers
     */
    public function __construct(array $headers = [])
    {
        foreach ($headers as $name => $value) {
            $this->set($name, $value);
        }
    }

    public function hasHeader(string $name): bool
    {
        return null !== $this->checkKeyPresence($name);
    }

    /**
     * @return string[]
     */
    public function get(string $name): array
    {
        $values = [];
        $keys = $this->checkKeyPresence($name);

        if ($keys) {
            if (1 === count($keys)) {
                $value = parent::get($keys[0]);
                $values = is_array($value) ? $value : [$value];
            } else {
                foreach ($keys as $key) {
                    $value = parent::get($key);
                    $values = [...$values, ...(is_array($value) ? $value : [$value])];
                }
            }
        }

        return $values;
    }

    public function getInline(string $name): string
    {
        return implode(', ', $this->get($name));
    }

    public function set(string $name, mixed $value): static
    {
        if (null === $this->checkKeyPresence($name)) {
            $this->headersMap[$this->standardizeKey($name)] = [$name];
        }

        return parent::set($name, $value);
    }

    public function add(string $name, mixed $value): static
    {
        $keys = $this->checkKeyPresence($name);
        if (null === $keys) {
            $this->headersMap[$this->standardizeKey($name)] = [$name];
        } elseif (!in_array($name, $keys)) {
            $this->headersMap[$this->standardizeKey($name)] = [...$keys, $name];
        }

        return parent::add($name, $value);
    }

    public function remove(string $name): static
    {
        $standardizedKey = $this->standardizeKey($name);
        if ($this->hasHeader($standardizedKey)) {
            foreach ($this->headersMap[$standardizedKey] as $key) {
                parent::remove($key);
            }

            unset($this->headersMap[$standardizedKey]);
        }

        return $this;
    }

    public function __toString()
    {
        $headers = $this->all();
        if (0 === count($headers)) {
            return '';
        }

        ksort($headers);
        $content = '';
        foreach ($headers as $name => $values) {
            if (!is_array($values)) {
                $values = [$values];
            }

            foreach ($values as $value) {
                $content .= sprintf("%s: %s\r\n", $name, $value);
            }
        }

        return $content;
    }

    /**
     * @return string[]|null
     */
    private function checkKeyPresence(string $key): ?array
    {
        $standardizedKey = $this->standardizeKey($key);
        if (!isset($this->headersMap[$standardizedKey])) {
            return null;
        }

        return $this->headersMap[$standardizedKey];
    }

    private function standardizeKey(string $key): string
    {
        return strtr($key, self::UPPER, self::LOWER);
    }
}
