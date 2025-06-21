<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
            max-width: 350px;
            width: 100%;
            text-align: center;
        }
        .logo {
            width: 100px;
            margin-bottom: 20px;
            filter: drop-shadow(0 2px 8px #e0e7ff);
        }
        h2 {
            font-size: 1.4rem;
            margin-bottom: 18px;
            font-weight: 700;
            color: #22223b;
        }
        .error {
            color: #e53e3e;
            margin-bottom: 10px;
            font-size: 0.98rem;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: stretch;
        }
        label {
            font-weight: 500;
            text-align: left;
            color: #4a5568;
            margin-bottom: 2px;
        }
        input {
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            font-size: 1rem;
            background: #f8fafc;
            transition: border 0.2s;
        }
        input:focus {
            border-color: #6366f1;
            outline: none;
        }
        button {
            margin-top: 10px;
            padding: 12px 0;
            background-color: #6366f1;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="../images/logo.jpg" alt="Logo" class="logo">
        <h2>Accés administrador</h2>
        @if($errors->any())
            <p class="error">{{ $errors->first() }}</p>
        @endif
        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Contrasenya:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
