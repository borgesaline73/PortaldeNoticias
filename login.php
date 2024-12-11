<?php
$dsn = "pgsql:host=localhost;port=5432;dbname=PortalNoticias;";
$pdo = new PDO($dsn, "postgres", "1234");

$loginMessage = '';

if ($_POST) {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!empty($email) && !empty($senha)) {
        $sql = "SELECT * FROM usuario WHERE email = :email";
        $statement = $pdo->prepare($sql);
        $statement->execute(['email' => $email]);
        $usuario = $statement->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            header("Location: index.php");
        } else {
            $loginMessage = '<div class="alert alert-danger">Login ou senha inválidos.</div>';
        }
    } else {
        $loginMessage = '<div class="alert alert-danger">Preencha todos os campos.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #2a2a2a;
            color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #3b3b3b;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-container h2 {
            text-align: center;
            color: #f4b400;
            margin-bottom: 20px;
        }
        .form-label {
            color: #f4b400;
            font-weight: bold;
        }
        .btn {
            background-color: #f4b400;
            color: #3b3b3b;
            border: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn:hover {
            background-color: #d4a300;
        }
        .form-control {
            background-color: #4e4e4e;
            color: #f4f4f4;
            border: none;
            border-radius: 10px;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #f4b400;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <?php if (!empty($loginMessage)) echo $loginMessage; ?>
        <h2>Login</h2>
        <form action="" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" name="email" id="email" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" name="senha" id="senha" required>
            </div>
            <button type="submit" class="btn w-100">Entrar</button>
        </form>
    </div>
</body>
</html>