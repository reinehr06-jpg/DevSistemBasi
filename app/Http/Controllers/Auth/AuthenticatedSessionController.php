<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): \Illuminate\Http\Response
    {
        $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevSistemBasi - Login</title>
    <style>
        :root { --primary: #581c87; --primary-hover: #4c1d95; --background: #f8fafc; --surface: #ffffff; --text-main: #1e293b; --text-muted: #64748b; --border: #e2e8f0; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, sans-serif; }
        body { background: var(--background); display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-wrapper { display: flex; width: 100%; max-width: 1000px; height: 600px; background: var(--surface); border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05); overflow: hidden; margin: 20px; }
        .login-brand { flex: 1; background: linear-gradient(135deg, var(--primary), #3b0764); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; color: white; text-align: center; }
        .login-brand h1 { font-size: 2.5rem; font-weight: 800; margin-bottom: 10px; }
        .login-brand p { font-size: 1.1rem; opacity: 0.9; max-width: 300px; line-height: 1.5; }
        .login-form-container { flex: 1; padding: 60px; display: flex; flex-direction: column; justify-content: center; background: white; }
        .login-header { margin-bottom: 30px; }
        .login-header h2 { font-size: 1.8rem; color: var(--text-main); margin-bottom: 8px; }
        .login-header p { color: var(--text-muted); font-size: 0.95rem; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-main); margin-bottom: 8px; }
        .form-input { width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 1rem; outline: none; background: var(--background); }
        .form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(88,28,135,0.1); }
        .btn-submit { width: 100%; background: var(--primary); color: white; border: none; border-radius: 8px; padding: 14px; font-size: 1rem; font-weight: 600; cursor: pointer; margin-top: 10px; }
        .btn-submit:hover { background: var(--primary-hover); }
        .logo-placeholder { width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; font-size: 3rem; font-weight: bold; }
        @media (max-width: 768px) { .login-wrapper { flex-direction: column; height: auto; min-height: 100vh; margin: 0; border-radius: 0; } .login-brand { padding: 40px 20px; } .login-form-container { padding: 40px 24px; } }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-brand">
        <div class="logo-placeholder">D</div>
        <h1>DevSistemBasi</h1>
        <p>Orquestração de Desenvolvimento e Infraestrutura.</p>
    </div>
    <div class="login-form-container">
        <div class="login-header">
            <h2>Acesso Restrito</h2>
            <p>Insira suas credenciais para continuar.</p>
        </div>
        <form method="POST" action="/login">
            <input type="hidden" name="_token" value="'.csrf_token().'">
            <div class="form-group">
                <label class="form-label" for="email">E-mail</label>
                <input type="email" id="email" name="email" class="form-input" required autofocus placeholder="Digite seu e-mail">
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Senha</label>
                <input type="password" id="password" name="password" class="form-input" required placeholder="Digite sua senha">
            </div>
            <button type="submit" class="btn-submit">Entrar</button>
        </form>
    </div>
</div>
</body>
</html>';
        return response($html);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
