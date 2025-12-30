<?php

declare(strict_types = 1);

namespace App\Exceptions\Api;

use App\Exceptions\Api\ApiException;

class AuthenticationException extends ApiException
{
    protected string $type = 'authentication_error';

    public function __construct(
        string $message = 'Требуется авторизация',
        array $errors = []
    ) {
        parent::__construct($message, 401, $errors);
    }
}