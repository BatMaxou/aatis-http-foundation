<?php

namespace Aatis\HttpFoundation\Interface;

interface FileInterface extends \Stringable
{
    /** @return resource|null */
    public function detach();

    public function close(): void;

    public function tell(): int;

    public function eof(): bool;

    public function seek(int $offset, int $whence = SEEK_SET): void;

    public function rewind(): void;

    public function read(int $length): string;

    public function getStream(): mixed;

    public function setOverrideName(string $fileName): static;

    public function write(string $string): int;

    public function append(string $string): int;

    public function save(string $path): bool;

    public function getContents(): string;

    public function getPath(): string;

    public function getFilename(): string;

    public function getExtension(): string;

    public function getBasename(string $suffix): string;

    public function getPathname(): string;

    public function getPerms(): int|false;

    public function getInode(): int|false;

    public function getSize(): int|false;

    public function getOwner(): int|false;

    public function getGroup(): int|false;

    public function getATime(): int|false;

    public function getMTime(): int|false;

    public function getCTime(): int|false;

    public function getType(): string|false;

    public function isWritable(): bool;

    public function isReadable(): bool;

    public function isExecutable(): bool;

    public function isFile(): bool;

    public function isDir(): bool;

    public function isLink(): bool;

    public function getLinkTarget(): string|false;

    public function getRealPath(): string|false;

    /**
     * @param class-string $class
     */
    public function getFileInfo(?string $class): \SplFileInfo;

    /**
     * @param class-string $class
     */
    public function getPathInfo(?string $class): ?\SplFileInfo;

    public function openFile(string $mode, bool $useIncludePath, mixed $context): \SplFileObject;

    /**
     * @param class-string $class
     */
    public function setFileClass(string $class): void;

    /**
     * @param class-string $class
     */
    public function setInfoClass(string $class): void;
}
