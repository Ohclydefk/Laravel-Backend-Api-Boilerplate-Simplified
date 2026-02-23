<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse\ApiResponseTrait;
use App\Traits\DataValidator\DataValidatorTrait;
use Illuminate\Routing\Controller as LaravelController;

class BaseController extends LaravelController
{
    use ApiResponseTrait, DataValidatorTrait;
}
    