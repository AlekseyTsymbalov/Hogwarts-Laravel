<?php

declare(strict_types = 1);

namespace App\Exceptions\Api;

use App\Exceptions\Api\ApiException;

class NotFoundException extends ApiException
{
    protected string $type = 'not_found';

    public function __construct(
        string $message = 'Ресурс не найден',
        array $errors = []
    ) {
        parent::__construct($message, 404, $errors);
    }
}