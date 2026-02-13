<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    protected array $context = [];
    protected string $userMessage;
    protected int $httpCode;

    public function __construct(
        string $userMessage,
        ?string $logMessage = null,
        int $httpCode = 422,
        array $context = [],
        ?\Throwable $previous = null,
    ) {
        $this->userMessage = $userMessage;
        $this->httpCode = $httpCode;
        $this->context = $context;

        parent::__construct($logMessage ?? $userMessage, 0, $previous);
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
