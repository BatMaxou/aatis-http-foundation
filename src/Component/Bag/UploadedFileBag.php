<?php

namespace Aatis\HttpFoundation\Component\Bag;

use Aatis\HttpFoundation\Component\File\UploadedFile;

class UploadedFileBag extends ParameterBag
{
    /**
     * @param array<string, UploadedFile> $files
     */
    public function __construct(array $files = [])
    {
        parent::__construct($files);
    }

    public function get(string $key): ?UploadedFile
    {
        $value = parent::get($key);

        if (null === $value) {
            return null;
        }

        if (!$value instanceof UploadedFile) {
            throw new \LogicException(sprintf('The key "%s" is not a file.', $key));
        }

        return $value;
    }

    /**
     * @return array<string, UploadedFile>
     */
    public function all(): array
    {
        $all = parent::all();

        foreach ($all as $key => $value) {
            if (!$value instanceof UploadedFile) {
                throw new \LogicException(sprintf('The key "%s" is not an uploaded file.', $key));
            }
        }

        /** @var array<string, UploadedFile> */
        return $all;
    }

    /**
     * @param UploadedFile $file
     */
    public function set(string $key, mixed $file): static
    {
        return parent::set($key, $file);
    }

    public function add(string $key, mixed $files): static
    {
        return parent::add($key, $files);
    }
}
