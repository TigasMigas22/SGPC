<?php
include('../conexao/conexao.php');

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $tipo = 'administrador';

    // Verificar duplicação
    $verificar = mysqli_query($ligacao, "SELECT * FROM utilizadores WHERE tipo = 'administrador'");
    if (mysqli_num_rows($verificar) > 0) {
        $msg = "<div class='alert alert-warning m-3'>Já existe um administrador registado.</div>";
        
    } else {
        $hash_password = password_hash($password, PASSWORD_DEFAULT);

        $inserir = "INSERT INTO utilizadores (nome, email, password, tipo)
                    VALUES ('$nome', '$email', '$hash_password', '$tipo')";

        if (mysqli_query($ligacao, $inserir)) {
            header('Location: ../../html/formularios/login.html');
            exit;
        } else {
            $msg = "<div class='alert alert-danger m-3'>Erro: " . mysqli_error($ligacao) . "</div>";
        }
    }

    mysqli_close($ligacao);
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Registar Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Registo do Primeiro Administrador</h2>
    <?php if (!empty($msg)) echo $msg; ?>
      <a href="../../html/formularios/registar_admin.html" class="btn btn-secondary mt-3">Voltar ao registo</a>
</body>
</html>
