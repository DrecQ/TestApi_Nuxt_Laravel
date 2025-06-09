<?php

namespace App\UseCases;

use App\Models\User;
use App\Exports\UsersExport;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserListRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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
     * Authentifie un utilisateur
     */
    public static function loginUser(LoginRequest $request): array
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return [
                'status' => false,
                'message' => 'Email ou mot de passe incorrect'
            ];
        }

        $user = User::where('email', $request->email)->firstOrFail();
        
        if (!$user->hasVerifiedEmail()) {
            return [
                'status' => false,
                'message' => 'Veuillez vérifier votre email avant de vous connecter'
            ];
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'status' => true,
            'message' => 'Connexion réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ];
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

    /**
     * Renvoie l'email de vérification à l'utilisateur
     */
    public static function resendEmailVerification(User $user): array
    {
        if ($user->hasVerifiedEmail()) {
            return [
                'status' => false,
                'message' => 'Adresse email déjà vérifiée.'
            ];
        }

        $user->sendEmailVerificationNotification();

        return [
            'status' => true,
            'message' => 'Email de verification envoyé à votre adresse',
            'user' => $user
        ];
    }

    /**
     * Exporte la liste des utilisateurs en Excel
     */
    public static function exportToExcel()
    {
        return Excel::download(new UsersExport, 'utilisateurs.xlsx');
    }

    /**
     * Exporte la liste des utilisateurs en PDF
     */
    public static function exportToPdf()
    {
        $users = User::all();
        $pdf = PDF::loadView('users.pdf', ['users' => $users]);
        
        return $pdf->download('utilisateurs.pdf');
    }
}