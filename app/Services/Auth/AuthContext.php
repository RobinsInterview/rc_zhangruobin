<?php

namespace App\Services\Auth;

final class AuthContext
{
    public function __construct(
        public readonly bool $authenticated,
        public readonly string $principal,
    ) {
    }
}
