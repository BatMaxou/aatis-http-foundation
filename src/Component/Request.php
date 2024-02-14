<?php

namespace Aatis\HttpFoundation\Component;

use Aatis\HttpFoundation\Component\Bag\CookieBag;
use Aatis\HttpFoundation\Trait\MessageTrait;
use Aatis\HttpFoundation\Component\Bag\HeaderBag;
use Aatis\HttpFoundation\Component\Bag\ParameterBag;
use Aatis\HttpFoundation\Component\Bag\ServerBag;
use Aatis\HttpFoundation\Interface\MessageInterface;

class Request implements MessageInterface
{
    use MessageTrait;

    public CookieBag $cookies;
    public ParameterBag $query;
    public ParameterBag $request;
    public ParameterBag $files;
    public ServerBag $server;

    public const FORMAT = [
        'html' => ['text/html', 'application/xhtml+xml'],
        'txt' => ['text/plain'],
        'js' => ['application/javascript', 'application/x-javascript', 'text/javascript'],
        'css' => ['text/css'],
        'json' => ['application/json', 'application/x-json'],
        'jsonld' => ['application/ld+json'],
        'xml' => ['text/xml', 'application/xml', 'application/x-xml'],
        'rdf' => ['application/rdf+xml'],
        'atom' => ['application/atom+xml'],
        'rss' => ['application/rss+xml'],
        'form' => ['application/x-www-form-urlencoded', 'multipart/form-data'],
    ];

    /**
     * @param array<string, mixed> $cookies
     * @param array<string, mixed> $query
     * @param array<string, mixed> $request
     * @param array<string, mixed> $files
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
        $this->files = new ParameterBag($files);
        $this->server = new ServerBag($server);
        $this->headers = new HeaderBag($this->server->getHeaders());

        $serverProtocol = $this->server->get('SERVER_PROTOCOL');
        if ($serverProtocol && is_string($serverProtocol)) {
            $this->setProtocolVersion(str_replace('HTTP/', '', $serverProtocol));
        }
    }

    /**
     * @return string[]
     */
    public function getMimeTypes(string $format): array
    {
        return self::FORMAT[$format] ?? [];
    }

    public function getFormat(string $mimeType): string
    {
        foreach (self::FORMAT as $format => $mimeTypes) {
            if (in_array($mimeType, $mimeTypes)) {
                return $format;
            }
        }

        return '';
    }

    public static function createFromGlobals(): static
    {
        return new static('', $_COOKIE, $_GET, $_POST, $_FILES, $_SERVER);
    }
}
