<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessRuleException extends Exception
{
    protected ?string $errorKey;
    protected array $contextData;

    public function __construct(string $message = 'Operación no permitida por regla de negocio.', ?string $errorKey = null, array $contextData = [], int $code = 422)
    {
        parent::__construct($message, $code);
        $this->errorKey = $errorKey;
        $this->contextData = $contextData;
    }

    public function getErrorKey(): ?string
    {
        return $this->errorKey;
    }

    public function getContextData(): array
    {
        return $this->contextData;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $this->getMessage(),
            'code' => $this->errorKey,
            'details' => $this->contextData,
        ], $this->getCode() ?: 422);
    }
}
