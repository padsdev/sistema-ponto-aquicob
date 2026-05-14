<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\BrazilianCpf;
use App\Support\Cpf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(Request $request): RedirectResponse|View
    {
        if ($request->user() !== null) {
            return redirect()->route($request->user()->isAdmin() ? 'dashboard' : 'home');
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'cpf' => Cpf::digitsOnly((string) $request->input('cpf', '')),
        ]);

        $request->validate([
            'cpf' => ['required', 'string', 'digits:11', new BrazilianCpf],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($request->only('cpf', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'cpf' => 'Credenciais inválidas. Verifique o CPF e a senha.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route($request->user()->isAdmin() ? 'dashboard' : 'home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
