<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\UseCases\UserUseCase;

class ApiController extends Controller
{
    public function register(UserStoreRequest $request)
    {
        $result = UserUseCase::createUser($request);
        return response()->json($result, $result['status'] ? 201 : 500);
    }

    public function login(LoginRequest $request)
    {
        $result = UserUseCase::loginUser($request);
        return response()->json($result, $result['status'] ? 200 : 401);
    }

    public function profile()
    {
        $result = UserUseCase::getUserProfile(auth()->user());
        return response()->json($result);
    }

    public function logout()
    {
        $result = UserUseCase::logoutUser(auth()->user());
        return response()->json($result);
    }

    public function refreshToken()
    {
        $result = UserUseCase::refreshUserToken(auth()->user());
        return response()->json($result);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $result = UserUseCase::forgotUserPassword($request);
        return response()->json($result, $result['status'] ? 200 : 400);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $result = UserUseCase::resetUserPassword($request);
        return response()->json($result, $result['status'] ? 200 : 400);
    }

    public function index()
    {
        $users = User::all();
        return response()->json([
            'status' => true,
            'users' => $users
        ]);
    }

    public function emailVerify($user_id, Request $request)
    {
        if (! $request->hasValidSignature()) {
           
            return redirect()->to(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/verify-email?message=invalid_link');
        }

        $user = User::find($user_id);

        if (!$user) {
            
            return redirect()->to(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/verify-email?message=user_not_found');
        }

        if ($user->hasVerifiedEmail()) {
            // Rediriger vers une page frontend indiquant que l'email est déjà vérifié
            return redirect()->to(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/verify-email?message=already_verified');
        }

        $user->markEmailAsVerified();

        // Rediriger vers une page frontend de succès après la vérification
        return redirect()->to(env('FRONTEND_URL', 'http://localhost:3000') . '/?message=success');
    }

    public function resendEmailVerificationMail(Request $request)
    {
        $user_id = $request->input('user_id');

        $user = User::findOrFail($user_id);

        if (! $user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé.',
            ], 404);
        }

        
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Adresse email déjà vérifiée.',
            ]);
        }

        
        $user->sendEmailVerificationNotification();


        // Réponse de succès
        return response()->json([
            "status" => true,
            "message" => "Email de verification envoyé à votre adresse",
            "user" => $user
        ], 201);

    }
}
