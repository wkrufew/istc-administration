{{-- ═══════════════════════════════════════════════════════
     VERSIÓN 1 — LIGHT GLASS (Clara, blanca, etérea)
     Reemplaza todo el contenido de resources/views/auth/login.blade.php
════════════════════════════════════════════════════════ --}}

{{-- ═══════════════════════════════════════════════════════
     VERSIÓN 2 — DARK CHROMATIC GLASS
     Colores: verde #32620e · verdeclaro #7ea41e
              morado #84219f · naranja #e59e20 · azul #0369a1
     Reemplaza todo el contenido de resources/views/auth/login.blade.php
════════════════════════════════════════════════════════ --}}

<x-guest-layout>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap');

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        :root {
            --verde: #32620e;
            --verde-claro: #7ea41e;
            --morado: #84219f;
            --naranja: #e59e20;
            --azul: #0369a1;
        }

        .login-scene {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow: hidden;
            background: #050a0f;
            font-family: 'DM Sans', sans-serif;
        }

        /* ── Blobs de color de fondo ── */
        .blob {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .blob-1 {
            width: 520px;
            height: 520px;
            background: radial-gradient(circle, var(--verde) 0%, transparent 65%);
            opacity: 0.55;
            top: -180px;
            left: -140px;
            filter: blur(70px);
            animation: blobA 12s ease-in-out infinite alternate;
        }

        .blob-2 {
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, var(--morado) 0%, transparent 65%);
            opacity: 0.5;
            top: -60px;
            right: -120px;
            filter: blur(65px);
            animation: blobB 15s ease-in-out infinite alternate;
        }

        .blob-3 {
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, var(--naranja) 0%, transparent 65%);
            opacity: 0.45;
            bottom: -100px;
            left: -80px;
            filter: blur(60px);
            animation: blobC 11s ease-in-out infinite alternate;
        }

        .blob-4 {
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, var(--azul) 0%, transparent 65%);
            opacity: 0.5;
            bottom: -60px;
            right: -60px;
            filter: blur(55px);
            animation: blobD 9s ease-in-out infinite alternate;
        }

        .blob-5 {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, var(--verde-claro) 0%, transparent 65%);
            opacity: 0.35;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            filter: blur(50px);
            animation: blobE 18s ease-in-out infinite alternate;
        }

        @keyframes blobA {
            from {
                transform: translate(0, 0) scale(1)
            }

            to {
                transform: translate(35px, 25px) scale(1.08)
            }
        }

        @keyframes blobB {
            from {
                transform: translate(0, 0) scale(1)
            }

            to {
                transform: translate(-25px, 35px) scale(0.92)
            }
        }

        @keyframes blobC {
            from {
                transform: translate(0, 0) scale(1)
            }

            to {
                transform: translate(20px, -30px) scale(1.05)
            }
        }

        @keyframes blobD {
            from {
                transform: translate(0, 0) scale(1)
            }

            to {
                transform: translate(-30px, -15px) scale(1.1)
            }
        }

        @keyframes blobE {
            from {
                transform: translate(-50%, -50%) scale(0.9)
            }

            to {
                transform: translate(-50%, -50%) scale(1.3)
            }
        }

        /* ── Grain overlay sutil ── */
        .login-scene::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");
            opacity: 0.04;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Card principal ── */
        .glass-card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            background: rgba(5, 12, 20, 0.5);
            backdrop-filter: blur(32px) saturate(180%);
            -webkit-backdrop-filter: blur(32px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 28px;
            padding: 2.5rem 2.25rem 2.25rem;
            box-shadow:
                0 0 0 0.5px rgba(255, 255, 255, 0.05) inset,
                0 24px 70px rgba(0, 0, 0, 0.55),
                0 8px 32px rgba(0, 0, 0, 0.35);
            animation: cardIn 0.7s cubic-bezier(.23, 1, .32, 1) both;
        }

        /* Shimmer top */
        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 18%;
            right: 18%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.28), transparent);
            border-radius: 1px;
        }

        /* Glow bottom */
        .glass-card::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 25%;
            right: 25%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(126, 164, 30, 0.4), transparent);
            border-radius: 1px;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ── Logo ── */
        .logo-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        /* ── Títulos ── */
        .card-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.92);
            text-align: center;
            line-height: 1.15;
            margin-bottom: 0.35rem;
        }

        .card-sub {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.3);
            text-align: center;
            margin-bottom: 1.75rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        /* ── Divider con color ── */
        .color-bar {
            display: flex;
            gap: 4px;
            justify-content: center;
            margin-bottom: 1.75rem;
        }

        .color-bar span {
            height: 2px;
            border-radius: 2px;
            flex: 1;
            max-width: 50px;
            opacity: 0.7;
        }

        .cb1 {
            background: var(--verde);
        }

        .cb2 {
            background: var(--verde-claro);
        }

        .cb3 {
            background: var(--naranja);
        }

        .cb4 {
            background: var(--morado);
        }

        .cb5 {
            background: var(--azul);
        }

        /* ── Labels ── */
        .field-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 500;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--verde-claro);
            opacity: 0.75;
            margin-bottom: 0.4rem;
        }

        /* ── Inputs ── */
        .glass-input {
            width: 100%;
            padding: 0.82rem 1rem;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.09);
            background: rgba(255, 255, 255, 0.05);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.85);
            outline: none;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
        }

        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.2);
        }

        .glass-input:focus {
            border-color: rgba(126, 164, 30, 0.5);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(126, 164, 30, 0.1), 0 2px 12px rgba(0, 0, 0, 0.2);
        }

        /* ── Input con ojo ── */
        .input-wrap {
            position: relative;
        }

        .eye-btn {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            padding: 0 0.85rem;
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.25);
            cursor: pointer;
            transition: color 0.2s;
        }

        .eye-btn:hover {
            color: rgba(126, 164, 30, 0.8);
        }

        /* ── Remember / forgot ── */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 1rem 0;
            font-size: 0.8rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255, 255, 255, 0.35);
            cursor: pointer;
        }

        .remember-label input[type="checkbox"] {
            width: 15px;
            height: 15px;
            border-radius: 5px;
            accent-color: var(--verde-claro);
            cursor: pointer;
        }

        .forgot-link {
            color: rgba(126, 164, 30, 0.75);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.78rem;
            letter-spacing: 0.02em;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: var(--verde-claro);
            text-decoration: underline;
        }

        /* ── Botón submit ── */
        .btn-submit {
            width: 100%;
            padding: 0.9rem;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: linear-gradient(135deg,
                    rgba(50, 98, 14, 0.4) 0%,
                    rgba(3, 105, 161, 0.32) 50%,
                    rgba(132, 33, 159, 0.28) 100%);
            backdrop-filter: blur(10px);
            color: rgba(255, 255, 255, 0.88);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(.23, 1, .32, 1);
            box-shadow:
                0 0 0 0.5px rgba(126, 164, 30, 0.2) inset,
                0 4px 20px rgba(0, 0, 0, 0.3);
            margin-top: 0.25rem;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg,
                    rgba(50, 98, 14, 0.6) 0%,
                    rgba(3, 105, 161, 0.5) 50%,
                    rgba(132, 33, 159, 0.45) 100%);
            border-color: rgba(126, 164, 30, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(50, 98, 14, 0.3), 0 0 20px rgba(3, 105, 161, 0.15);
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .field-group {
            margin-bottom: 1.1rem;
        }

        /* ── Session status ── */
        .status-msg {
            margin-bottom: 1rem;
            font-size: 0.82rem;
            color: var(--verde-claro);
            padding: 0.6rem 0.9rem;
            background: rgba(126, 164, 30, 0.1);
            border: 1px solid rgba(126, 164, 30, 0.2);
            border-radius: 10px;
        }
    </style>

    <div class="login-scene">
        <!-- Blobs -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="blob blob-4"></div>
        <div class="blob blob-5"></div>

        <div class="glass-card" x-data="{ showPassword: false }">

            {{-- Logo --}}
            <div class="logo-wrap">
                <x-authentication-card-logo />
            </div>

            {{-- Título --}}
            <h2 class="card-title">Bienvenido a {{ \App\Services\SettingService::get('instituto.nombre_corto') ?: 'ISTC' }}</h2>
            <p class="card-sub">Ingresa tus credenciales</p>

            {{-- Color bar decorativa --}}
            <div class="color-bar">
                <span class="cb1"></span>
                <span class="cb2"></span>
                <span class="cb3"></span>
                <span class="cb4"></span>
                <span class="cb5"></span>
            </div>

            <x-validation-errors class="mb-4" />

            @session('status')
                <div class="status-msg">{{ $value }}</div>
            @endsession

            <form method="POST" action="{{ route('login') }}" style="margin-top:0;">
                @csrf

                {{-- Email --}}
                <div class="field-group">
                    <label for="email" class="field-label">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="username" class="glass-input" placeholder="ejemplo@correo.com">
                </div>

                {{-- Password --}}
                <div class="field-group">
                    <label for="password" class="field-label">Contraseña</label>
                    <div class="input-wrap">
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                            autocomplete="current-password" class="glass-input" style="padding-right:3rem;"
                            placeholder="••••••••">
                        <button type="button" class="eye-btn" @click="showPassword = !showPassword" tabindex="-1">
                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember / Forgot --}}
                <div class="remember-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember"> Recordarme
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">¿Olvidaste tu contraseña?</a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-submit">Iniciar Sesión</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    })
                    .fire({
                        icon: 'error',
                        title: "{{ session('error') }}"
                    });
            });
        @endif
    </script>
</x-guest-layout>
{{-- <x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                    autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ms-4">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}",
                });
            });
        @endif
    </script>
</x-guest-layout>
 --}}
