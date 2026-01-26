<?php

namespace App\Support\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * Положительный ответ (код 200, можно переопределить)
     *
     * @param  mixed  $data
     * @param  int  $statusCode
     * @return JsonResponse
     */
    public function successResponse(mixed $data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['data' => $data], $statusCode);
    }

    /**
     * Ошибка (код 500)
     *
     * @param mixed $errors
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function errorResponse(mixed $errors = [], string $message = '', int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        if (!$message) {
            $message = Response::$statusTexts[$statusCode];
        }

        $data = [
            'message' => $message,
            'errors' => $errors,
        ];

        return new JsonResponse($data, $statusCode);
    }

    /**
     * Положительный ответ (код 200)
     *
     * @param  mixed  $data
     * @return JsonResponse
     */
    public function okResponse(mixed $data): JsonResponse
    {
        return $this->successResponse($data);
    }

    /**
     * Успешное создание ресурса (код 201)
     *
     * @param  mixed  $data
     * @return JsonResponse
     */
    public function createdResponse(mixed $data): JsonResponse
    {
        return $this->successResponse($data, Response::HTTP_CREATED);
    }

    /**
     * Положительный ответ без контента (код 204)
     *
     * @return JsonResponse
     */
    public function noContentResponse(): JsonResponse
    {
        return $this->successResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Не авторизован (код 401)
     *
     * @param  mixed  $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function unauthorizedResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, $message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Недостаточно прав для доступа к контенту (код 403)
     *
     * @param  mixed  $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function forbiddenResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, $message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Код 404
     *
     * @return JsonResponse
     */
    public function notFoundResponse(): JsonResponse
    {
        return $this->errorResponse(statusCode: Response::HTTP_NOT_FOUND);
    }

    /**
     * Недопустимый контент в запросе (код 422)
     *
     * @param  mixed  $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function unprocessableResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, $message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
