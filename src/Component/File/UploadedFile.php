<?php

namespace Aatis\HttpFoundation\Component\File;

class UploadedFile extends File
{
    private string $originalName;
    private string $originalExtension;

    public function __construct(string $path, string $originalName)
    {
        parent::__construct($path);

        $this->originalName = pathinfo($originalName, PATHINFO_FILENAME);
        $this->originalExtension = pathinfo($originalName, PATHINFO_EXTENSION);
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getOriginalExtension(): string
    {
        return $this->originalExtension;
    }

    public function getFilename(): string
    {
        return $this->overrideName ?? $this->originalName;
    }

    public function getExtension(): string
    {
        return $this->originalExtension;
    }
}
