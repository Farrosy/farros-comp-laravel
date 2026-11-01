<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $email = $request->input('email');

        $request->merge([
            'email' => is_string($email) ? mb_strtolower(trim($email)) : '',
        ]);

        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang Admin!');
            }

            return redirect()->intended('/')->with('success', 'Berhasil masuk ke akun Anda.');
        }

        return back()->withErrors([
            'login' => 'Email atau kata sandi yang Anda masukkan salah. Periksa kembali lalu coba lagi.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $address = $request->input('address');

        $phone = preg_replace('/[\s().-]+/', '', is_string($phone) ? $phone : '');
        $phone = preg_replace('/^\+?62/', '0', $phone);

        $request->merge([
            'name' => is_string($name) ? trim($name) : '',
            'email' => is_string($email) ? mb_strtolower(trim($email)) : '',
            'phone' => $phone,
            'address' => is_string($address) ? trim($address) : '',
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:13', 'regex:/^08[1-9][0-9]{7,10}$/'],
            'address' => ['required', 'string', 'max:2000'],
            'password' => [
                'required',
                'string',
                Password::min(15)->max(72),
                function ($attribute, $value, $fail) {
                    if (is_string($value) && strlen($value) > 72) {
                            $fail('Kata sandi terlalu panjang. Gunakan maksimal 72 karakter.');
                    }
                },
            ],
            'password_confirmation' => ['required', 'same:password'],
            'privacy_notice' => ['accepted'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'email.max' => 'Alamat email maksimal 255 karakter.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk dengan akun Anda.',
            'phone.required' => 'Nomor WhatsApp/HP wajib diisi.',
            'phone.regex' => 'Masukkan nomor HP Indonesia yang valid, misalnya 081234567890 atau +62 812 3456 7890.',
            'address.required' => 'Alamat pengiriman wajib diisi.',
            'address.max' => 'Alamat maksimal 2.000 karakter.',
            'password.min' => 'Gunakan kata sandi minimal 15 karakter.',
            'password.max' => 'Kata sandi maksimal 72 karakter.',
            'password_confirmation.same' => 'Konfirmasi kata sandi belum cocok.',
            'privacy_notice.accepted' => 'Baca dan setujui pemberitahuan privasi untuk melanjutkan.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Akun berhasil dibuat. Selamat datang di Farros Comp!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah berhasil keluar.');
    }
}
