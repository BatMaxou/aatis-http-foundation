<?php

namespace Aatis\HttpFoundation\Component;

class RedirectResponse extends Response
{
    public function __construct(string $url, int $status = 301, array $headers = [])
    {
        $urlHtml = htmlspecialchars($url);

        $template = sprintf(<<<HTML
                <!DOCTYPE html>
                <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta http-equiv="refresh" content=%s>

                        <title>Redirecting to %s</title>
                    </head>
                    <body>
                        Redirecting to %s
                    </body>
                </html>
            HTML, sprintf('0;url=%s', $urlHtml), $urlHtml, $urlHtml);

        parent::__construct($template, $status, $headers);

        $this->headers->set('Location', $url);
    }
}
