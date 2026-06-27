<?php

namespace App\Services;

abstract class BaseService
{
    /**
     * Generate SHA-256 hash from given data.
     * Used for entity and image synchronization.
     */
    protected function generateHash(mixed $data): string
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }

        return hash('sha256', (string) $data);
    }
}