<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function attemptLogin(array $credentials, bool $remember = false): array
    {
        try {
            $admin = Admin::where('email', $credentials['email'])->first();

            if (!$admin) {
                return [
                    'success' => false,
                    'error' => 'email',
                    'message' => 'No admin found with this email address.',
                ];
            }

            if (!Hash::check($credentials['password'], $admin->password)) {
                return [
                    'success' => false,
                    'error' => 'email',
                    'message' => 'The provided credentials are incorrect.',
                ];
            }

            Auth::guard('admin')->login($admin, $remember);

            return [
                'success' => true,
                'admin' => $admin,
            ];

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'email',
                'message' => 'An error occurred during login. Please try again.',
            ];
        }
    }

    public function logout(): void
    {
        Auth::guard('admin')->logout();
    }

    public function validateCredentials(array $credentials): array
    {
        $errors = [];

        if (empty($credentials['email'])) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please provide a valid email address.';
        }

        if (empty($credentials['password'])) {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($credentials['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        return $errors;
    }
}
