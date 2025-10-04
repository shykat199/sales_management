<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as FortifyLogin;

class AuthController extends FortifyLogin
{
    public function create(Request $request): LoginViewResponse
    {

        return new class($request) implements LoginViewResponse {
            public function __construct(protected $request) {}

            public function toResponse($request)
            {
                if (\Auth::check()) {
                    return redirect()->route('dashboard');
                }
                return view('pages.auth.login');
            }
        };
    }
}
