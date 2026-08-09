<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Kovo Backend API",
    version: "1.0.0",
    description: "API backend du projet Kovo (Laravel) — authentification JWT"
)]
#[OA\Server(
    url: "https://kovo-backend-0pmr.onrender.com",
    description: "Production (Render)"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/register",
        tags: ["Auth"],
        summary: "Créer un nouvel utilisateur",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nom", "email", "password"],
                properties: [
                    new OA\Property(property: "nom", type: "string", example: "Jean Kouassi"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "jean@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", minLength: 8, example: "motdepasse123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Inscription réussie",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Inscription réussie"),
                        new OA\Property(property: "user", properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "nom", type: "string", example: "Jean Kouassi"),
                            new OA\Property(property: "email", type: "string", example: "jean@example.com"),
                        ], type: "object"),
                        new OA\Property(property: "token", type: "string", example: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
                        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Erreur de validation"),
        ]
    )]
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

    #[OA\Post(
        path: "/api/login",
        tags: ["Auth"],
        summary: "Connecter un utilisateur",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "jean@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "motdepasse123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Connexion réussie",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Connexion réussie"),
                        new OA\Property(property: "user", properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "nom", type: "string", example: "Jean Kouassi"),
                            new OA\Property(property: "email", type: "string", example: "jean@example.com"),
                        ], type: "object"),
                        new OA\Property(property: "token", type: "string", example: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
                        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Identifiants incorrects"),
        ]
    )]
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

    #[OA\Get(
        path: "/api/profile",
        tags: ["Auth"],
        summary: "Récupérer le profil de l'utilisateur connecté",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Profil utilisateur",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "user", properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "nom", type: "string", example: "Jean Kouassi"),
                            new OA\Property(property: "email", type: "string", example: "jean@example.com"),
                        ], type: "object"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Non authentifié"),
        ]
    )]
    public function profile()
    {
        return response()->json([
            'user' => auth('api')->user(),
        ]);
    }

    #[OA\Put(
        path: "/api/profile",
        tags: ["Auth"],
        summary: "Mettre à jour le profil de l'utilisateur connecté",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nom", type: "string", example: "Jean K. Kouassi"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "jean.kouassi@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", minLength: 8, example: "nouveaumotdepasse"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Profil mis à jour",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Profil mis à jour"),
                        new OA\Property(property: "user", properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "nom", type: "string", example: "Jean K. Kouassi"),
                            new OA\Property(property: "email", type: "string", example: "jean.kouassi@example.com"),
                        ], type: "object"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 422, description: "Erreur de validation"),
        ]
    )]
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