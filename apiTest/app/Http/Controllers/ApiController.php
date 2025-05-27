<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            "lastname" => "required|string|max:200",
            "firstname"  => "required|string|max:200",
            "email" => "required|string|email|unique:users",
            "role" => "string|max:20",
            "password" => "required|string|confirmed"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors()
            ], 422);
        }

        // Création de l'utilisateur
            $user = User::create([
                "lastname" => $request->lastname,
                "firstname" => $request->firstname,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "identifiant" => "USER-" . strtoupper(Str::random(8))
            ]);

            $user->sendEmailVerificationNotification();


        // Réponse de succès
        return response()->json([
            "status" => true,
            "message" => "Utilisateur enregistré avec succès",
            "user" => $user
        ], 201);
    }

    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->first(), 
            ], 422); 
        }

        
        $user = User::where('email', $request->email)->first();

        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'L’e-mail ou le mot de passe est incorrect.',
            ], 401); 
        }

        
        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'status' => false,
                'message' => 'Votre email n\'est pas vérifié. Veuillez vérifier votre boîte de réception.',
            ], 403); 
        }

        
        $token = $user->createToken('api_token')->plainTextToken;

        
        return response()->json([
            'status' => true,
            'message' => 'Connexion réussie.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                
            ],
        ], 200); 
    }

     // Profile (GET, Auth, Token)
    public function profile()
    {
        $userData = auth()->user();

        return response()->json([
            'status' => true,
            'message' => 'Le profil utilisateur a été récupéré avec succès.',
            'user' => $userData
        ]);
    }

         // Logout (GET, Auth, Token)
        public function logout()
        {
                 // To get all tokens of logged in user and delete that

                 request()->user()->tokens()->delete();

                 return response()->json([
                    "status" => true,
                    "message" => "Utilisateur déconnecté"
                 ]);

        }


    // Refresh Token (GET or POST, Auth, Token)
    public function refreshToken()
    {
        // Delete the old token
        request()->user()->currentAccessToken()->delete();

        // Create a new token
        $tokenInfo = request()->user()->createToken('newApi-token');
        $newToken = $tokenInfo->plainTextToken;

        return response()->json([
            "status" => true,
            "message" => "Nouveau token généré avec succès.",
            "access_token" => $newToken
        ]);
    }


        public function emailVerify($user_id, Request $request)
        {
            if (! $request->hasValidSignature()) {
               
                return redirect()->to(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/email-verification-error?message=invalid_link');
            }

            $user = User::find($user_id);

            if (!$user) {
                
                return redirect()->to(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/email-verification-error?message=user_not_found');
            }

            if ($user->hasVerifiedEmail()) {
                // Rediriger vers une page frontend indiquant que l'email est déjà vérifié
                return redirect()->to(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/email-verified-success?message=already_verified');
            }

            $user->markEmailAsVerified();

            // Rediriger vers une page frontend de succès après la vérification
            return redirect()->to(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/email-verified-success?message=success');
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

       public function forgotPassword(Request $request)
        {
            // Valide que l'email est bien envoyé et dans un format correct
            $request->validate([
                'email' => 'required|email',
            ]);

            // Tente d'envoyer le lien de réinitialisation par email
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'message' => 'Lien de réinitialisation envoyé à votre adresse email.'
                ]);
            } else {
                return response()->json([
                    'message' => 'Impossible d\'envoyer le lien de réinitialisation.'
                ], 400);
            }
        }

        public function resetPassword(Request $request)
        {
            // Validation des champs requis
            $request->validate([
                'email' => 'required|email',
                'token' => 'required|string',
                'password' => 'required|string|min:8|confirmed', // attend password_confirmation
            ]);

            // Tentative de réinitialisation
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->save();
                }
            );

            if ($status == Password::PASSWORD_RESET) {
                return response()->json(['message' => 'Mot de passe réinitialisé avec succès.']);
            }

            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        }

        public function index()
    {
        // Récupérer tous les utilisateurs
        $users = User::all();

        return response()->json([
            'status' => true,
            'users' => $users
        ]);
    }



}
