<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Mail\ResetPasswordCodeMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'no_telp' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('users', 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'no_telp' => $validated['no_telp'] ?? null,
            'password' => Hash::make($validated['password']),
            'foto' => $fotoPath,
            'role' => 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'token' => $token,
            'user' => new UserResource($user),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'login' => 'required|string', // username or email
            'password' => 'required|string',
        ]);

        $field = filter_var($validated['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!Auth::attempt([$field => $validated['login'], 'password' => $validated['password']])) {
            throw ValidationException::withMessages([
                'login' => ['Username/Email atau password salah.'],
            ]);
        }

        $user = User::where($field, $validated['login'])->first();
        
        // Revoke old tokens & create new token
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        Auth::guard('web')->logout();

        return response()->json(['message' => 'Logout berhasil']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }

    // Step 1: Request 6-Digit OTP Code & Send Real Email
    public function sendResetCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem BookStore.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate 6-Digit OTP Code
        $code = (string) rand(100000, 999999);

        // Store/Update code in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $code,
                'created_at' => Carbon::now(),
            ]
        );

        // Send Email
        try {
            Mail::to($user->email)->send(new ResetPasswordCodeMail($code, $user->name));
        } catch (\Exception $e) {
            // Log error if mail server fails
            logger()->error('Failed to send reset password email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Kode verifikasi 6 digit telah dikirim ke email ' . $user->email,
            'email' => $user->email,
        ]);
    }

    // Step 2: Verify 6-Digit OTP Code
    public function verifyResetCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
        ], [
            'code.size' => 'Kode verifikasi harus 6 digit angka.',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Kode verifikasi 6 digit salah atau tidak cocok.'], 422);
        }

        // Check 15 minutes expiration
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return response()->json(['message' => 'Kode verifikasi sudah kadaluarsa (lebih dari 15 menit). Silakan minta kode baru.'], 422);
        }

        return response()->json([
            'message' => 'Kode verifikasi 6 digit valid. Silakan buat password baru.',
            'email' => $request->email,
            'code' => $request->code,
        ]);
    }

    // Step 3: Set New Password
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Kode verifikasi tidak valid atau sudah digunakan.'], 422);
        }

        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return response()->json(['message' => 'Kode verifikasi telah kadaluarsa.'], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete token after successful reset
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Password berhasil diperbarui! Silakan login dengan password baru Anda.',
        ]);
    }
}
