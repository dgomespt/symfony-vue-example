<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;

final class ErrorResponse implements \JsonSerializable
{

    private int $status = Response::HTTP_BAD_REQUEST;

    /**
     * @param string $message
     * @var array $errors
     */
    public function __construct(
        private readonly string $message,
        private readonly array $errors
    )
    {
    }


    public function jsonSerialize(): array
    {
        return [
            'message' => $this->message,
            'errors' => $this->errors
        ];
    }

    public function getStatus(): int
    {
        return $this->status;
    }



    public function getMessage(): string
    {
        return $this->message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }


}