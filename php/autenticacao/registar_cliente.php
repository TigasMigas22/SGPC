<?php
include("../../php/conexao/conexao.php");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql_verifica = "SELECT * FROM utilizadores WHERE email = ?";
    $stmt = mysqli_prepare($ligacao, $sql_verifica);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) > 0) {
        $msg = "<div class='alert alert-warning'>Email já registado!</div>";
    } else {
        $sql = "INSERT INTO utilizadores (nome, email, password, tipo) VALUES (?, ?, ?, 'cliente')";
        $stmt = mysqli_prepare($ligacao, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $nome, $email, $password);

        if (mysqli_stmt_execute($stmt)) {
            $msg = "<div class='alert alert-success'>Cliente registado com sucesso!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Erro ao registar cliente.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Registar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Registo de Cliente</h2>
    <?php if (!empty($msg)) echo $msg; ?>
    <?php if (!empty($msg)): ?>
        <a href='../../html/formularios/registar_cliente.html' class='btn btn-secondary mt-3'>Voltar ao registo</a>
    <?php endif; ?>
</body>
</html>
