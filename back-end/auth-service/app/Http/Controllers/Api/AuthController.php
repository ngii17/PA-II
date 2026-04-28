<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\SendOTPMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    /**
     * 1. REGISTER (Validasi Super Ketat & Simpan ke Cache)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'  => 'required|string|min:3|max:8|unique:users,username|regex:/^[A-Za-z]+[@$!%*?&]?$/',
            'full_name' => 'required|string|min:3|max:20|regex:/^[a-zA-Z\s]+$/',
            'email'     => 'required|string|email|unique:users,email',
            'phone'     => ['required', 'string', 'min:10', 'max:16', 'regex:/^\+62\d+$/'],
            'address'   => 'required|string',
            'password'  => [
                'required',
                'string',
                'min:8',
                'max:12',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/',
            ],
        ], [
            'username.regex'     => 'Username harus diawali huruf dan maksimal 1 simbol.',
            'full_name.regex'    => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'password.regex'     => 'Password wajib mengandung huruf besar, huruf kecil, angka, dan simbol.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'email.unique'       => 'Email ini sudah terdaftar. Silakan login.',
            'username.unique'    => 'Username ini sudah dipakai orang lain.',
            'phone.regex'        => 'Nomor HP wajib menggunakan format internasional (+62).',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $otp = rand(100000, 999999);
        $registerData = [
            'username'  => $request->username,
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password), 
            'phone'     => $request->phone,
            'address'   => $request->address,
            'otp'       => $otp
        ];

        Cache::put('reg_' . $request->email, $registerData, now()->addMinutes(3));

        try {
            Mail::to($request->email)->send(new SendOTPMail($otp));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Validasi sukses. Kode OTP berlaku 3 menit telah dikirim ke email.',
            'email'   => $request->email
        ], 200);
    }

    /**
     * 2. VERIFIKASI OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $cachedData = Cache::get('reg_' . $request->email);

        if (!$cachedData) {
            return response()->json(['success' => false, 'message' => 'Waktu habis atau data tidak ditemukan.'], 404);
        }

        if ($cachedData['otp'] != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Kode OTP salah!'], 401);
        }

        $user = User::create([
            'username'    => $cachedData['username'],
            'full_name'   => $cachedData['full_name'],
            'email'       => $cachedData['email'],
            'password'    => $cachedData['password'],
            'phone'       => $cachedData['phone'],
            'address'     => $cachedData['address'],
            'role_id'     => 2, 
            'is_verified' => true,
        ]);

        Cache::forget('reg_' . $request->email);

        return response()->json(['success' => true, 'message' => 'Registrasi Berhasil!'], 201);
    }

    /**
     * 3. LOGIN
     */
    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Email atau Password salah.'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'      => true,
            'access_token' => $token,
            'user'         => $user
        ], 200);
    }

    /**
     * 4. FORGOT PASSWORD
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email tidak ditemukan.'], 404);
        }

        $otp = rand(100000, 999999);
        Cache::put('reset_' . $request->email, $otp, now()->addMinutes(3));

        try {
            Mail::to($request->email)->send(new SendOTPMail($otp));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'OTP reset dikirim ke email.'], 200);
    }

    /**
     * 5. RESET PASSWORD
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'otp'      => 'required|digits:6',
            'password' => 'required|string|min:8|max:12|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $cachedOtp = Cache::get('reset_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['success' => false, 'message' => 'OTP salah atau kadaluwarsa.'], 401);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        Cache::forget('reset_' . $request->email);

        return response()->json(['success' => true, 'message' => 'Password berhasil diganti.']);
    }

    /**
     * 6. LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Berhasil logout.']);
    }

    /**
     * 7. AMBIL PROFIL (VERSI FIX FOTO BLANK)
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        // Kita gunakan url('/') agar link mengarah ke IP Laptop kamu, bukan localhost
        $photoUrl = $user->profile_photo 
            ? url('storage/profiles/' . $user->profile_photo) 
            : "https://ui-avatars.com/api/?name=" . urlencode($user->full_name) . "&background=0D8ABC&color=fff";

        return response()->json([
            'success' => true,
            'data'    => [
                'id'            => $user->id,
                'full_name'     => $user->full_name,
                'username'      => $user->username,
                'email'         => $user->email,
                'phone'         => $user->phone,
                'address'       => $user->address,
                'profile_photo' => $photoUrl,
                'created_at'    => $user->created_at->format('d M Y'),
            ]
        ], 200);
    }

    /**
     * 8. UPDATE PROFIL
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'username'  => 'required|string|min:3|max:8|unique:users,username,' . $user->id,
            'full_name' => 'required|string|min:3|max:20|regex:/^[a-zA-Z\s]+$/',
            'phone'     => ['required', 'string', 'min:10', 'max:16', 'regex:/^\+62\d+$/'],
            'address'   => 'required|string',
            'image'     => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            if ($request->hasFile('image')) {
                if ($user->profile_photo) {
                    Storage::delete('public/profiles/' . $user->profile_photo);
                }
                $file = $request->file('image');
                $fileName = time() . '_' . $user->username . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/profiles', $fileName);
                $user->profile_photo = $fileName;
            }

            $user->update([
                'username'  => $request->username,
                'full_name' => $request->full_name,
                'phone'     => $request->phone,
                'address'   => $request->address,
            ]);

            return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 9. HAPUS FOTO PROFIL (Langkah perbaikan Tombol Hapus)
     */
    public function deletePhoto(Request $request)
    {
        $user = $request->user();
        if ($user->profile_photo) {
            Storage::delete('public/profiles/' . $user->profile_photo);
            $user->update(['profile_photo' => null]);
            return response()->json(['success' => true, 'message' => 'Foto profil dihapus.']);
        }
        return response()->json(['success' => false, 'message' => 'Tidak ada foto untuk dihapus.'], 400);
    }
}