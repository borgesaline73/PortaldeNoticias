<?php
$dsn = "pgsql:host=localhost;port=5432;dbname=PortalNoticias;";
$pdo = new PDO($dsn, "postgres", "1234");

try {

    //Aqui ele irá verificar se o parametro foi enviado via URL, metodo GET 
    if (isset($_GET['editar'])) {
        $id = $_GET['editar'];

        if(!is_numeric($id)){
            echo'id inválido';
        }

        //Prepara e executa os comandos sqls 
        $sql = "SELECT * FROM noticia WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $noticia = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$noticia) {
            echo"Notícia não encontrada.";
        }
    }

    // Processar o formulário de edição através do metodo POST
    if ($_POST) {
        $id = $_POST['id'] ?? '';

        if (empty($id) || !is_numeric($id)) {
            echo"ID inválido ou não enviado.";
        }

        //Recebe os valores e atribui as váriaveis 
        $manchete = $_POST['manchete'] ?? '';
        $noticiaTexto = $_POST['noticia'] ?? '';
        $data = $_POST['data'] ?? '';
        $imagem = !empty($_FILES['imagem']['tmp_name']) ? file_get_contents($_FILES['imagem']['tmp_name']) : null;

        //Verifica se tem ou não imagem, se sim inclui no campo sql
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $nome_original = $_FILES['imagem']['name'];
            $temp = explode(".", $nome_original);
            $extensao = end($temp);
            $arquivo_destino = uniqid() . '.' . $extensao;
            move_uploaded_file($_FILES['imagem']['tmp_name'], 'fotos/' . $arquivo_destino);

            // aqui ele faz a inserção caso seja inserido uma imagem
            $sql = "UPDATE noticia SET 
                        manchete = :manchete, 
                        textonoticia = :noticia, 
                        datapubli = :data, 
                        imagem = :imagem 
                    WHERE id = :id";

            $statement = $pdo->prepare($sql);
            $statement->bindParam(':imagem', $arquivo_destino, PDO::PARAM_STR);
        } else {
            // zaqui ele faz a inserção sem a imagem se ja tiver ou não
            $sql = "UPDATE noticia SET 
                        manchete = :manchete, 
                        textonoticia = :noticia, 
                        datapubli = :data 
                    WHERE id = :id";

            $statement = $pdo->prepare($sql);
        }

        $statement->bindParam(':manchete', $manchete, PDO::PARAM_STR);//parametros do php que valida a consulta sql ou seja a forma como deve ser tratado, strin, int...
        $statement->bindParam(':noticia', $noticiaTexto, PDO::PARAM_STR);
        $statement->bindParam(':data', $data, PDO::PARAM_STR);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

       
        $statement->execute();

        
        header("Location: cadastrar.php?mensagem=sucesso");
        exit();
    }
} catch (PDOException $e) {
    echo 'Erro ao editar a notícia: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Editar Notícias</title>
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
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #3b3b3b;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        h1 {
            color: #f4b400;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-label {
            color: #f4b400;
            font-weight: bold;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Notícia</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($noticia['id'] ?? ''); ?>">

            <div class="mb-3">
                <label for="manchete" class="form-label">Edite a sua manchete</label>
                <input type="text" name="manchete" id="manchete" class="form-control" 
                       value="<?php echo htmlspecialchars($noticia['manchete'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="noticia" class="form-label">Edite aqui a notícia completa</label>
                <textarea name="noticia" id="noticia" rows="4" class="form-control" required><?php echo htmlspecialchars($noticia['textonoticia'] ?? ''); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="data" class="form-label">Edite aqui a data da publicação</label>
                <input type="date" name="data" id="data" class="form-control" 
                       value="<?php echo isset($noticia['datapubli']) ? (new DateTime($noticia['datapubli']))->format('Y-m-d') : ''; ?>" required>
            </div>

            <div class="mb-3">
                <label for="imagem" class="form-label">Insira uma nova imagem (opcional)</label>
                <input type="file" name="imagem" id="imagem" class="form-control">
            </div>

            <button type="submit" class="btn w-100">Salvar</button>
        </form>
    </div>
</body>
</html>