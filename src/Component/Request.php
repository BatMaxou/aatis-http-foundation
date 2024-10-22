<?php

namespace Aatis\HttpFoundation\Component;

use Aatis\HttpFoundation\Component\Bag\CookieBag;
use Aatis\HttpFoundation\Trait\MessageTrait;
use Aatis\HttpFoundation\Component\Bag\HeaderBag;
use Aatis\HttpFoundation\Component\Bag\ParameterBag;
use Aatis\HttpFoundation\Component\Bag\ServerBag;
use Aatis\HttpFoundation\Component\Bag\UploadedFileBag;
use Aatis\HttpFoundation\Component\File\UploadedFile;
use Aatis\HttpFoundation\Interface\MessageInterface;

class Request implements MessageInterface
{
    use MessageTrait;

    public CookieBag $cookies;
    public ParameterBag $query;
    public ParameterBag $request;
    public UploadedFileBag $files;
    public ServerBag $server;

    /**
     * @param array<string, mixed> $cookies
     * @param array<string, mixed> $query
     * @param array<string, mixed> $request
     * @param array<string, UploadedFile> $files
     * @param array<string, mixed> $server
     */
    final public function __construct(
        string $content = '',
        array $cookies = [],
        array $query = [],
        array $request = [],
        array $files = [],
        array $server = [],
    ) {
        $this->content = $content;
        $this->cookies = new CookieBag($cookies);
        $this->query = new ParameterBag($query);
        $this->request = new ParameterBag($request);
        $this->files = new UploadedFileBag($files);
        $this->server = new ServerBag($server);
        $this->headers = new HeaderBag($this->server->getHeaders());

        $serverProtocol = $this->server->get('SERVER_PROTOCOL');
        if ($serverProtocol && is_string($serverProtocol)) {
            $this->setProtocolVersion(str_replace('HTTP/', '', $serverProtocol));
        }
    }

    public static function createFromGlobals(): static
    {
        $files = [];
        foreach ($_FILES as $key => $file) {
            $files[$key] = new UploadedFile($file['tmp_name'], $file['name']);
        }

        return new static('', $_COOKIE, $_GET, $_POST, $files, $_SERVER);
    }
}
