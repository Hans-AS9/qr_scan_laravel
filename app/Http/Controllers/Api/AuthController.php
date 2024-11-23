<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;

use function Laravel\Prompts\error;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        //validation

        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required',
                'password' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'validation errors',
                'errors' => $validator->errors(),
                'data' => []

            ]);
        }

        //cek username ada atau tidak didatabase
        $user = User::where('username', $request->username)->first();

        //Jika user tidak ditemukan atau password salah
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'User/Password salah', //pesan kesalahan

            ],);
        }

        //Jika user ditemukan dan password benar, generate token
        $token = $user->createToken('auth_token')->plainTextToken;


        //Kembalikan response berhasil login
        return response()->json([
            'status' => 'success',
            'message' => 'OK',
            'data' => [
                'id' => $user->id,
                'nama' => $user->name,
                'token' => $token
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        //Mendapatkkan user yang sedang login dari token yang dikirim
        $user = $request->user();

        if ($user) {
            //menghapus semua token pengguna (log out dari semua perangkat)
            $user->tokens()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout berhasil',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan atau sudah logout',
            ], 404);
        }
    }
}
