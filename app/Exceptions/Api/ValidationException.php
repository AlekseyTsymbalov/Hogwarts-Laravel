<?php

declare(strict_types = 1);

namespace App\Exceptions\Api;

use App\Exceptions\Api\ApiException;

class ValidationException extends ApiException
{
    protected string $type = 'validation_error';

    public function __construct(array $errors = [])
    {
        parent::__construct('Ошибка валидации', 422, $errors);
    }
}