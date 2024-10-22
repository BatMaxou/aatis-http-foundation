<?php

namespace Aatis\HttpFoundation\Component\File;

use Aatis\HttpFoundation\Interface\FileInterface;

class File extends \SplFileInfo implements FileInterface
{
    /** @var resource|null */
    private $stream;

    protected ?string $overrideName = null;

    public function __construct(string $path)
    {
        if (!is_file($path)) {
            throw new \FileNotFoundException($path);
        }

        parent::__construct($path);

        if ($this->isReadable()) {
            $this->stream = fopen($path, 'rb+') ?: null;
        }
    }

    public function __toString(): string
    {
        try {
            $this->rewind();

            return $this->getContents();
        } catch (\Exception) {
            return '';
        }
    }

    /**
     * @return resource|null
     */
    public function getStream(): mixed
    {
        return $this->stream;
    }

    public function getFilename(): string
    {
        return $this->overrideName ?? parent::getFilename();
    }

    public function setOverrideName(string $fileName): static
    {
        $this->overrideName = $fileName;

        return $this;
    }

    public function read(int $length): string
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->isReadable()) {
            throw new \RuntimeException('File is not readable');
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
            throw new \RuntimeException('Unable to read the file', 0, $e);
        }

        if (false === $string) {
            throw new \RuntimeException('Unable to read the file');
        }

        return $string;
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

        return $return;
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

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        $result = fseek($this->stream, $offset, $whence);

        if (-1 === $result) {
            throw new \RuntimeException('Unable to seek the file');
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function write(string $string): int
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->isWritable()) {
            throw new \RuntimeException('File is not writable');
        }

        $updated = fwrite($this->stream, $string);

        if (false === $updated) {
            throw new \RuntimeException('Unable to write into the file');
        }

        return $updated;
    }

    public function append(string $string): int
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->isWritable()) {
            throw new \RuntimeException('File is not writable');
        }

        $this->seek(0, SEEK_END);

        return $this->write($string);
    }

    public function getContents(): string
    {
        $stream = $this->stream;
        if (!isset($stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->isReadable()) {
            throw new \RuntimeException('File is not readable');
        }

        try {
            $this->rewind();
            $string = stream_get_contents($stream);
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to read the file', 0, $e);
        }

        if (false === $string) {
            throw new \RuntimeException('Unable to read the file');
        }

        $this->rewind();

        return $string;
    }

    public function save(string $path): bool
    {
        $stream = $this->stream;
        if (!isset($stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->isReadable()) {
            throw new \RuntimeException('File is not readable');
        }

        $target = fopen(sprintf(
            '%s/%s',
            $path,
            sprintf('%s.%s', $this->getFilename(), $this->getExtension())
        ), 'wb+');
        if (!$target) {
            throw new \RuntimeException('Unable to write to the target file');
        }

        $result = stream_copy_to_stream($stream, $target);

        if (is_int($result)) {
            return fclose($target);
        }

        return $result;
    }
}
