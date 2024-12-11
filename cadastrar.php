<?php
$dsn = "pgsql:host=localhost;port=5432;dbname=PortalNoticias;";
$pdo = new PDO($dsn, "postgres", "1234");

//Mensagem de retorno ao editar uma noticia
if (isset($_GET['mensagem']) && $_GET['mensagem'] == 'sucesso') {
    echo '<div class="alert alert-success">Notícia editada com sucesso!</div>';
}

if ($_POST) {
    $manchete = $_POST['manchete'] ?? '';
    $noticia = $_POST['noticia'] ?? '';
    $data = $_POST['data'] ?? '';

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $nome_original = $_FILES['imagem']['name'];
        $temp = explode(".", $nome_original);
        $extensao = end($temp);
        $arquivo_destino = uniqid();
        move_uploaded_file($_FILES['imagem']['tmp_name'], 'fotos/' . $arquivo_destino . '.' . $extensao);
        $imagem = $arquivo_destino . '.' . $extensao;
    } else {
        $imagem = null;
    }

    //Aqui ele verifica se os dados não estão vazios e insere um novo cadastro
    if (!empty($manchete) && !empty($noticia) && !empty($data)) {
        try {
            $sql = "INSERT INTO noticia (manchete, textoNoticia, dataPubli, imagem) 
                    VALUES (:manchete, :noticia, :data, :imagem) RETURNING id";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':manchete', $manchete, PDO::PARAM_STR);
            $statement->bindParam(':noticia', $noticia, PDO::PARAM_STR);
            $statement->bindParam(':data', $data, PDO::PARAM_STR);

            if ($imagem !== null) {
                $statement->bindParam(':imagem', $imagem, PDO::PARAM_LOB);
            } else {
                $statement->bindValue(':imagem', null, PDO::PARAM_NULL);
            }
            $statement->execute();

            echo '<div class="alert alert-success">Notícia inserida com sucesso!</div>';
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger">Erro ao inserir a notícia: ' . $e->getMessage() . '</div>';
        }
    } else {
        echo '<div class="alert alert-warning">Por favor, preencha todos os campos obrigatórios!</div>';
    }
}
  //Aqui ele efetua a exclusão da noticia via metodo GET
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $sql = "DELETE FROM noticia WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        echo '<div class="alert alert-success">Notícia excluída com sucesso!</div>';
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger">Erro ao excluir a notícia: ' . $e->getMessage() . '</div>';
    }
}

$sql = "SELECT id, manchete, datapubli, imagem FROM noticia";
$statement = $pdo->prepare($sql);
$statement->execute();
$noticias = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Gerenciar Notícias</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #2a2a2a;
            color: #f4f4f4;
            margin: 0;
            padding: 20px;
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
        .table {
            background-color: #3b3b3b;
            color: #f4f4f4;
            border-radius: 10px;
            overflow: hidden;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table img {
            border-radius: 10px;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-warning mb-4">Gerenciar Notícias</h1>
        <form action="" method="post" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label for="manchete" class="form-label">Insira aqui a sua manchete</label>
                <input type="text" name="manchete" id="manchete" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="noticia" class="form-label">Insira aqui a notícia completa</label>
                <textarea name="noticia" id="noticia" rows="4" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label for="data" class="form-label">Data da Publicação</label>
                <input type="date" name="data" id="data" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="imagem" class="form-label">Insira uma imagem (opcional)</label>
                <input type="file" name="imagem" id="imagem" class="form-control">
            </div>
            <button type="submit" class="btn w-100">Enviar</button>
        </form>

        <h3 class="text-warning">Minhas Notícias Cadastradas</h3>
        <table class="table table-striped table-dark">
            <thead>
                <tr>
                    <th>Manchete</th>
                    <th>Data de Publicação</th>
                    <th>Imagem</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($noticias as $noticia): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($noticia['manchete']); ?></td>
                        <td>
                            <?php if (isset($noticia['datapubli'])) {
                                $data = new DateTime($noticia['datapubli']);
                                echo $data->format('d/m/Y');
                            } ?>
                        </td>
                        <td>
                            <?php if (!empty($noticia['imagem'])) { ?>
                                <img src="fotos/<?php echo htmlspecialchars($noticia['imagem']); ?>" alt="Imagem da notícia" width="100" height="100">
                            <?php } else { ?>
                                <span>Sem imagem</span>
                            <?php } ?>
                        </td>
                        <td>
                            <a href="?delete=<?php echo $noticia['id']; ?>" class="btn btn-danger btn-sm">Excluir</a>
                            <a href="editar.php?editar=<?php echo $noticia['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 class="text-warning">Ir para Página de Notícias</h3>
        <a href="index.php" class="btn w-20">Ir</a>
    </div>
</body>
</html>