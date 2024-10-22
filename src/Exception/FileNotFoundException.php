<?php

class FileNotFoundException extends RuntimeException
{
    public function __construct(string $filePath)
    {
        parent::__construct(sprintf('File "%s" not found.', $filePath));
    }
}
