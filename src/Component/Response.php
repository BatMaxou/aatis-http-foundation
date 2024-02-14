<?php

namespace Aatis\HttpFoundation\Component;

use Aatis\HttpFoundation\Trait\MessageTrait;
use Aatis\HttpFoundation\Component\Bag\HeaderBag;
use Aatis\HttpFoundation\Interface\MessageInterface;

class Response implements MessageInterface
{
    use MessageTrait;

    private int $statusCode;
    private string $reasonPhrase;
    private string $charset = 'UTF-8';

    private const REASONS = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];

    /**
     * @param array<string, mixed> $headers
     */
    public function __construct(string $message = '', int $statusCode = 200, array $headers = [])
    {
        self::assertValidCode($statusCode);

        $this->statusCode = $statusCode;
        $this->reasonPhrase = self::REASONS[$statusCode];
        $this->initializeHeaders($headers);
        $this->setContent($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $code, string $reasonPhrase = ''): static
    {
        self::assertValidCode($code);

        $this->statusCode = $code;
        $this->reasonPhrase = '' === $reasonPhrase ? self::REASONS[$code] : $reasonPhrase;

        return $this;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase ?? '';
    }

    public function setCharset(string $charset): static
    {
        $this->charset = $charset;

        return $this;
    }

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function prepare(Request $request): static
    {
        $this->setProtocolVersion($request->getProtocolVersion());
        foreach ($request->headers->all() as $key => $value) {
            if ('Content-Type' === $key) {
                if (!$this->headers->has('Content-Type')) {
                    $this->headers->set($key, $value);
                }

                continue;
            }

            $this->headers->add($key, $value);
        }

        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', sprintf('text/html; charset=%s', $this->charset));
        }

        $this->headers->set('Cookie', $request->cookies->getInline());

        return $this;
    }

    public function send(): void
    {
        $this->sendHeaders();
        echo $this->getContent();
    }

    private function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        foreach ($this->headers->all() as $name => $values) {
            if (!is_array($values)) {
                $values = [$values];
            }

            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false, $this->statusCode);
            }
        }

        header(sprintf(
            'HTTP/%s %s %s',
            $this->protocol,
            $this->statusCode,
            $this->reasonPhrase
        ), true, $this->statusCode);
    }

    public function __toString()
    {
        return sprintf(
            "HTTP/%s %d %s\r\n%s\r\n%s",
            $this->protocol,
            $this->statusCode,
            $this->reasonPhrase,
            $this->headers,
            $this->getContent(),
        );
    }

    /**
     * @param array<string, mixed> $headers
     */
    private function initializeHeaders(array $headers): void
    {
        $this->headers = new HeaderBag();

        foreach ($headers as $name => $value) {
            $this->headers->set($name, $value);
        }
    }

    private static function assertValidCode(int $code): void
    {
        if (!isset(self::REASONS[$code])) {
            throw new \InvalidArgumentException('Invalid status code');
        }
    }
}
