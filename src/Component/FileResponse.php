<?php

namespace Aatis\HttpFoundation\Component;

use Aatis\HttpFoundation\Component\File\File;
use Aatis\HttpFoundation\Interface\FileInterface;

class FileResponse extends Response
{
    public function __construct(FileInterface|string $file, int $status = 200, array $headers = [])
    {
        if (is_string($file)) {
            $file = new File($file);
        }

        $file->rewind();
        parent::__construct($file->getContents(), $status, $headers);

        $this->headers
            ->set('Content-Type', mime_content_type($file->getPathname()) ?: 'application/octet-stream')
            ->set('Content-Length', (string) ($file->getSize() ?: 0))
            ->set('Content-Disposition', sprintf('attachment; filename="%s"', $file->getFilename()))
        ;
    }

    public function setContent(string $content): static
    {
        throw new \LogicException('The content cannot be set on a FileResponse instance.');
    }
}
