<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\UseCases\UserUseCase;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserListRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct(private readonly UserUseCase $userUseCase)
    {

    }

    // Lister tous les utilisateurs
    public function index(UserListRequest $request)
    {
        return $this->userUseCase->findUsers($request);
    }

    // Créer un utilisateur
    public function store(UserStoreRequest $request)
    {
        $user = $this->userUseCase->createUser($request);
        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user
        ], 201);
    }

    // Afficher un utilisateur spécifique
    public function show($id)
    {
        $user = $this->userUseCase->findUser($id);
        return response()->json($user);
    }

    // Mettre à jour un utilisateur
    public function update(UserStoreRequest $request, $id)
    {
        $user = $this->userUseCase->updateUser($request, $id);
        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'user' => $user
        ]);
    }

    // Supprimer un utilisateur
    public function destroy($id)
    {
        $this->userUseCase->deleteUser($id);
        return response()->json([
            'message' => 'Utilisateur supprimé avec succès'
        ]);
    }

    /**
     * Renvoie l'email de vérification à l'utilisateur
     */
    public function resendEmailVerificationMail(User $user)
    {
        $result = $this->userUseCase->resendEmailVerification($user);
        return response()->json($result, $result['status'] ? 201 : 200);
    }

    /**
     * Exporte la liste des utilisateurs en Excel
     */
    public function exportExcel()
    {
        return $this->userUseCase->exportToExcel();
    }

    /**
     * Exporte la liste des utilisateurs en PDF
     */
    public function exportPdf()
    {
        return $this->userUseCase->exportToPdf();
    }
}
