<?php

declare(strict_types = 1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiException extends \Exception
{
    protected int $statusCode = 400;
    protected array $errors = [];
    protected string $type = 'api_error';

    public function __construct(
        string $message = 'Ошибка API',
        int $statusCode = 400,
        array $errors = [],
        ?Exception $previous = null
    ) {
        $this->statusCode = $statusCode;
        $this->errors = $errors;

        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'type' => $this->getType(),
                'message' => $this->getMessage(),
                'errors' => $this->getErrors(),
            ],
            'data' => null
        ], $this->getStatusCode());
    }
}