<?php

namespace App\Validations;

class AddressValidation
{
    public static function store(): array
    {
        return [
            'label' => ['required', 'string'],
            'street' => ['required', 'string'],
            'barangay' => ['required', 'string'],
            'city' => ['required', 'string'],
            'province' => ['required', 'string'],
            'postal_code' => ['required', 'string'],
            'country' => ['required', 'string'],
        ];
    }

    public static function update(int $id): array
    {
        return [
            'label' => ['sometimes', 'string'],
            'street' => ['sometimes', 'string'],
            'barangay' => ['sometimes', 'string'],
            'city' => ['sometimes', 'string'],
            'province' => ['sometimes', 'string'],
            'postal_code' => ['sometimes', 'string'],
            'country' => ['sometimes', 'string']
        ];
    }
}
