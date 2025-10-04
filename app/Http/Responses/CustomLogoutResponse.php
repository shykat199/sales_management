<?php

namespace App\Http\Responses;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class CustomLogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        // Flash a toast message
        toast('Successfully logged out', 'success');

        return redirect()->route('login');
    }
}
