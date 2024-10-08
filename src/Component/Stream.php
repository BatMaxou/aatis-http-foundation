<?php

namespace Aatis\HttpFoundation\Component;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    /** @var resource|null */
    private $stream;

    private bool $seekable;

    private bool $readable;

    private bool $writable;

    private ?int $size;

    private ?string $uri;

    /** @var mixed[] */
    private array $metadata;

    /** @var mixed[] */
    private array $customMetadata;

    /**
     * @param resource $stream
     * @param array{
     *  size?: int,
     *  metadata?: mixed[]
     * } $options
     */
    public function __construct($stream, array $options)
    {
        if (isset($options['size'])) {
            $this->size = $options['size'];
        }

        $this->stream = $stream;
        $this->customMetadata = $options['metadata'] ?? [];
        $this->metadata = stream_get_meta_data($this->stream);

        $this->extractUsefulMetadata();
    }

    public function __toString(): string
    {
        try {
            $this->rewind();

            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }

    public function close(): void
    {
        if (isset($this->stream)) {
            fclose($this->stream);
        }

        $this->detach();
    }

    /**
     * @return resource|null
     */
    public function detach(): mixed
    {
        if (!isset($this->stream)) {
            return null;
        }

        $return = $this->stream;

        unset($this->stream);
        $this->size = $this->uri = null;
        $this->writable = $this->readable = $this->seekable = false;

        return $return;
    }

    public function getSize(): ?int
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (null !== $this->size) {
            return $this->size;
        }

        if ($this->uri) {
            clearstatcache(true, $this->uri);
        }

        $stats = fstat($this->stream);
        if (isset($stats['size'])) {
            $this->size = $stats['size'];

            return $this->size;
        }

        return null;
    }

    public function tell(): int
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        $position = ftell($this->stream);

        if (!$position) {
            throw new \RuntimeException('Unable to get the cursor position');
        }

        return $position;
    }

    public function eof(): bool
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        return feof($this->stream);
    }

    public function isSeekable(): bool
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        return $this->seekable;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->seekable) {
            throw new \RuntimeException('Stream is not seekable');
        }

        fseek($this->stream, $offset, $whence);
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function write(string $string): int
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        if ($this->writable) {
            throw new \RuntimeException('Stream is not writable');
        }

        $this->size = null;
        $updated = fwrite($this->stream, $string);

        if (false === $updated) {
            throw new \RuntimeException('Unable to write into the stream');
        }

        return $updated;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function read(int $length): string
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->readable) {
            throw new \RuntimeException('Stream is not readable');
        }

        if ($length < 0) {
            throw new \RuntimeException('Can not read until a negative position');
        }

        if (0 === $length) {
            return '';
        }

        try {
            $string = fread($this->stream, $length);
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to read the stream', 0, $e);
        }

        if (false === $string) {
            throw new \RuntimeException('Unable to read the stream');
        }

        return $string;
    }

    public function getContents(): string
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->readable) {
            throw new \RuntimeException('Stream is not readable');
        }

        try {
            $string = stream_get_contents($this->stream);
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to read the stream', 0, $e);
        }

        if (false === $string) {
            throw new \RuntimeException('Unable to read the stream');
        }

        return $string;
    }

    public function getMetadata($key = null): mixed
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$key) {
            return [
                ...$this->customMetadata,
                ...stream_get_meta_data($this->stream),
            ];
        }

        if (isset($this->customMetadata[$key])) {
            return $this->customMetadata[$key];
        }

        return stream_get_meta_data($this->stream)[$key] ?? null;
    }

    /**
     * @param array{
     *  size?: int,
     *  metadata?: mixed[]
     * } $options
     */
    public static function createFrom(mixed $data, array $options = []): StreamInterface
    {
        if ($data instanceof StreamInterface) {
            return $data;
        }

        if (is_object($data) && method_exists($data, '__toString')) {
            return self::createFrom((string) $data, $options);
        }

        $resource = fopen('php://temp', 'r+');

        if (!$resource) {
            throw new \RuntimeException('Unable to create a temporary file');
        }

        if (null === $data) {
            return new self($resource, $options);
        }

        if (is_scalar($data)) {
            if ('' !== $data) {
                fwrite($resource, (string) $data);
                rewind($resource);
            }

            return new self($resource, $options);
        }

        if (is_resource($data)) {
            if ('php://input' === stream_get_meta_data($data)['uri']) {
                stream_copy_to_stream($data, $resource);
                rewind($resource);
                $data = $resource;
            }

            return new self($data, $options);
        }

        throw new \InvalidArgumentException('Stream must be a string stream, stream resource or object with a __toString method');
    }

    private function extractUsefulMetadata(): void
    {
        $uri = $this->getMetadata('uri');

        if (null === $uri || is_string($uri)) {
            $this->uri = $uri;
        } else {
            throw new \RuntimeException('Invalid URI');
        }

        $this->seekable = (bool) $this->metadata['seekable'];

        $mode = $this->metadata['code'] ?? null;
        if ($mode && is_string($mode)) {
            $this->readable = str_contains($mode, 'r') || str_contains($mode, '+');
            $this->writable = str_contains($mode, 'x')
                || str_contains($mode, 'w')
                || str_contains($mode, 'c')
                || str_contains($mode, 'a')
                || str_contains($mode, '+');
        } else {
            $this->readable = $this->writable = false;
        }
    }
}
