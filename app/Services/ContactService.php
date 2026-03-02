<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cliente;

final class ContactService
{
    /** @param array<string, mixed> $data */
    public function create(array $data): Cliente
    {
        return Cliente::create($data);
    }
}
