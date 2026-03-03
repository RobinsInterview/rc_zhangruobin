<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;

class NullAuthenticator implements AuthenticatorInterface
{
    public function authenticate(Request $request): AuthContext
    {
        return new AuthContext(authenticated: true, principal: 'anonymous');
    }
}
