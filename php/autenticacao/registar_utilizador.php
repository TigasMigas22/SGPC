<?php
session_start();
include('../../php/conexao/conexao.php');

$msg = "";

// Verifica se o utilizador está autenticado e é administrador
if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] != 'administrador') {
    $msg = "<div class='alert alert-danger m-3'>Acesso não autorizado.</div>";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipo'];

    // Verificar se o e-mail já existe
    $verifica = mysqli_query($ligacao, "SELECT id_utilizador FROM utilizadores WHERE email = '$email'");
    if (mysqli_num_rows($verifica) > 0) {
        $msg = "<div class='alert alert-warning m-3'>Erro: E-mail já registado.</div>";
    } else {
        // Inserir utilizador
        $sql = "INSERT INTO utilizadores (nome, email, password, tipo)
                VALUES ('$nome', '$email', '$password', '$tipo')";

        if (mysqli_query($ligacao, $sql)) {
            $id = mysqli_insert_id($ligacao);

            // Se for cliente, inscreve no programa de fidelidade automaticamente
            if ($tipo === 'cliente') {
                mysqli_query($ligacao, "
                    INSERT INTO cartao_fidelidade (id_utilizador, limite_gasto, premio)
                    VALUES ($id, 100, 'Lavagem grátis')
                ");
            }

            header('Location: ../../html/formularios/registar_utilizador.html');
            exit;
        } else {
            $msg = "<div class='alert alert-danger m-3'>Erro ao registar utilizador: " . mysqli_error($ligacao) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Registar Utilizador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Registo de Utilizador</h2>
    <?php if (!empty($msg)) echo $msg; ?>
</body>
</html>
