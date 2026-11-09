<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // Menampilkan informasi profil pengguna
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'message' => 'Data profil berhasil diambil.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ],
        ]);
    }

    // Memperbarui nama dan email pengguna
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    // Mengubah password pengguna
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return response()->json([
            'message' => 'Password berhasil diperbarui.',
        ]);
    }
}
