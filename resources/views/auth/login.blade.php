<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingreso | NATADINATTA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; background: linear-gradient(135deg, #0f172a, #2563eb, #dbeafe); }
        .login-card { border: 0; border-radius: 1.5rem; box-shadow: 0 20px 60px rgba(15, 23, 42, .18); }
    </style>
</head>
<body class="d-flex align-items-center">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card login-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="text-center mb-4">
                        <div class="badge text-bg-primary mb-3">Sistema de Ventas</div>
                        <h1 class="h3 fw-bold mb-2">NATADINATTA</h1>
                        <p class="text-muted mb-0">Ingresa con tu usuario para gestionar clientes, ventas y facturas.</p>
                    </div>

                    @include('partials.alerts')

                    <form method="POST" action="{{ route('login.attempt') }}" class="d-grid gap-3">
                        @csrf
                        <div>
                            <label for="email" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div>
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Recordar sesión</label>
                        </div>
                        <button class="btn btn-primary btn-lg">Iniciar sesión</button>
                    </form>

                    <div class="mt-4 p-3 bg-light rounded">
                        <div class="small text-muted">Acceso inicial sugerido</div>
                        <div class="fw-semibold">admin@natadinatta.com / password</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
