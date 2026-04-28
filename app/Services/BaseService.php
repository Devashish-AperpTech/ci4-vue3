<?php

namespace App\Services;

abstract class BaseService
{
    protected array $errors = [];
    
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }
    
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
    
    public function clearErrors(): void
    {
        $this->errors = [];
    }
    
    protected function respondWithError(string $message): array
    {
        return ['success' => false, 'message' => $message];
    }
    
    protected function respondWithSuccess(string $message, array $data = []): array
    {
        return array_merge(['success' => true, 'message' => $message], $data);
    }
}