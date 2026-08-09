<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create($validated);

        $token = auth('api')->login($user);

        return response()->json([
            'message' => 'Inscription réussie',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!$token = auth('api')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => auth('api')->user(),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function profile()
    {
        return response()->json([
            'user' => auth('api')->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        $validated = $request->validate([
            'nom' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
            'password' => ['sometimes', 'string', 'min:8'],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profil mis à jour',
            'user' => $user->fresh(),
        ]);
    }
}