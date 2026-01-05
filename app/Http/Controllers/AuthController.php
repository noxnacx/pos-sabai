<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ฟังก์ชันล็อกอิน
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // 👇 แก้ไขตรงนี้: ส่ง user (ที่มี role) กลับไปด้วย
            return response()->json([
                'message' => 'ยินดีต้อนรับครับ!',
                'user' => Auth::user()
            ]);
        }

        return response()->json(['message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'], 401);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'ออกจากระบบเรียบร้อย']);
    }
}
