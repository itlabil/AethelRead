<?php

namespace App\Contracts;

interface HashableContract
{
    /**
     * Generate a hash representing the current state of the object.
     * Used for Smart Synchronization between Android and Backend.
     */
    public function generateHash(): string;
}