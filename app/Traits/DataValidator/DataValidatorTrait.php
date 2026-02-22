<?php

namespace App\Traits\DataValidator;

use Illuminate\Http\Request;

trait DataValidatorTrait
{
    protected function validateData(
        Request $request,
        array $rules,
        array $messages = []
    ): array {
        return $request->validate($rules, $messages);
    }
}
