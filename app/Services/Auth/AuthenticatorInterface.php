<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;

interface AuthenticatorInterface
{
    public function authenticate(Request $request): AuthContext;
}
