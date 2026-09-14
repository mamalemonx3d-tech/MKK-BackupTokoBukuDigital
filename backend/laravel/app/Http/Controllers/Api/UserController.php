<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::orderBy('id', 'desc')->get();
        return response()->json([
            'data' => UserResource::collection($users),
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => new UserResource($user)]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_telp' => 'nullable|string|max:20',
            'role' => 'required|in:admin,user',
            'password' => 'nullable|string|min:6',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $validated['foto'] = $request->file('foto')->store('users', 'public');
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Data pengguna berhasil diperbarui',
            'data' => new UserResource($user),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Tidak dapat menghapus akun sendiri'], 400);
        }

        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->delete();

        return response()->json(['message' => 'Pengguna berhasil dihapus']);
    }
}
