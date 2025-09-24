<?php
session_start();
include('../conexao/conexao.php');

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $palavra_passe = $_POST['palavra_passe'];

    $consulta = "SELECT * FROM utilizadores WHERE email = '$email'";
    $resultado = mysqli_query($ligacao, $consulta);
    
    if ($resultado->num_rows === 1) {
        $utilizador = mysqli_fetch_assoc($resultado);

        if (password_verify($palavra_passe, $utilizador['password'])) {
            $_SESSION['id_utilizador'] = $utilizador['id_utilizador'];
            $_SESSION['nome'] = $utilizador['nome'];
            $_SESSION['tipo'] = $utilizador['tipo'];

            $id_utilizador = $utilizador['id_utilizador'];
            $hora_atual = date('Y-m-d H:i:s');

            $inserir_login = "INSERT INTO login (id_utilizador, tempo_inicio) VALUES ('$id_utilizador', '$hora_atual')";
            if (!mysqli_query($ligacao, $inserir_login)) {
                $msg = "<div class='alert alert-danger m-3'>Erro ao registar o login: " . mysqli_error($ligacao) . "</div>";
            } else {
                header('Location: ../../html/paineis/painel_utilizador.php');
                exit;
            }

        } else {
            $msg = "<div class='alert alert-danger m-3'>Palavra-passe incorreta.</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger m-3'>Utilizador não encontrado.</div>";
    }

    mysqli_close($ligacao);
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Validação de Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Validação de Login</h2>
    <?php if (!empty($msg)) echo $msg; ?>
    <?php if (!empty($msg)): ?>
        <a href="../../html/formularios/login.html" class="btn btn-secondary mt-3">Voltar ao login</a>
    <?php endif; ?>
</body>
</html>
