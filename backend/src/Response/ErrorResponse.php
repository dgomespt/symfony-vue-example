<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;

final class ErrorResponse implements \JsonSerializable
{

    public int $status = Response::HTTP_BAD_REQUEST;

    /**
     * @param string $message
     * @var array $errors
     */
    public function __construct(
        private string $message,
        private array  $errors
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
}