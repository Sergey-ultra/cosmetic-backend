<?php

declare(strict_types=1);

namespace App\Services\CurlService;


class CurlResponse
{
    protected int $code;
    protected array $headers;
    protected string $status;
    protected string $body;
    protected string $errors;

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function setHeader(string $headerRaw): void
    {
        $headers_raw = explode("\r\n", $headerRaw);

        $status_row = array_shift($headers_raw);
        $statusRowArray = explode(' ', $status_row);
        if (count($statusRowArray) > 1) {
            $this->status = $statusRowArray[1];
        }



        foreach ($headers_raw as $header) {
            if (strpos($header, ':') !== false) {
                [$k, $v] = explode(':', $header, 2);
                $this->headers[$k] = $v;
            }
        }
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function setErrors(string $errors): void
    {
        $this->errors = $errors;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getErrors(): string
    {
        return $this->errors;
    }
}
