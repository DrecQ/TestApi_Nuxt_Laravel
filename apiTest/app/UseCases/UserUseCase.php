<?php

namespace App\UseCases;

use App\Models\User;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserListRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserUseCase
{

    public function findUsers(UserListRequest $request)
    {
        $perPage = $request->input('per_page', 10);
        $users = User::query()->paginate($perPage);

        return UserListResource::collection($users);
    }

    /**
     * Crée un nouvel utilisateur
     */
    public static function createUser(UserStoreRequest $request): User
    {
        $user = new User;

        $user->identifiant = $request->input('identifiant');
        $user->lastname = $request->input('lastname');
        $user->firstname = $request->input('firstname');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->role = $request->input('role');
        $user->remember_token = Str::random(10);
        $user->email_verified_at = now();

        $user->save();
        return $user;
    }

    /**
     * Récupère un utilisateur par son ID
     */
    public static function findUser(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Met à jour un utilisateur
     */
    public static function updateUser(UserStoreRequest $request, int $id): User
    {
        $user = User::findOrFail($id);
        $validated = $request->validated();

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return $user;
    }

    /**
     * Supprime un utilisateur
     */
    public static function deleteUser(int $id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }
}