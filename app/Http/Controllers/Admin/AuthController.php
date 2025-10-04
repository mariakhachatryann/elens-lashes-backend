<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        $remember = $request->boolean('remember');

        $validationErrors = $this->authService->validateCredentials($credentials);
        if (!empty($validationErrors)) {
            return back()->withErrors($validationErrors)->withInput($request->only('email'));
        }

        $result = $this->authService->attemptLogin($credentials, $remember);

        if (!$result['success']) {
            return back()->withErrors([
                $result['error'] => $result['message'],
            ])->withInput($request->only('email'));
        }

        $request->session()->regenerate();
        return redirect()->intended(route('admin.services.index'));
    }

    public function logout(Request $request)
    {
        $this->authService->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
