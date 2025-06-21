<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="shortcut icon" href="./images/favicon.png" type="image/x-icon">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', Arial, sans-serif;
        }
        .container {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(60,60,60,0.08);
            padding: 48px 36px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        h2 {
            font-size: 1.4rem;
            margin-bottom: 24px;
            font-weight: 700;
            color: #22223b;
        }
        .card {
            background: #f8fafc;
            padding: 18px;
            border-radius: 10px;
            margin: 12px 0;
            box-shadow: 0 2px 8px rgba(99,102,241,0.04);
        }
        .card a {
            text-decoration: none;
            color: #6366f1;
            font-weight: 600;
            font-size: 1.1rem;
            transition: color 0.2s;
        }
        .card a:hover {
            color: #4338ca;
        }
        form {
            margin-top: 24px;
        }
        button {
            padding: 12px 0;
            background-color: #e53e3e;
            color: #fff;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
            border: none;
            font-weight: 600;
            transition: background 0.2s;
            width: 100%;
        }
        button:hover {
            background-color: #b91c1c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Dashboard d'administrador</h2>
        <div class="card">
            <a href="/telescope">📊 Accedir a Telescope</a>
        </div>
        <div class="card">
            <a href="/">🏡 Tornar a la home</a>
        </div>
        <form method="GET" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit">Cerrar Sesión</button>
        </form>
    </div>
</body>
</html>
