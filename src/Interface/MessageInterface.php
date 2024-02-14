<?php

namespace Aatis\HttpFoundation\Interface;

interface MessageInterface
{
    public function getProtocolVersion(): string;

    public function setProtocolVersion(string $version): static;

    public function getContent(): string;

    public function setContent(string $content): static;
}
