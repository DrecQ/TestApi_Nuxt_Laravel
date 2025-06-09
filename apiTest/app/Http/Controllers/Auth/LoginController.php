<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    //
    public function __construct(private readonly LoginUseCase $LoginUseCase)
    {

    }
    public function login(LoginRequest $request)
    {
        $response = $this->LoginUseCase->login($request);
    }
}
