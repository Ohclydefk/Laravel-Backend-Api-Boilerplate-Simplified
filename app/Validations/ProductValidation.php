<?php

namespace App\Validations;

class ProductValidation
{
    public static function store(): array
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0']
        ];
    }

    public static function update(int $id): array
    {
        return [
            'name' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0']
        ];
    }
}
