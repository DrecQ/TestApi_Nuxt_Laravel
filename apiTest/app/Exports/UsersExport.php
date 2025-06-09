<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return User::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Identifiant',
            'Nom',
            'Prénom',
            'Email',
            'Rôle',
            'Date de création',
            'Dernière modification'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->identifiant,
            $user->lastname,
            $user->firstname,
            $user->email,
            $user->role,
            $user->created_at->format('d/m/Y H:i'),
            $user->updated_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
} 