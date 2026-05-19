<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Simple CRM') }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #0f172a, #111827);
            color: #ffffff;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
        }

        .card {
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 32px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 42px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.35);
        }

        .badge {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 44px;
            line-height: 1.1;
            margin: 0 0 18px;
        }

        p {
            color: #cbd5e1;
            font-size: 17px;
            line-height: 1.7;
            margin: 0 0 24px;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 28px;
        }

        .btn {
            display: inline-block;
            padding: 13px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
        }

        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .features {
            display: grid;
            gap: 14px;
        }

        .feature {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            padding: 18px;
        }

        .feature strong {
            display: block;
            font-size: 16px;
            margin-bottom: 6px;
        }

        .feature span {
            color: #cbd5e1;
            font-size: 14px;
            line-height: 1.5;
        }

        @media (max-width: 800px) {
            .card {
                grid-template-columns: 1fr;
                padding: 28px;
            }

            h1 {
                font-size: 34px;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="card">
            <div>
                <div class="badge">Simple CRM Project</div>

                <h1>Simple CRM for managing customers, leads and tasks.</h1>

                <p>
                    A practical CRM web application built with PHP, Laravel, database migrations,
                    authentication, role-based access control and deployment experience.
                </p>

                <div class="actions">
                    <a class="btn btn-primary" href="{{ route('login') }}">Log in</a>
                    <a class="btn btn-secondary" href="{{ route('register') }}">Create account</a>
                </div>
            </div>

            <div class="features">
                <div class="feature">
                    <strong>Customer Management</strong>
                    <span>Manage customers, leads and business records in one place.</span>
                </div>

                <div class="feature">
                    <strong>Authentication</strong>
                    <span>User login, registration and secure access flow.</span>
                </div>

                <div class="feature">
                    <strong>RBAC</strong>
                    <span>Role-based access control for different user permissions.</span>
                </div>

                <div class="feature">
                    <strong>Deployment Ready</strong>
                    <span>Docker-based deployment experience on Render.</span>
                </div>
            </div>
        </section>
    </main>
</body>
</html>