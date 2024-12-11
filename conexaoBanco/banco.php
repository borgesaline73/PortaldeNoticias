<?php
    $metodo = $_SERVER['REQUEST_METHOD'];
    $recurso = explode("/", substr(@$_SERVER['PATH_INFO'], 1));
    $dados = json_decode(file_get_contents("php://input"), true);
    
    $retorno = [];

    switch($metodo) {
        case 'GET': 
            $retorno = logar();
        break;
        case 'POST':
            if(isset($_POST['nome']) && isset($_POST['senha'])){
                $usuario = $_POST['nome'];
                $senha = $_POST['senha'];
                $retorno = logar($usuario, $senha);
            }else{
                $retorno = "DADOS INCORRETOS, FAVOR INSIRA NOVAMENTE";
            }
            break;
        // 'POST': 
//$tarefa = adicionar($dados); 
           // $retorno = $tarefa;
        //break;
        //case 'DELETE': 
           // $id = end($recurso);
           // $total = remover($id); 
           // $retorno['total'] = $total;
           // $retorno['sucesso'] = $total > 0;
       // break;
       // case 'PATCH': 
           // $id = end($recurso);
            //$tarefa = atualizar($id, $dados);
           // $retorno = $tarefa;
      //  break;
    }

    function logar($usuario = null, $senha = null) {

        if(is_null($usuario = null || $senha = null)){
            return "Usuario ou senha não inserido";
        }

        $pdo = getPDO();
        $sql = "select * from usuario where nome = :usuario AND
                senha = :senha";
        $banco = $pdo->prepare($sql);
        $banco->bindParam(':usuario', $usuario);
        $banco->bindParam(':senha', $senha);
        $banco->execute();
        $login = $banco->fetch(PDO::FETCH_ASSOC);
       
        if($login){
            return "Login bem sucedido!!" .$login['nome'];
        }else{
            return 'Usuario ou senha incorreto!!';
        }
    }

    header('Content-type: application/json');
    echo json_encode($retorno);
    die();

    function getPDO() {
        $host = 'localhost';
        $banco = 'Noticias';
        $dsn = "pgsql:host=$host;port=5432;dbname=$banco;";
        $pdo = new PDO($dsn, "postgres", "123456");
        return $pdo;
    }
?>