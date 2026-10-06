<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">
    
<style>
    * {
        box-sizing: border-box;
    }

    html,
    body {
        margin: 0;
        min-height: 100%;
    }

    body {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px 20px;
        background:
            radial-gradient(
                circle at 50% 0%,
                rgba(99, 102, 241, 0.10),
                transparent 38%
            ),
            #f8fafc;
        color: #111827;
        font-family:
            Inter,
            ui-sans-serif,
            system-ui,
            -apple-system,
            BlinkMacSystemFont,
            "Segoe UI",
            sans-serif;
        transition:
            background-color 180ms ease,
            color 180ms ease;
    }

    .page {
        width: 100%;
        max-width: 560px;
    }

    .logo-wrapper {
        display: flex;
        justify-content: center;
        margin-bottom: 28px;
    }

    .logo {
        width: 82px;
        height: 82px;
        padding: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        box-shadow:
            0 10px 30px rgba(15, 23, 42, 0.08),
            0 2px 8px rgba(15, 23, 42, 0.04);
        transition:
            background-color 180ms ease,
            border-color 180ms ease,
            box-shadow 180ms ease;
    }

    .logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .card {
        overflow: hidden;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 24px;
        background: #ffffff;
        box-shadow:
            0 25px 50px -12px rgba(15, 23, 42, 0.10),
            0 4px 12px rgba(15, 23, 42, 0.04);
        transition:
            background-color 180ms ease,
            border-color 180ms ease,
            box-shadow 180ms ease;
    }

    .content {
        padding: 44px 40px 40px;
        text-align: center;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 11px;
        margin-bottom: 20px;
        border-radius: 999px;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .code {
        margin: 0;
        font-size: clamp(76px, 16vw, 128px);
        line-height: 0.9;
        font-weight: 900;
        letter-spacing: -0.07em;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .title {
        margin: 24px 0 0;
        color: #111827;
        font-size: 26px;
        line-height: 1.25;
        font-weight: 750;
        letter-spacing: -0.025em;
    }

    .message {
        max-width: 430px;
        margin: 14px auto 0;
        color: #6b7280;
        font-size: 15px;
        line-height: 1.7;
    }

    .footer {
        padding: 15px 24px;
        border-top: 1px solid #f1f5f9;
        background: #f8fafc;
        text-align: center;
        transition:
            background-color 180ms ease,
            border-color 180ms ease;
    }

    .footer span {
        color: #9ca3af;
        font-size: 12px;
    }

    @media (prefers-color-scheme: dark) {
        body {
            background:
                radial-gradient(
                    circle at 50% 0%,
                    rgba(99, 102, 241, 0.14),
                    transparent 38%
                ),
                #0b0f19;
            color: #f9fafb;
        }

        .logo {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow:
                0 14px 35px rgba(0, 0, 0, 0.30),
                0 0 0 1px rgba(255, 255, 255, 0.02);
        }

        .card {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.07);
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.40),
                0 4px 12px rgba(0, 0, 0, 0.20);
        }

        .badge {
            background: rgba(99, 102, 241, 0.14);
            color: #a5b4fc;
        }

        .title {
            color: #f9fafb;
        }

        .message {
            color: #9ca3af;
        }

        .footer {
            background: #0f172a;
            border-color: rgba(255, 255, 255, 0.06);
        }

        .footer span {
            color: #6b7280;
        }
    }

    @media (max-width: 520px) {
        body {
            padding: 24px 16px;
        }

        .content {
            padding: 36px 24px 30px;
        }

        .logo {
            width: 72px;
            height: 72px;
        }

        .title {
            font-size: 23px;
        }
    }
</style>

</head>

<body>

<main class="page">

<div class="logo-wrapper">
    <div class="logo">
        <img
            src="{{ asset('images/logo.png') }}"
            alt="{{ config('app.name') }}"
        >
    </div>
</div>

<section class="card">

    <div class="content">

        <div class="badge">
            Errore
        </div>

        <p class="code">
            @yield('code')
        </p>

        <h1 class="title">
            @yield('title')
        </h1>

        <p class="message">
            @yield('message')
        </p>

    </div>

    <footer class="footer">
        <span>
            {{ config('app.name') }}
        </span>
    </footer>

</section>
</main>

</body>
</html>
